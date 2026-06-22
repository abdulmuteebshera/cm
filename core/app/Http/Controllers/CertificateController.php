<?php

namespace App\Http\Controllers;

use App\Models\Certificate;

class CertificateController extends Controller
{
    /**
     * Publicly viewable / shareable certificate render page.
     */
    public function show($uid)
    {
        $certificate = Certificate::with(['user', 'plan'])->where('uid', $uid)->firstOrFail();

        $pageTitle = $certificate->title();

        $holderName = trim((string) optional($certificate->user)->fullname);
        if ($holderName === '') {
            $holderName = optional($certificate->user)->username ?? 'Valued Member';
        }

        return view($this->activeTemplate . 'certificate', compact('certificate', 'pageTitle', 'holderName'));
    }
}
