<?php

namespace App\Services\Linkedin;

use App\Models\LinkedinSetting;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class LinkedinClient
{
    private const VERSION = '202601';

    public function authorizationUrl(LinkedinSetting $settings, string $state): string
    {
        if (!$settings->client_id || !$settings->client_secret) {
            throw new RuntimeException('Save the LinkedIn Client ID and Client Secret first.');
        }

        $query = http_build_query([
            'response_type' => 'code',
            'client_id'     => $settings->client_id,
            'redirect_uri'  => route('admin.linkedin.callback'),
            'state'         => $state,
            'scope'         => 'openid profile email w_member_social',
        ], '', '&', PHP_QUERY_RFC3986);

        return 'https://www.linkedin.com/oauth/v2/authorization?'.$query;
    }

    public function exchangeCode(LinkedinSetting $settings, string $code): void
    {
        $response = Http::asForm()->timeout(30)->post('https://www.linkedin.com/oauth/v2/accessToken', [
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'redirect_uri'  => route('admin.linkedin.callback'),
            'client_id'     => $settings->client_id,
            'client_secret' => $settings->client_secret,
        ]);

        $this->storeToken($settings, $response->json() ?? [], $response->status(), $response->body());
        $this->storeProfile($settings);
    }

    public function publish(LinkedinSetting $settings, string $commentary, ?string $imageRelativePath): string
    {
        $token = $this->token($settings);
        $author = 'urn:li:person:'.$settings->member_id;
        $imageUrn = null;

        if ($imageRelativePath) {
            $imageUrn = $this->uploadImage($settings, $token, $author, $imageRelativePath);
        }

        $payload = [
            'author'       => $author,
            'commentary'   => $this->escapeCommentary($commentary),
            'visibility'   => 'PUBLIC',
            'distribution' => [
                'feedDistribution'               => 'MAIN_FEED',
                'targetEntities'                 => [],
                'thirdPartyDistributionChannels' => [],
            ],
            'lifecycleState'            => 'PUBLISHED',
            'isReshareDisabledByAuthor' => false,
        ];

        if ($imageUrn) {
            $payload['content'] = [
                'media' => [
                    'id' => $imageUrn,
                ],
            ];
        }

        $response = Http::withToken($token)
            ->withHeaders($this->apiHeaders())
            ->timeout(30)
            ->post('https://api.linkedin.com/rest/posts', $payload);

        if (!$response->successful()) {
            throw new RuntimeException($this->apiError('LinkedIn did not publish the post', $response->json() ?? [], $response->body()));
        }

        $settings->last_error = null;
        $settings->save();

        return (string) ($response->header('x-restli-id') ?: $response->header('X-RestLi-Id') ?: '');
    }

    private function uploadImage(LinkedinSetting $settings, string $token, string $author, string $relativePath): string
    {
        $absolute = assetFilesystemPath($relativePath);
        if (!is_file($absolute)) {
            throw new RuntimeException('The post picture is missing from the server. Upload it again.');
        }

        $init = Http::withToken($token)
            ->withHeaders($this->apiHeaders())
            ->timeout(30)
            ->post('https://api.linkedin.com/rest/images?action=initializeUpload', [
                'initializeUploadRequest' => [
                    'owner' => $author,
                ],
            ]);

        if (!$init->successful()) {
            throw new RuntimeException($this->apiError('LinkedIn did not accept the picture', $init->json() ?? [], $init->body()));
        }

        $value = $init->json('value') ?? [];
        $uploadUrl = $value['uploadUrl'] ?? null;
        $imageUrn = $value['image'] ?? null;
        if (!$uploadUrl || !$imageUrn) {
            throw new RuntimeException('LinkedIn did not return an upload link for the picture.');
        }

        $bytes = file_get_contents($absolute);
        $mime = str_ends_with(strtolower($absolute), '.png') ? 'image/png' : 'image/jpeg';

        $upload = Http::withToken($token)
            ->withBody($bytes, $mime)
            ->timeout(60)
            ->put($uploadUrl);

        if (!$upload->successful()) {
            throw new RuntimeException('LinkedIn rejected the picture upload.');
        }

        $settings->last_error = null;

        return $imageUrn;
    }

    private function token(LinkedinSetting $settings): string
    {
        if (!$settings->access_token || !$settings->member_id) {
            throw new RuntimeException('Connect the LinkedIn account before publishing.');
        }

        if ($settings->token_expires_at && $settings->token_expires_at->lt(now()->addMinutes(5))) {
            $this->refresh($settings);
            $settings->refresh();
        }

        return (string) $settings->access_token;
    }

    private function refresh(LinkedinSetting $settings): void
    {
        if (!$settings->refresh_token) {
            $settings->last_error = 'The LinkedIn connection expired. Connect the account again.';
            $settings->save();
            throw new RuntimeException($settings->last_error);
        }

        $response = Http::asForm()->timeout(30)->post('https://www.linkedin.com/oauth/v2/accessToken', [
            'grant_type'    => 'refresh_token',
            'refresh_token' => $settings->refresh_token,
            'client_id'     => $settings->client_id,
            'client_secret' => $settings->client_secret,
        ]);

        $this->storeToken($settings, $response->json() ?? [], $response->status(), $response->body());
    }

    private function storeToken(LinkedinSetting $settings, array $payload, int $status, string $body): void
    {
        if ($status >= 400 || empty($payload['access_token'])) {
            $message = $payload['error_description'] ?? $payload['error'] ?? 'LinkedIn did not return an access token.';
            $settings->last_error = mb_substr((string) $message, 0, 500);
            $settings->save();
            throw new RuntimeException($settings->last_error !== '' ? $settings->last_error : 'LinkedIn authorization failed.');
        }

        $settings->access_token = $payload['access_token'];
        if (!empty($payload['refresh_token'])) {
            $settings->refresh_token = $payload['refresh_token'];
        }
        $expires = (int) ($payload['expires_in'] ?? 0);
        $settings->token_expires_at = $expires > 0 ? now()->addSeconds(max(60, $expires - 120)) : null;
        $settings->last_error = null;
        $settings->save();
    }

    private function storeProfile(LinkedinSetting $settings): void
    {
        $response = Http::withToken($settings->access_token)
            ->timeout(30)
            ->get('https://api.linkedin.com/v2/userinfo');

        if (!$response->successful()) {
            throw new RuntimeException('LinkedIn authorized the app, but the profile could not be read. Connect again.');
        }

        $profile = $response->json() ?? [];
        $sub = $profile['sub'] ?? null;
        if (!$sub) {
            throw new RuntimeException('LinkedIn did not return the member id for this account.');
        }

        $settings->member_id = $sub;
        $settings->member_name = $profile['name'] ?? trim(($profile['given_name'] ?? '').' '.($profile['family_name'] ?? ''));
        $settings->member_email = $profile['email'] ?? null;
        $settings->connected_at = now();
        $settings->last_error = null;
        $settings->save();
    }

    private function apiHeaders(): array
    {
        return [
            'LinkedIn-Version'          => self::VERSION,
            'X-Restli-Protocol-Version' => '2.0.0',
        ];
    }

    private function escapeCommentary(string $text): string
    {
        $text = str_replace('\\', '\\\\', $text);

        return preg_replace('/([()\[\]{}@|~*_<>])/u', '\\\\$1', $text) ?? $text;
    }

    private function apiError(string $prefix, array $json, string $body): string
    {
        $message = $json['message'] ?? $json['error_description'] ?? null;
        if (!$message && $body !== '') {
            $message = mb_substr($body, 0, 300);
        }

        return $prefix.($message ? ': '.$message : '.');
    }
}
