<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushSubscriptionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'endpoint' => ['required', 'url'],
            'keys.auth' => ['required', 'string'],
            'keys.p256dh' => ['required', 'string'],
        ]);

        $user = $this->resolveAuthenticatedUser();

        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $user->updatePushSubscription(
            $request->input('endpoint'),
            $request->input('keys.p256dh'),
            $request->input('keys.auth'),
            $request->input('content_encoding', 'aesgcm')
        );

        return response()->json(['success' => true]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $request->validate([
            'endpoint' => ['required', 'url'],
        ]);

        $user = $this->resolveAuthenticatedUser();

        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $user->deletePushSubscription($request->input('endpoint'));

        return response()->json(['success' => true]);
    }

    /**
     * Resolve the currently authenticated user across all staff guards.
     */
    private function resolveAuthenticatedUser(): mixed
    {
        foreach (['admin', 'manager', 'employee'] as $guard) {
            if (Auth::guard($guard)->check()) {
                return Auth::guard($guard)->user();
            }
        }

        return null;
    }
}
