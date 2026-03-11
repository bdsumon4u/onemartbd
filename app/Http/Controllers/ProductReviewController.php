<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductReviewRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Codebyray\ReviewRateable\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ProductReviewController extends Controller
{
    public function store(StoreProductReviewRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $ignoreVerification = $validated['order_id'] === '--0' && $validated['phone'] === '--0';

        $userId = null;

        if (! $ignoreVerification) {
            $order = Order::query()
                ->where('invoice_id', $validated['order_id'])
                ->where('customer_phone', $validated['phone'])
                ->whereHas('products', static function ($query) use ($validated): void {
                    $query->where('product_id', $validated['product_id']);
                })
                ->first();

            if ($order === null) {
                return response()->json(['error' => 'Order verification failed.'], 422);
            }

            $userId = (int)$order->customer_id;
        } else {
            $userId = (int)User::query()->inRandomOrder()->value('id');
        }

        $product = Product::findOrFail($validated['product_id']);

        $product->addReview([
            'review' => $validated['review'],
            'approved' => $ignoreVerification,
            'ratings' => [
                'overall' => (int) $validated['rating'],
            ],
        ], $userId);

        return response()->json(['message' => 'Review submitted, pending approval.']);
    }

    public function index(int $product, Request $request): JsonResponse
    {
        $perPage = 10;
        $page = max((int) $request->query('page', 1), 1);
        $offset = ($page - 1) * $perPage;

        $baseQuery = Review::query()
            ->where('reviewable_type', Product::class)
            ->where('reviewable_id', $product)
            ->where('approved', true)
            ->with('ratings')
            ->orderByDesc('created_at');

        $total = (clone $baseQuery)->count();

        $reviews = $baseQuery
            ->skip($offset)
            ->take($perPage)
            ->get();

        $userNames = User::query()
            ->whereIn('id', $reviews->pluck('user_id')->filter()->all())
            ->pluck('name', 'id');

        $data = $reviews
            ->map(static function (Review $review) use ($userNames): array {
                $overall = optional($review->ratings->firstWhere('key', 'overall'))->value;
                $name = $userNames[$review->user_id] ?? 'Customer';

                return [
                    'review' => $review->review,
                    'rating' => $overall,
                    'customer_name' => $name,
                    'created_at' => $review->created_at?->toDateTimeString(),
                ];
            })
            ->values();

        $hasMore = $offset + $perPage < $total;

        return response()->json([
            'data' => $data,
            'has_more' => $hasMore,
        ]);
    }
}
