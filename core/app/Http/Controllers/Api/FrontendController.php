<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Frontend;
use App\Models\GeneralSetting;
use App\Models\Language;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function logoFavicon()
    {
        $logoPath = getFilePath('logoIcon');
        $data = [
            'logo'      => getImage($logoPath . '/logo.png'),
            'auth_logo' => getImage($logoPath . '/logo_2.png'),
            'favicon'   => getImage($logoPath . '/favicon.png'),
        ];
        return getResponse('logo_favicon', 'success', 'Logo & Favicon', $data);
    }

    public function branding()
    {
        $general     = GeneralSetting::first();
        $authContent = getContent('authentication.content', true);
        $logoPath    = getFilePath('logoIcon');
        $template    = activeTemplateName();

        $policies = Frontend::where('template_name', $template)
            ->where('data_keys', 'policy_pages.element')
            ->get();

        $privacy = $policies->first(fn ($p) => stripos((string) ($p->data_values->title ?? ''), 'privacy') !== false);
        $terms   = $policies->first(fn ($p) => stripos((string) ($p->data_values->title ?? ''), 'term') !== false);

        $data = [
            'site_name'    => $general->site_name ?? 'Crownmaire Capital',
            'cur_text'     => $general->cur_text,
            'cur_sym'      => $general->cur_sym,
            'base_color'   => $general->base_color ?? '1989BE',
            'auth_logo'    => getImage($logoPath . '/logo_2.png'),
            'logo'         => getImage($logoPath . '/logo.png'),
            'login_title'  => __(@$authContent->data_values->login_title ?? 'Login Account'),
            'login_subtitle' => __(@$authContent->data_values->login_subtitle ?? ''),
            'privacy_policy_url' => $privacy
                ? route('policy.pages', [slug($privacy->data_values->title), $privacy->id])
                : null,
            'terms_url' => $terms
                ? route('policy.pages', [slug($terms->data_values->title), $terms->id])
                : null,
        ];

        return getResponse('branding', 'success', 'Portal branding', $data);
    }

    public function language($code)
    {
        $language = Language::where('code', $code)->first();

        if (!$language) {
            return getResponse('not_found', 'error', ['Language not found']);
        }

        $languages = Language::get();

        $path        = base_path() . "/resources/lang/$code.json";
        $fileContent = file_get_contents($path);

        $data = [
            'languages' => $languages,
            'file'      => $fileContent,
        ];

        return getResponse('language', 'success', 'Language Details', $data);
    }

    public function generalSetting()
    {
        $general = GeneralSetting::first();
        return getResponse('general_setting', 'success', 'General setting data', ['general_setting' => $general]);
    }

    public function policy(Request $request)
    {
        $policy = Frontend::where('template_name', $request->template)->where('data_keys', 'policy_pages.element')->get();
        return getResponse('policy_page', 'success', 'Policy & Terms and condition page', ['policy' => $policy]);
    }
    
    public function faq(Request $request)
    {
        $faqs = Frontend::where('template_name', $request->template)->where('data_keys', 'faq.element')->get();
        return getResponse('faq', 'success', 'Faq List', ['faqs' => $faqs]);
    }

}
