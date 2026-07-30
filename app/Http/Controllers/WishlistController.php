<?php

namespace App\Http\Controllers;

use App\Models\Gig;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WishlistController extends Controller
{
    /**
     * Retrieve a collection of marketplace listings saved to the user's wishlist repository.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $wishlist = $request->user()->wishlistedGigs()->with('user')->get();

        return response()->json([
            'status' => 'success',
            'data'   => $wishlist
        ]);
    }

    /**
     * Alternately attach or detach a listing profile inside the wishlist pivot table index.
     *
     * @param Request $request
     * @param int $gigId
     * @return JsonResponse
     */
    public function toggle(Request $request, $gigId): JsonResponse
    {
        $gig = Gig::findOrFail($gigId);
        
        $result = $request->user()->wishlistedGigs()->toggle($gig->id);
        
        $isAttached = count($result['attached']) > 0;

        return response()->json([
            'status'        => 'success',
            'is_wishlisted' => $isAttached,
            'message'       => $isAttached ? 'Added to wishlist.' : 'Removed from wishlist.'
        ]);
    }
}