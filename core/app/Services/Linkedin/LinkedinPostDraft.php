<?php

namespace App\Services\Linkedin;

use App\Models\LinkedinPost;
use RuntimeException;

class LinkedinPostDraft
{
    public function create(string $context): LinkedinPost
    {
        $context = $this->clean($context);
        if ($context === '') {
            throw new RuntimeException('Write a short context for the post.');
        }

        $post = new LinkedinPost();
        $post->commentary = $this->commentary($context);
        $post->image = $this->image($context);
        $post->source = 'manual';
        $post->status = 'pending_approval';
        $post->scheduled_for = today();
        $post->save();

        return $post;
    }

    private function commentary(string $context): string
    {
        $lead = rtrim($context, '.');
        $text = $lead.".\n\n";
        $text .= 'Crownmaire Capital is a quantitative asset management firm for qualified investors. Programs are private and invitation-only, built on research, algorithmic execution, and multi-asset exposure across currencies, indices, commodities, futures, and select equities. ';
        $text .= 'Offices are in New York and Dubai. Participation starts at $20,000, with room for larger allocations.';
        $text .= "\n\n".$this->hashtags($text);

        return mb_substr($text, 0, 3000);
    }

    private function hashtags(string $text): string
    {
        $tags = ['CrownmaireCapital'];
        $themes = [
            'quantitative' => 'QuantitativeAssetManagement',
            'qualified' => 'QualifiedInvestors',
            'dubai' => 'Dubai',
            'new york' => 'NewYork',
            'multi-asset' => 'MultiAsset',
            'multi asset' => 'MultiAsset',
            'algorithm' => 'AlgorithmicInvesting',
            'family office' => 'FamilyOffice',
            'invitation-only' => 'PrivateInvestment',
            'private' => 'PrivateInvestment',
            'institutional' => 'InstitutionalInvesting',
            'risk' => 'RiskManagement',
            'portal' => 'InstitutionalReporting',
        ];
        $haystack = strtolower($text);
        foreach ($themes as $needle => $tag) {
            if (str_contains($haystack, $needle)) {
                $tags[] = $tag;
            }
        }

        $tags = array_slice(array_values(array_unique($tags)), 0, 8);

        return implode(' ', array_map(fn (string $tag) => '#'.$tag, $tags));
    }

    private function image(string $context): string
    {
        if (!function_exists('imagecreatetruecolor') || !function_exists('imagettftext')) {
            throw new RuntimeException('The server cannot draw the post image. Enable the PHP GD extension.');
        }

        $font = $this->font(false);
        $bold = $this->font(true);
        $width = 1080;
        $height = 1350;
        $image = imagecreatetruecolor($width, $height);
        if ($image === false) {
            throw new RuntimeException('The post image could not be created.');
        }

        for ($y = 0; $y < $height; $y++) {
            $shift = $y / $height;
            $color = imagecolorallocate(
                $image,
                (int) (3 * (1 - $shift)),
                (int) (7 + (37 - 7) * $shift),
                (int) (18 + (122 - 18) * $shift)
            );
            imageline($image, 0, $y, $width - 1, $y, $color);
        }

        $teal = imagecolorallocate($image, 27, 176, 206);
        $blue = imagecolorallocate($image, 0, 51, 173);
        $white = imagecolorallocate($image, 255, 255, 255);
        $muted = imagecolorallocate($image, 190, 205, 224);
        $footer = imagecolorallocate($image, 6, 12, 28);
        imagefilledrectangle($image, 0, 0, 8, $height, $teal);

        $this->placeLogo($image, 230, 64, 620);
        $this->centeredText($image, 18, 250, $muted, $font, 'Quantitative asset management');

        $lines = $this->wrap($bold, 42, $this->headline($context), 900);
        $y = 420;
        foreach ($lines as $line) {
            $this->centeredText($image, 42, $y, $white, $bold, $line);
            $y += 62;
        }

        $this->drawMotif($image, 390, 1040, $teal, $blue);
        imagefilledrectangle($image, 0, 1120, $width, $height, $footer);
        imagefilledrectangle($image, 0, 1120, $width, 1126, $teal);

        imagettftext($image, 16, 0, 56, 1184, $teal, $bold, 'NEW YORK');
        imagettftext($image, 18, 0, 56, 1220, $white, $font, '100 Wall Street Ct, NY 10005');
        imagettftext($image, 16, 0, 560, 1184, $teal, $bold, 'DUBAI');
        imagettftext($image, 18, 0, 560, 1220, $white, $font, '2402 Al-Manara Tower, Business Bay');
        $this->centeredText($image, 20, 1300, $white, $font, '+1 917 500 6476    ·    Info@crownmaire.com');

        $relative = $this->store($image);
        imagedestroy($image);

        return $relative;
    }

