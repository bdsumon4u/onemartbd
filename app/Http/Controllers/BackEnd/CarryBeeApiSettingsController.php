<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCarryBeeApiSettingsRequest;
use App\Models\CarryBeeApi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;

class CarryBeeApiSettingsController extends Controller
{
    private const LOGIN_URL = 'https://developers.carrybee.com/api/login';

    public function index()
    {
        $data = $this->settings();

        return view('backEnd.admin.carrybee_api_settings', compact('data'));
    }

    public function update(UpdateCarryBeeApiSettingsRequest $request): RedirectResponse
    {
        try {
            $this->settings()->update($request->validated());

            return back()->with('success', 'CarryBee API Settings Updated Successfully');
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Something went wrong while updating CarryBee API settings.');
        }
    }

    public function generateAccessToken(): RedirectResponse
    {
        try {
            $settings = $this->settings();
            $payload = $this->buildLoginPayload($settings);

            if ($payload === null) {
                return back()->with('error', 'Please configure your CarryBee API credentials first.');
            }

            $response = Http::acceptJson()
                ->asJson()
                ->post(self::LOGIN_URL, $payload);

            if (! $response->ok()) {
                return back()->with('error', 'Failed to generate access token from CarryBee.');
            }

            $data = $response->json();
            $token = $data['data']['token'] ?? null;

            if (! is_string($token) || $token === '') {
                return back()->with('error', 'CarryBee token response was invalid.');
            }

            $settings->update([
                'access_token' => $token,
            ]);

            return back()->with('success', 'New Access Token Generated Successfully');
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Something went wrong while generating a new access token.');
        }
    }

    private function settings(): CarryBeeApi
    {
        $settings = CarryBeeApi::query()->find(1);

        if ($settings) {
            return $settings;
        }

        $settings = new CarryBeeApi;
        $settings->id = 1;
        $settings->is_active = false;
        $settings->store_id = '';
        $settings->email = '';
        $settings->password = '';
        $settings->access_token = '';
        $settings->save();

        return $settings;
    }

    private function buildLoginPayload(CarryBeeApi $settings): ?array
    {
        if (blank($settings->email) || blank($settings->password)) {
            return null;
        }

        return [
            'email' => $settings->email,
            'password' => $settings->password,
        ];
    }
}
