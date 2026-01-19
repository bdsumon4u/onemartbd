<?php

namespace App\Services;

use Illuminate\Contracts\Auth\Authenticatable;

class OrderTransactionService
{
    public function __construct(
        protected ActingUserContextResolver $actingUserContextResolver,
    ) {}

    /**
     * @param  array<string, string|int|float|null>  $replacements
     */
    public function logFromTemplate(
        int $orderId,
        string $templateKey,
        array $replacements,
        ?int $employeeId = null,
        string $type = 'local',
    ): bool {
        [$user, $createdBy] = $this->actingUserContextResolver->resolve();

        if (! $user || ! $createdBy) {
            return false;
        }

        $replacements = array_merge([
            '{user_name}' => (string) $user->name,
            '{role}' => $createdBy,
        ], $replacements);

        $text = strtr(config($templateKey), array_map(static fn ($value) => (string) $value, $replacements));

        order_transaction(
            $type,
            $orderId,
            $text,
            null,
            $createdBy,
            (int) $user->id,
            $employeeId
        );

        return true;
    }

    /**
     * @param  array<string, string|int|float|null>  $replacements
     */
    public function logFromTemplateForActor(
        int $orderId,
        string $templateKey,
        array $replacements,
        string $createdBy,
        int $userId,
        string $userName,
        ?int $employeeId = null,
        string $type = 'local',
    ): bool {
        $replacements = array_merge([
            '{user_name}' => $userName,
            '{role}' => $createdBy,
        ], $replacements);

        $text = strtr(config($templateKey), array_map(static fn ($value) => (string) $value, $replacements));

        order_transaction(
            $type,
            $orderId,
            $text,
            null,
            $createdBy,
            $userId,
            $employeeId
        );

        return true;
    }

    /**
     * @return array{0: Authenticatable|null, 1: string|null}
     */
    public function actor(): array
    {
        return $this->actingUserContextResolver->resolve();
    }
}