    private function placeLogo($image, int $x, int $y, int $width): void
    {
        $path = assetFilesystemPath('assets/images/logoIcon/logo.png');
        if (!is_file($path)) {
            throw new RuntimeException('The Crownmaire logo is missing from the server.');
        }

        $source = imagecreatefrompng($path);
        if ($source === false) {
            throw new RuntimeException('The Crownmaire logo could not be read.');
        }

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        $height = (int) max(1, round($width * $sourceHeight / $sourceWidth));
        $scaled = imagecreatetruecolor($width, $height);
        imagealphablending($scaled, false);
        imagesavealpha($scaled, true);
        $clear = imagecolorallocatealpha($scaled, 0, 0, 0, 127);
        imagefilledrectangle($scaled, 0, 0, $width, $height, $clear);
        imagecopyresampled($scaled, $source, 0, 0, 0, 0, $width, $height, $sourceWidth, $sourceHeight);

        for ($pixelY = 0; $pixelY < $height; $pixelY++) {
            for ($pixelX = 0; $pixelX < $width; $pixelX++) {
                $rgb = imagecolorat($scaled, $pixelX, $pixelY);
                $red = ($rgb >> 16) & 0xFF;
                $green = ($rgb >> 8) & 0xFF;
                $blue = $rgb & 0xFF;
                if ($red < 40 && $green < 40 && $blue < 40) {
                    imagesetpixel($scaled, $pixelX, $pixelY, $clear);
                }
            }
        }

        imagealphablending($image, true);
        imagecopy($image, $scaled, $x, $y, 0, 0, $width, $height);
        imagedestroy($source);
        imagedestroy($scaled);
    }

    private function centeredText($image, int $size, int $y, int $color, string $font, string $text): void
    {
        $box = imagettfbbox($size, 0, $font, $text);
        $textWidth = abs($box[2] - $box[0]);
        $x = (int) max(24, (1080 - $textWidth) / 2);
        imagettftext($image, $size, 0, $x, $y, $color, $font, $text);
    }

    private function drawMotif($image, int $baseX, int $baseY, int $teal, int $blue): void
    {
        $bars = [46, 88, 64, 124, 96, 150, 112];
        foreach ($bars as $index => $bar) {
            $x = $baseX + ($index * 42);
            imagefilledrectangle($image, $x, $baseY - $bar, $x + 24, $baseY, $index % 2 === 0 ? $teal : $blue);
        }
    }

    private function headline(string $context): string
    {
        $headline = rtrim($context, '.');
        if (mb_strlen($headline) > 160) {
            $headline = rtrim(mb_substr($headline, 0, 157)).'...';
        }

        return $headline;
    }

    private function wrap(string $font, int $size, string $text, int $maxWidth): array
    {
        $lines = [];
        $line = '';
        foreach (preg_split('/\s+/', $text) ?: [] as $word) {
            $attempt = trim($line.' '.$word);
            $box = imagettfbbox($size, 0, $font, $attempt);
            if (abs($box[2] - $box[0]) > $maxWidth && $line !== '') {
                $lines[] = $line;
                $line = $word;
            } else {
                $line = $attempt;
            }
        }
        if ($line !== '') {
            $lines[] = $line;
        }

        return array_slice($lines, 0, 6);
    }

    private function store($image): string
    {
        $directory = assetFilesystemPath('assets/images/linkedin');
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new RuntimeException('The post image folder could not be created.');
        }

        $filename = 'post-'.date('YmdHis').'-'.substr(bin2hex(random_bytes(4)), 0, 8).'.jpg';
        $absolute = $directory.DIRECTORY_SEPARATOR.$filename;
        if (!imagejpeg($image, $absolute, 88)) {
            throw new RuntimeException('The post image could not be saved.');
        }

        return 'assets/images/linkedin/'.$filename;
    }

    private function font(bool $bold): string
    {
        $candidates = $bold
            ? [
                base_path('resources/fonts/Roboto-Bold.ttf'),
                'C:\\Windows\\Fonts\\segoeuib.ttf',
                'C:\\Windows\\Fonts\\arialbd.ttf',
                '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
                '/usr/share/fonts/dejavu/DejaVuSans-Bold.ttf',
                '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
            ]
            : [
                base_path('resources/fonts/Roboto-Regular.ttf'),
                'C:\\Windows\\Fonts\\segoeui.ttf',
                'C:\\Windows\\Fonts\\arial.ttf',
                '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
                '/usr/share/fonts/dejavu/DejaVuSans.ttf',
                '/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf',
            ];

        foreach ($candidates as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        throw new RuntimeException('The server has no font available to draw the post image.');
    }

    private function clean(string $value): string
    {
        $value = trim(preg_replace('/\s+/', ' ', $value) ?? '');

        return $value;
    }
}
