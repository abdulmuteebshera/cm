<?php

namespace App\Services\Linkedin;

use App\Models\LinkedinPost;
use App\Support\Seo\SeoSite;
use RuntimeException;

class LinkedinPostDraft
{
    public function create(string $context, ?string $keywords = null): LinkedinPost
    {
        $context = $this->clean($context);
        if ($context === '') {
            throw new RuntimeException('Write a short context for the post.');
        }

        $keywords = $this->clean((string) $keywords);
        $post = new LinkedinPost();
        $post->commentary = $this->commentary($context, $keywords);
        $post->image = $this->image($context, $keywords);
        $post->source = 'manual';
        $post->status = 'pending_approval';
        $post->scheduled_for = today();
        $post->save();

        return $post;
    }

    private function commentary(string $context, string $keywords): string
    {
        $lead = rtrim($context, '.');
        $text = $lead.".\n\n";
        $text .= 'Crownmaire Capital is a quantitative asset management firm for qualified investors. Programs are private and invitation-only, built on research, algorithmic execution, and multi-asset exposure across currencies, indices, commodities, futures, and select equities. ';
        $text .= 'Offices are in New York and Dubai. Participation starts at $20,000, with room for larger allocations.';
        $text .= "\n\n".$this->hashtags($context, $keywords);

        return mb_substr($text, 0, 3000);
    }

    private function hashtags(string $context, string $keywords): string
    {
        $tags = ['CrownmaireCapital', 'QuantitativeAssetManagement', 'QualifiedInvestors'];
        $themes = [
            'dubai' => 'Dubai',
            'new york' => 'NewYork',
            'multi-asset' => 'MultiAsset',
            'multi asset' => 'MultiAsset',
            'algorithm' => 'AlgorithmicInvesting',
            'family office' => 'FamilyOffice',
            'private' => 'PrivateInvestment',
            'institutional' => 'InstitutionalInvesting',
            'risk' => 'RiskManagement',
            'portal' => 'InstitutionalReporting',
            'quant' => 'QuantitativeTrading',
        ];
        $haystack = strtolower($context.' '.$keywords);
        foreach ($themes as $needle => $tag) {
            if (str_contains($haystack, $needle)) {
                $tags[] = $tag;
            }
        }
        foreach (preg_split('/[,;]+/', $keywords) ?: [] as $word) {
            $tag = $this->tag((string) $word);
            if ($tag !== '') {
                $tags[] = $tag;
            }
        }
        foreach (['NewYork', 'Dubai', 'MultiAsset'] as $tag) {
            $tags[] = $tag;
        }
        $tags = array_slice(array_values(array_unique($tags)), 0, 8);

        return implode(' ', array_map(fn (string $tag) => '#'.$tag, $tags));
    }

    private function tag(string $word): string
    {
        $word = trim($word);
        if ($word === '') {
            return '';
        }
        $parts = preg_split('/\s+/', strtolower($word)) ?: [];
        $tag = '';
        foreach ($parts as $part) {
            $part = preg_replace('/[^a-z0-9]/', '', $part) ?? '';
            if ($part !== '') {
                $tag .= ucfirst($part);
            }
        }

        return strlen($tag) >= 3 ? $tag : '';
    }

    private function image(string $context, string $keywords): string
    {
        if (!function_exists('imagecreatetruecolor') || !function_exists('imagettftext')) {
            throw new RuntimeException('The server cannot draw the post image. Enable the PHP GD extension.');
        }

        $font = $this->font(false);
        $bold = $this->font(true);
        $width = 1200;
        $height = 627;
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
        imagefilledrectangle($image, 0, 0, 8, $height, $teal);

        imagettftext($image, 18, 0, 72, 92, $teal, $bold, 'CROWNMAIRE CAPITAL');
        imagettftext($image, 16, 0, 72, 128, $muted, $font, 'Quantitative asset management  ·  New York  ·  Dubai');

        $lines = $this->wrap($bold, 36, $this->headline($context), 680);
        $y = 230;
        foreach ($lines as $line) {
            imagettftext($image, 36, 0, 72, $y, $white, $bold, $line);
            $y += 52;
        }

        $pills = array_slice($this->pillWords($context, $keywords), 0, 4);
        $x = 72;
        foreach ($pills as $pill) {
            $box = imagettfbbox(14, 0, $font, $pill);
            $pillWidth = abs($box[2] - $box[0]) + 28;
            imagefilledrectangle($image, $x, 500, $x + $pillWidth, 540, $blue);
            imagettftext($image, 14, 0, $x + 14, 527, $white, $font, $pill);
            $x += $pillWidth + 12;
        }

        $this->drawMotif($image, $context, $teal, $blue, $white);
        $relative = $this->store($image);
        imagedestroy($image);

        return $relative;
    }

    private function drawMotif($image, string $context, int $teal, int $blue, int $white): void
    {
        $haystack = strtolower($context);
        $bars = str_contains($haystack, 'dubai') || str_contains($haystack, 'new york')
            ? [70, 120, 90, 160, 110, 190, 140]
            : [60, 100, 80, 140, 180, 150, 210];
        $baseX = 860;
        $baseY = 470;
        foreach ($bars as $index => $bar) {
            $x = $baseX + ($index * 42);
            $color = $index % 2 === 0 ? $teal : $blue;
            imagefilledrectangle($image, $x, $baseY - $bar, $x + 26, $baseY, $color);
        }
        imagefilledellipse($image, 1040, 180, 150, 150, $teal);
        imagefilledellipse($image, 1040, 180, 90, 90, imagecolorallocate($image, 3, 12, 28));
        imageline($image, 900, 390, 1120, 250, $white);
    }

    private function pillWords(string $context, string $keywords): array
    {
        $words = ['Qualified investors', 'Multi-asset'];
        $haystack = strtolower($context.' '.$keywords);
        if (str_contains($haystack, 'dubai')) {
            $words[] = 'Dubai';
        }
        if (str_contains($haystack, 'new york')) {
            $words[] = 'New York';
        }
        if (str_contains($haystack, 'family')) {
            $words[] = 'Family office';
        }
        foreach (SeoSite::defaultKeywords() as $keyword) {
            if (str_contains($haystack, strtolower($keyword))) {
                $words[] = $keyword;
            }
        }
        foreach (preg_split('/[,;]+/', $keywords) ?: [] as $word) {
            $word = trim($word);
            if ($word !== '') {
                $words[] = mb_substr($word, 0, 28);
            }
        }

        return array_values(array_unique($words));
    }

    private function headline(string $context): string
    {
        $headline = rtrim($context, '.');
        if (mb_strlen($headline) > 110) {
            $headline = rtrim(mb_substr($headline, 0, 107)).'...';
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

        return array_slice($lines, 0, 4);
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
                'C:\\Windows\\Fonts\\segoeuib.ttf',
                'C:\\Windows\\Fonts\\arialbd.ttf',
                '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
                '/usr/share/fonts/dejavu/DejaVuSans-Bold.ttf',
                '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
            ]
            : [
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
