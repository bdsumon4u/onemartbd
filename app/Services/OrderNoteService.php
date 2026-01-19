<?php

namespace App\Services;

use App\Models\NoteHistory;

class OrderNoteService
{
    public function __construct(
        protected ActingUserContextResolver $actingUserContextResolver,
    ) {}

    public function addNoteHistory(int $orderId, string $text): bool
    {
        [$user, $createdBy] = $this->actingUserContextResolver->resolve();

        if (! $user || ! $createdBy) {
            return false;
        }

        NoteHistory::create([
            'order_id' => $orderId,
            'user_id' => (int) $user->id,
            'user_type' => $createdBy,
            'text' => $text,
        ]);

        return true;
    }
}
