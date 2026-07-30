<?php

namespace App\Http\Controllers;

use App\Models\Gig;
use App\Models\GigSlot;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http; 
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class GigController extends Controller
{
    /**
     * Retrieve a global directory of active service listings with calculated reputation metrics.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $gigs = Gig::whereHas('user', function($query) {
                $query->where('role', 'seller');
            })
            ->where('status', 'active')
            ->with(['user', 'slots' => function($query) {
                $query->where('is_booked', false)
                    ->where('start_time', '>=', now()->subMinutes(30)); 
            }, 'reviews.user'])
            ->withAvg('reviews', 'rating') 
            ->withCount('reviews')        
            ->latest()
            ->get();

        return response()->json($gigs);
    }

    /**
     * Retrieve the authenticated user's active freelance offerings.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function myGigs(Request $request): JsonResponse
    {
        $gigs = $request->user()->gigs()->with('slots')->latest()->get();

        return response()->json($gigs);
    }

    /**
     * Retrieve soft-deleted temporal allocation slots historically managed by the provider.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function archivedSlots(Request $request): JsonResponse
    {
        $slots = GigSlot::onlyTrashed()
            ->whereHas('gig', function($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->with('gig')
            ->latest('deleted_at')
            ->get();

        return response()->json($slots);
    }

    /**
     * Store a new freelance service offering and initialize scheduling logic.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'max_price' => 'nullable|numeric|gte:price',
            'category' => 'required|string',
            'scheduling_type' => 'required|string|in:always,specific',
            'slots' => 'nullable|array',
            'slots.*.start_time' => 'required|date',
        ]);

        $gigStatus = $this->checkContentSafety($validated['title'], $validated['description']);

        return DB::transaction(function () use ($request, $validated, $gigStatus) {
            $gig = $request->user()->gigs()->create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'max_price' => $validated['max_price'] ?? null,
                'category' => $validated['category'],
                'scheduling_type' => $validated['scheduling_type'],
                'status' => $gigStatus,
            ]);

            if ($validated['scheduling_type'] === 'specific' && !empty($validated['slots'])) {
                $this->syncSlots($gig, $validated['slots']);
            }

            $message = ($gigStatus === 'pending_review') 
                ? 'Gig submitted! Flagged for manual review.' 
                : 'Gig published successfully!';

            return response()->json(['message' => $message, 'gig' => $gig->load('slots')], 201);
        });
    }

    /**
     * Update an existing gig listing profile and adjust corresponding unbooked slots.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $gig = $request->user()->gigs()->findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'max_price' => 'nullable|numeric|gte:price',
            'category' => 'required|string',
            'scheduling_type' => 'required|string|in:always,specific',
            'slots' => 'nullable|array',
            'slots.*.start_time' => 'required|date',
        ]);

        $gigStatus = $this->checkContentSafety($validated['title'], $validated['description']);

        return DB::transaction(function () use ($gig, $validated, $gigStatus) {
            $gig->update([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'max_price' => $validated['max_price'] ?? null,
                'category' => $validated['category'],
                'scheduling_type' => $validated['scheduling_type'],
                'status' => $gigStatus,
            ]);

            $gig->slots()->where('is_booked', false)->delete();
            if ($validated['scheduling_type'] === 'specific' && !empty($validated['slots'])) {
                $this->syncSlots($gig, $validated['slots']);
            }

            return response()->json(['message' => 'Service updated!', 'gig' => $gig->load('slots')]);
        });
    }

    /**
     * Remove a specified service listing from active visibility boundaries.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $gig = $request->user()->gigs()->findOrFail($id);
        $gig->delete();
        
        return response()->json(['message' => 'Service deleted successfully']);
    }

    /**
     * Evaluate listing copy safety profile via automated AI profiling.
     *
     * @param string $title
     * @param string $description
     * @return string
     */
    private function checkContentSafety($title, $description): string
    {
        $textToAnalyze = $title . "\n" . $description;
        Log::info("Checking content safety via ModerateAPI for: " . $title);

        try {
            $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'X-API-Key'    => config('services.moderateapi.key', env('MODERATE_API_KEY'))
                ])
                ->timeout(10)
                ->post('https://moderateapi.com/api/v1/moderate', [
                    'text'    => $textToAnalyze,
                    'context' => 'gig_marketplace'
                ]);

            if ($response->failed()) {
                Log::error("ModerateAPI Failed: " . $response->body());
                return 'pending_review'; 
            }

            $result = $response->json();
            
            if (isset($result['safe']) && $result['safe'] === false) {
                Log::info("ModerateAPI flagged content.");
                return 'pending_review';
            }

            return 'active';

        } catch (Exception $e) {
            Log::error("ModerateAPI Exception: " . $e->getMessage());
            return 'pending_review'; 
        }
    }

    /**
     * Atomically process temporal intervals and append zero-conflict reservation periods.
     *
     * @param Gig $gig
     * @param array $slotsData
     * @return void
     */
    private function syncSlots($gig, array $slotsData): void
    {
        foreach ($slotsData as $data) {
            $start = Carbon::parse($data['start_time']);
            $end = (clone $start)->addMinutes(60);
            
            $conflict = $gig->slots()->where(function ($query) use ($start, $end) {
                $query->where('start_time', '<', $end)
                      ->whereRaw("DATE_ADD(start_time, INTERVAL 60 MINUTE) > ?", [$start]);
            })->exists();

            if (!$conflict) {
                $gig->slots()->create(['start_time' => $start, 'is_booked' => false]);
            }
        }
    }
}