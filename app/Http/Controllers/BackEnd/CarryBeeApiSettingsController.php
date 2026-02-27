<?php

declare(strict_types=1);

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCarryBeeApiSettingsRequest;
use App\Models\CarryBeeApi;
use Illuminate\Http\RedirectResponse;

class CarryBeeApiSettingsController extends Controller
{
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
        return back()->with(
            'success',
            'CarryBee now uses Client-ID, Client-Secret and Client-Context headers. Access tokens are no longer required.'
        );
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
        $settings->client_id = '';
        $settings->client_secret = '';
        $settings->client_context = '';
        $settings->save();

        return $settings;
    }
}
