<?php

declare(strict_types=1);

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use Codebyray\ReviewRateable\Models\Review;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

final class ProductReviewApprovalController extends Controller
{
    public function index(): View
    {
        $reviews = Review::query()
            ->with(['reviewable', 'ratings'])
            ->orderBy('approved', 'asc')
            ->latest()
            ->paginate(30);

        return view('backEnd.admin.review_approvals.index', compact('reviews'));
    }

    public function approve(int $id): RedirectResponse
    {
        $review = Review::findOrFail($id);
        $review->approved = true;
        $review->save();

        return redirect()->back()->with('success', 'Review approved.');
    }

    public function reject(int $id): RedirectResponse
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()->back()->with('success', 'Review rejected.');
    }
}
