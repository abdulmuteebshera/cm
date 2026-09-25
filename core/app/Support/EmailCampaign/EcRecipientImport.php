<?php

namespace App\Support\EmailCampaign;

use Illuminate\Http\UploadedFile;

class EcRecipientImport
{
    /**
     * @return array<int, array{email: string, name: ?string, merge_data: array}>
     */
    public function parseUploadedFile(UploadedFile $file): array
    {
        $ext = strtolower($file->getClientOriginalExtension());

        if (in_array($ext, ['csv', 'txt'], true)) {
            return $this->parseCsv($file->getRealPath());
        }

        if (in_array($ext, ['xlsx', 'xls'], true)) {
            return $this->parseSpreadsheet($file->getRealPath(), $ext);
        }

        throw new \InvalidArgumentException('Upload CSV or Excel (.xlsx, .xls).');
    }

    /**
     * @return array<int, array{email: string, name: ?string, merge_data: array}>
     */
    public function parseCsv(string $path): array
    {
        $rows = [];
        $handle = fopen($path, 'rb');
        if (!$handle) {
            return [];
        }

        $header = null;
        while (($data = fgetcsv($handle)) !== false) {
            if ($header === null) {
                $header = array_map(fn ($h) => strtolower(trim((string) $h)), $data);
                continue;
            }
            if (count(array_filter($data, fn ($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }
            $row = $this->mapRow($header, $data);
            if ($row) {
                $rows[] = $row;
            }
        }
        fclose($handle);

        return $rows;
    }

    /**
     * @return array<int, array{email: string, name: ?string, merge_data: array}>
     */
    protected function parseSpreadsheet(string $path, string $ext): array
    {
        if (!class_exists(\ZipArchive::class)) {
            throw new \RuntimeException('Excel import requires Zip extension. Save as CSV instead.');
        }

        if ($ext === 'xlsx') {
            return $this->parseXlsx($path);
        }

        throw new \InvalidArgumentException('Legacy .xls is not supported. Save the sheet as .xlsx or CSV.');
    }

    /**
     * @return array<int, array{email: string, name: ?string, merge_data: array}>
     */
    protected function parseXlsx(string $path): array
    {
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            throw new \InvalidArgumentException('Could not read Excel file.');
        }

        $sharedStrings = [];
        $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedXml) {
            $sx = @simplexml_load_string($sharedXml);
            if ($sx && isset($sx->si)) {
                foreach ($sx->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string) $si->t;
                    } elseif (isset($si->r)) {
                        $text = '';
                        foreach ($si->r as $r) {
                            $text .= (string) $r->t;
                        }
                        $sharedStrings[] = $text;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if (!$sheetXml) {
            throw new \InvalidArgumentException('Excel sheet1 not found.');
        }

        $sheet = @simplexml_load_string($sheetXml);
        if (!$sheet || !isset($sheet->sheetData->row)) {
            return [];
        }

        $grid = [];
        foreach ($sheet->sheetData->row as $row) {
            foreach ($row->c as $cell) {
                $ref = (string) $cell['r'];
                if (!preg_match('/^([A-Z]+)(\d+)$/', $ref, $m)) {
                    continue;
                }
                $col = $this->columnIndex($m[1]);
                $rowNum = (int) $m[2];
                $type = (string) ($cell['t'] ?? '');
                $value = isset($cell->v) ? (string) $cell->v : '';
                if ($type === 's' && $value !== '' && isset($sharedStrings[(int) $value])) {
                    $value = $sharedStrings[(int) $value];
                }
                $grid[$rowNum][$col] = $value;
            }
        }

        if ($grid === []) {
            return [];
        }

        ksort($grid);
        $firstRowNum = array_key_first($grid);
        $headerRow = $grid[$firstRowNum];
        ksort($headerRow);
        $header = array_map(fn ($h) => strtolower(trim((string) $h)), array_values($headerRow));

        $rows = [];
        foreach ($grid as $rowNum => $cells) {
            if ($rowNum === $firstRowNum) {
                continue;
            }
            ksort($cells);
            $values = array_values($cells);
            while (count($values) < count($header)) {
                $values[] = '';
            }
            $mapped = $this->mapRow($header, $values);
            if ($mapped) {
                $rows[] = $mapped;
            }
        }

        return $rows;
    }

    protected function columnIndex(string $letters): int
    {
        $n = 0;
        foreach (str_split($letters) as $c) {
            $n = $n * 26 + (ord($c) - 64);
        }

        return $n - 1;
    }

    /**
     * @param array<int, string> $header
     * @param array<int, string> $data
     * @return array{email: string, name: ?string, merge_data: array}|null
     */
    protected function mapRow(array $header, array $data): ?array
    {
        $assoc = [];
        foreach ($header as $i => $key) {
            $assoc[$key] = trim((string) ($data[$i] ?? ''));
        }

        $email = $assoc['email'] ?? $assoc['e-mail'] ?? $assoc['mail'] ?? '';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        $name = $assoc['name'] ?? $assoc['full name'] ?? $assoc['fullname'] ?? null;
        unset($assoc['email'], $assoc['e-mail'], $assoc['mail'], $assoc['name'], $assoc['full name'], $assoc['fullname']);

        return [
            'email'      => strtolower($email),
            'name'       => $name ?: null,
            'merge_data' => $assoc,
        ];
    }
}
