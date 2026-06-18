<?php

namespace Database\Seeders;

use App\Models\Frontend;
use App\Support\LegalPagesContent;
use Illuminate\Database\Seeder;

class CrownmaireLegalPagesSeeder extends Seeder
{
    public function run(): void
    {
        $pages = Frontend::where('data_keys', 'policy_pages.element')->get();

        foreach ($pages as $page) {
            $title = strtolower((string) ($page->data_values->title ?? ''));

            if (str_contains($title, 'privacy')) {
                $page->data_values = [
                    'title'   => 'Privacy Policy',
                    'details' => LegalPagesContent::privacyPolicyHtml(),
                ];
            } elseif (str_contains($title, 'term')) {
                $page->data_values = [
                    'title'   => 'Terms and Conditions',
                    'details' => LegalPagesContent::termsAndConditionsHtml(),
                ];
            }

            $page->save();
        }
    }
}
