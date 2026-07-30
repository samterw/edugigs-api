<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Gig;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class AdminController extends Controller
{
    /**
     * Retrieve global marketplace metrics.
     *
     * @return JsonResponse
     */
    public function getMetrics(): JsonResponse
    {
        $totalUsers = User::count();
        $activeGigs = Gig::where('status', 'active')->count();
        $totalOrders = Order::count();
        
        $totalRevenue = Order::whereIn('status', ['completed', 'accepted'])
            ->whereNotNull('final_price')
            ->sum('final_price');

        return response()->json([
            'total_users' => $totalUsers,
            'active_gigs' => $activeGigs,
            'total_orders' => $totalOrders,
            'total_revenue' => $totalRevenue
        ]);
    }

    /**
     * Retrieve all users excluding the currently authenticated administrator.
     *
     * @return JsonResponse
     */
    public function getUsers(): JsonResponse
    {
        $users = User::where('id', '!=', auth()->id())->latest()->get();
        
        return response()->json($users);
    }

    /**
     * Toggle the suspension status of a user profile and manage related gig visibilities.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function toggleUserBan(Request $request, $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->is_banned = !$user->is_banned;
        $user->save();

        if ($user->is_banned) {
            Gig::where('user_id', $user->id)->update(['status' => 'suspended']);
        }

        $action = $user->is_banned ? 'banned' : 'unbanned';
        
        return response()->json(['message' => "User successfully {$action}."]);
    }

    /**
     * Retrieve all marketplace gigs alongside their creator profiles.
     *
     * @return JsonResponse
     */
    public function getGigs(): JsonResponse
    {
        $gigs = Gig::with('user')->latest()->get();
        
        return response()->json($gigs);
    }

    /**
     * Permanently remove a specific gig listing from the database.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function deleteGig($id): JsonResponse
    {
        $gig = Gig::findOrFail($id);
        $gig->delete();
        
        return response()->json(['message' => 'Gig permanently removed from the marketplace.']);
    }

    /**
     * Retrieve all application orders with complete buyer and seller relationships.
     *
     * @return JsonResponse
     */
    public function getOrders(): JsonResponse
    {
        $orders = Order::with(['buyer', 'gig.user'])->latest()->get();
        
        return response()->json($orders);
    }

    /**
     * Endorse a flagged or pending gig listing to change its state to active.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function approveGig($id): JsonResponse
    {
        $gig = Gig::findOrFail($id);
        $gig->update(['status' => 'active']);
        
        return response()->json(['message' => 'Gig approved by Admin.']);
    }

    /**
     * Moderate a student review entry based on automated flags or administrative criteria.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function moderateReview(Request $request, $id): JsonResponse
    {
        try {
            $request->validate(['action' => 'required|in:keep,delete']);
            
            $review = Review::findOrFail($id);

            if ($request->action === 'delete') {
                $review->delete();
                return response()->json(['message' => 'Review permanently deleted.']);
            }
            
            return response()->json(['message' => 'Review approved and kept.']);
            
        } catch (Exception $e) {
            // Logs the error internally instead of exposing database structures to the network client
            logger()->error('Review Moderation Failure: ' . $e->getMessage());
            
            return response()->json(['message' => 'An error occurred while executing moderation.'], 500);
        }
    }

    /**
     * Retrieve all reviews that have been isolated by automated profiling metrics.
     *
     * @return JsonResponse
     */
    public function getFlaggedReviews(): JsonResponse
    {
        $reviews = Review::where('is_flagged', true)
            ->with(['user', 'gig'])
            ->latest()
            ->get();

        return response()->json($reviews);
    }
}