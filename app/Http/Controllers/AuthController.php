<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\SellerPortfolio;
use App\Models\Badge;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Authenticate user credentials and issue an API personal access token.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!str_ends_with($credentials['email'], '@siswa.unimas.my')) {
            return response()->json(['message' => 'Access restricted to UNIMAS student emails only.'], 403);
        }

        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        if ((bool)$user->is_banned === true) {
            Log::warning("Banned user login attempt blocked for: " . $user->email);
            return response()->json([
                'message' => 'Your account has been suspended for malicious or suspicious activities. Please contact support.'
            ], 403);
        }

        if (Auth::attempt($credentials)) {
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token
            ]);
        }

        return response()->json(['message' => 'Invalid credentials.'], 401);
    }

    /**
     * Register a new institutional user profile within the ecosystem.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
                'regex:/^[a-zA-Z0-9._%+-]+@siswa\.unimas\.my$/' 
            ],
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:buyer,seller'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status' => 'active',
        ]);

        return response()->json([
            'message' => 'User registered successfully'
        ], 201);
    }

    /**
     * Update auxiliary profile preferences and communication channels.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'nickname'           => 'nullable|string|max:50',
            'bio'                => 'nullable|string|max:500',
            'faculty'            => 'nullable|string|max:100',
            'availability_type'  => 'nullable|string|max:50',
            'availability_start' => 'nullable|string',
            'availability_end'   => 'nullable|string',
            'social_whatsapp'    => 'nullable|string|max:20',
            'social_instagram'   => 'nullable|string|max:50',
            'social_facebook'    => 'nullable|string|max:100',
            'social_telegram'    => 'nullable|string|max:50',
            'social_email'       => 'nullable|email|max:100',
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profile details and social links updated successfully!',
            'user'    => $user 
        ]);
    }

    /**
     * Store and associate a graphic flyer showcase asset with the user's seller portfolio.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadPortfolioAsset(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'title' => 'nullable|string|max:100'
        ]);

        $user = $request->user();
        $path = $request->file('image')->store('portfolios', 'public');

        $portfolio = SellerPortfolio::create([
            'user_id'    => $user->id,
            'image_path' => '/storage/' . $path,
            'title'      => $request->title
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Promotional flyer uploaded successfully.',
            'data'    => $portfolio
        ], 201);
    }

    /**
     * Retrieve public profile details including gigs, portfolios, and reputation badges.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getPublicProfile($id): JsonResponse
    {
        $user = User::with(['gigs', 'portfolios', 'badges'])->findOrFail($id);
        return response()->json(['status' => 'success', 'data' => $user]);
    }

    /**
     * Transition a buyer profile into an active marketplace service provider.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function upgradeToSeller(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user->role === 'seller') {
            return response()->json(['message' => 'Already a seller'], 400);
        }

        if ($user->seller_deactivated_at) {
            $daysSince = Carbon::parse($user->seller_deactivated_at)->diffInDays(now());
            if ($daysSince < 30) {
                return response()->json(['message' => "Cooldown active."], 403);
            }
        }

        $user->update(['role' => 'seller', 'seller_deactivated_at' => null]);
        return response()->json(['message' => 'Upgraded to seller!', 'user' => $user]);
    }

    /**
     * Revert a provider profile back to standard client execution tier.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function downgradeToBuyer(Request $request): JsonResponse
    {
        $user = $request->user();
        $request->validate(['reason' => 'required|string']);

        $user->gigs()->delete();
        $user->update([
            'role' => 'buyer',
            'seller_deactivated_at' => now()
        ]);

        return response()->json(['message' => 'Seller account deactivated.', 'user' => $user]);
    }

    /**
     * Change the public visibility parameter of an individual portfolio entry.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function togglePortfolioVisibility(Request $request, $id): JsonResponse
    {
        $portfolio = SellerPortfolio::where('user_id', $request->user()->id)->findOrFail($id);
        $portfolio->update(['is_visible' => !$portfolio->is_visible]);
        return response()->json(['status' => 'success', 'data' => $portfolio]);
    }

    /**
     * Delete a graphic showcase asset from local storage disks and clean model records.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function deletePortfolioAsset(Request $request, $id): JsonResponse
    {
        $portfolio = SellerPortfolio::where('user_id', $request->user()->id)->findOrFail($id);
        $relativeStoragePath = str_replace('/storage/', '', $portfolio->image_path);
        
        if (Storage::disk('public')->exists($relativeStoragePath)) {
            Storage::disk('public')->delete($relativeStoragePath);
        }

        $portfolio->delete();
        return response()->json(['status' => 'success', 'message' => 'Asset discarded.']);
    }
}