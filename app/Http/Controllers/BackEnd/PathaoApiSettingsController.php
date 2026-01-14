<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePathaoApiSettingsRequest;
use App\Models\PathaoApi;
use Illuminate\Support\Facades\Http;

class PathaoApiSettingsController extends Controller
{
    private const ISSUE_TOKEN_URL = 'https://api-hermes.pathao.com/aladdin/api/v1/issue-token';

    public function index()
    {
        $data = $this->settings();

        return view('backEnd.admin.pathao_api_settings', compact('data'));
    }

    public function update(UpdatePathaoApiSettingsRequest $request)
    {
        try {
            $this->settings()->update($request->validated());

            return back()->with('success', 'Pathao API Settings Updated Successfully');
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Something went wrong while updating Pathao API settings.');
        }
    }

    public function generateAccessToken()
    {
        try {
            $settings = $this->settings();
            $payload = $this->buildIssueTokenPayload($settings);

            if ($payload === null) {
                return back()->with('error', 'Please configure your Pathao API credentials first.');
            }

            $response = Http::acceptJson()
                ->asJson()
                ->post(self::ISSUE_TOKEN_URL, $payload);

            if (! $response->ok()) {
                return back()->with('error', 'Failed to generate access token from Pathao.');
            }

            $data = $response->json();
            $accessToken = $data['access_token'] ?? null;
            $refreshToken = $data['refresh_token'] ?? null;

            if (! is_string($accessToken) || $accessToken === '' || ! is_string($refreshToken) || $refreshToken === '') {
                return back()->with('error', 'Pathao token response was invalid.');
            }

            $settings->update([
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
            ]);

            return back()->with('success', 'New Access Token Generated Successfully');
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Something went wrong while generating a new access token.');
        }
    }

    private function settings(): PathaoApi
    {
        $settings = PathaoApi::query()->find(1);

        if ($settings) {
            return $settings;
        }

        $settings = new PathaoApi;
        $settings->id = 1;
        $settings->is_active = false;
        $settings->access_token = '';
        $settings->refresh_token = '';
        $settings->client_id = '';
        $settings->client_secret = '';
        $settings->username = '';
        $settings->password = '';
        $settings->store_id = '';
        $settings->save();

        return $settings;
    }

    private function buildIssueTokenPayload(PathaoApi $settings): ?array
    {
        if (
            blank($settings->client_id)
            || blank($settings->client_secret)
            || blank($settings->username)
            || blank($settings->password)
        ) {
            return null;
        }

        return [
            'client_id' => $settings->client_id,
            'client_secret' => $settings->client_secret,
            'username' => $settings->username,
            'password' => $settings->password,
            'grant_type' => 'password',
        ];
    }
}
