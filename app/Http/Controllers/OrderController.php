<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Gig;
use App\Models\GigSlot;
use App\Models\Notification; 
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Retrieve incoming freelance requests sent to the authenticated provider.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function incomingOrders(Request $request): JsonResponse
    {
        $orders = Order::whereHas('gig', function($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->with(['gig', 'buyer', 'slot'])->latest()->get();

        return response()->json($orders);
    }

    /**
     * Initialize a service transaction request and atomically capture scheduling allocations.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        if ($request->user()->role === 'admin') {
            return response()->json(['error' => 'Administrators are restricted from placing orders.'], 403);
        }
        
        $validated = $request->validate([
            'gig_id' => 'required|exists:gigs,id',
            'slot_id' => 'nullable|exists:gig_slots,id',
            'notes' => 'nullable|string',
        ]);

        $gig = Gig::findOrFail($validated['gig_id']);

        if ($gig->user_id === $request->user()->id) {
            return response()->json(['error' => 'You cannot order your own service.'], 403);
        }

        $slot = null;
        if (isset($validated['slot_id'])) {
            $slot = GigSlot::where('id', $validated['slot_id'])->where('gig_id', $gig->id)->first();
            if (!$slot || $slot->is_booked) {
                return response()->json(['error' => 'This time slot is no longer available.'], 422);
            }
        }

        return DB::transaction(function () use ($request, $validated, $gig, $slot) {
            $order = Order::create([
                'gig_id' => $validated['gig_id'],
                'slot_id' => $validated['slot_id'] ?? null,
                'buyer_id' => $request->user()->id,
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
            ]);

            if ($slot) {
                $slot->update(['is_booked' => true]);
            }

            Notification::create([
                'user_id' => $gig->user_id,
                'title'   => 'New Request!',
                'message' => $request->user()->name . ' wants to book "' . $gig->title . '".',
                'type'    => 'warning',
            ]);

            return response()->json([
                'message' => 'Order placed successfully!', 
                'order' => $order->load('gig')
            ], 201);
        });
    }

    /**
     * Mutate state mappings of client requests and initialize communications where accepted.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $order = Order::whereHas('gig', function($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->with('gig')->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:accepted,declined,completed',
            'final_price' => 'nullable|numeric' 
        ]);

        return DB::transaction(function () use ($request, $order, $validated) {
            $order->status = $validated['status'];
            if ($request->has('final_price')) {
                $order->final_price = $request->final_price;
            }
            
            $order->save();
            
            $title = '';
            $message = '';
            $type = 'info';

            if ($validated['status'] === 'accepted') {
                $title = 'Order Accepted!';
                $message = 'Your request for "' . $order->gig->title . '" was accepted by the seller.';
                $type = 'success';
            } elseif ($validated['status'] === 'completed') {
                $title = 'Service Finished!';
                $message = '"' . $order->gig->title . '" has been marked as complete. Total: RM ' . $order->final_price;
                $type = 'info';
            } elseif ($validated['status'] === 'declined') {
                $title = 'Order Declined';
                $message = 'Unfortunately, your request for "' . $order->gig->title . '" was declined.';
                $type = 'info';
            }

            if ($title !== '') {
                Notification::create([
                    'user_id' => $order->buyer_id,
                    'title'   => $title,
                    'message' => $message,
                    'type'    => $type,
                ]);

                if ($validated['status'] === 'accepted') {
                    Conversation::firstOrCreate([
                        'order_id'    => $order->id,
                        'sender_id'   => $order->buyer_id,
                        'receiver_id' => $order->gig->user_id,
                    ]);
                }
            }

            return response()->json(['message' => 'Order status updated successfully.']);
        });
    }

    /**
     * Retrieve historical procurement listings commissioned by the authenticated student client.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function myPurchases(Request $request): JsonResponse
    {
        if ($request->user()->role === 'admin') {
            return response()->json([]);
        }

        $purchases = Order::where('buyer_id', $request->user()->id)
            ->with(['gig.user', 'slot', 'review']) 
            ->latest()
            ->get();

        return response()->json($purchases);
    }
}