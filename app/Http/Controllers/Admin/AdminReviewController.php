<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminReviewController extends Controller
{
    /**
     * Affiche la liste des avis.
     */
    public function index(): View
    {
        $reviews = Review::with(['user', 'book'])
            ->latest()
            ->paginate(20);

        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Modifie la visibilité d'un avis.
     */
    public function updateVisibility(Review $review): RedirectResponse
    {
        $review->update([
            'is_visible' => !$review->is_visible,
        ]);

        $status = $review->is_visible ? 'L\'avis est désormais visible.' : 'L\'avis a été masqué.';

        return back()->with('status', $status);
    }

    /**
     * Supprime un avis.
     */
    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return back()->with('status', 'L\'avis a été supprimé définitivement.');
    }
}
