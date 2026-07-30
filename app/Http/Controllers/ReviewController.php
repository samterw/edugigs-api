<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Gig;
use App\Models\Order;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Submit a transactional service evaluation review and dispatch provider alerts.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'gig_id'   => 'required|exists:gigs,id',
            'order_id' => 'required|exists:orders,id',
            'rating'   => 'required|integer|min:1|max:5',
            'comment'  => 'nullable|string|max:500',
        ]);

        $order = Order::where('id', $validated['order_id'])
            ->where('buyer_id', Auth::id())
            ->where('status', 'completed')
            ->first();

        if (!$order) {
            return response()->json([
                'error' => 'Unauthorized or invalid context. Reviews can only be left for completed purchases.'
            ], 403);
        }

        $existing = Review::where('order_id', $validated['order_id'])->first();

        if ($existing) {
            return response()->json(['message' => 'You have already reviewed this specific order.'], 403);
        }

        return DB::transaction(function () use ($validated, $order) {
            $review = Review::create([
                'user_id'  => Auth::id(),
                'gig_id'   => $validated['gig_id'],
                'order_id' => $validated['order_id'],
                'rating'   => $validated['rating'],
                'comment'  => $validated['comment'],
            ]);

            $gig = Gig::findOrFail($validated['gig_id']);
            
            Notification::create([
                'user_id' => $gig->user_id,
                'title'   => 'New Review! ⭐',
                'message' => Auth::user()->name . ' gave you ' . $validated['rating'] . ' stars for "' . $gig->title . '".',
                'type'    => 'info',
            ]);

            return response()->json([
                'message' => 'Review posted successfully!',
                'review'  => $review
            ], 201);
        });
    }

    /**
     * Isolate a suspicious evaluation entry and alert system administrators.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function flag(Request $request, $id): JsonResponse
    {
        $review = Review::with('user')->findOrFail($id);
        $review->update(['is_flagged' => true]);

        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'title'   => 'Safety Report: Review Flagged',
                'message' => 'A review on the platform was just reported by a user. Please review it in the control panel.',
                'type'    => 'warning',
            ]);
        }

        return response()->json(['message' => 'Review flagged and admins notified successfully.']);
    }
}