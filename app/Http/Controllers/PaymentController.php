<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\Notification;
use Laravel\Sanctum\PersonalAccessToken;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    /**
     * Create a structured Billplz transactional bill instance and generate the settlement portal routing layout.
     *
     * @param Request $request
     * @param int $orderId
     * @return JsonResponse
     */
    public function createBill(Request $request, $orderId): JsonResponse
    {
        $order = Order::with(['buyer', 'gig'])->findOrFail($orderId);

        $price = $order->final_price ?: $order->gig->price;
        $amount = $price * 100;

        $response = Http::withBasicAuth(config('services.billplz.key'), '')
            ->post(config('services.billplz.url') . 'bills', [
                'collection_id'     => config('services.billplz.collection_id'),
                'description'       => "EduGigs Payment for Order #" . $order->id,
                'email'             => $order->buyer->email,
                'name'              => $order->buyer->name,
                'amount'            => $amount,
                'callback_url'      => config('services.billplz.callback_url', url('/api/payment/callback')),
                'redirect_url'      => config('services.billplz.redirect_url', 'http://localhost:5173/activity'), 
                'reference_1_label' => 'Bank Code',
                'reference_1'       => $request->bank_code, 
            ]);

        if ($response->successful()) {
            $bill = $response->json();
            
            $order->update(['billplz_id' => $bill['id']]);

            return response()->json(['url' => $bill['url']]);
        }

        Log::error("Billplz Invoice Engine Generation Failure: " . $response->body());

        return response()->json([
            'error' => 'Payment system integration failed. Please contact engineering support.'
        ], 500);
    }

    /**
     * Parse payment status mappings synchronously from third-party settlement gateways.
     *
     * @param int $orderId
     * @return JsonResponse
     */
    public function checkStatus($orderId): JsonResponse
    {
        $order = Order::with(['gig.user', 'buyer', 'slot'])->findOrFail($orderId);

        if (!$order->billplz_id) {
            return response()->json(['message' => 'No gateway assignment matching this marketplace order transaction.'], 400);
        }

        $response = Http::withBasicAuth(config('services.billplz.key'), '')
            ->get(config('services.billplz.url') . 'bills/' . $order->billplz_id);

        if ($response->successful()) {
            $billData = $response->json();
            
            if ($billData['paid']) {
                $order->update([
                    'payment_status' => 'paid',
                    'status'         => 'completed' 
                ]);

                $order->refresh();
                
                Notification::create([
                    'user_id' => $order->gig->user_id,
                    'title'   => 'Payment Received! 💰',
                    'message' => $order->buyer->name . ' has paid RM ' . number_format($order->final_price, 2) . ' for "' . $order->gig->title . '".',
                    'type'    => 'success',
                ]);

                return response()->json(['message' => 'Payment verified successfully! Order is now completed.']);
            }
            
            return response()->json(['message' => 'Payment authentication trace remains pending authorization.'], 400);
        }

        return response()->json(['message' => 'Communication timeout with payment authentication provider.'], 500);
    }

    /**
     * Render and compile transactional invoice summaries to document stream containers.
     *
     * @param Request $request
     * @param int $orderId
     * @return Response|JsonResponse
     */
    public function downloadInvoice(Request $request, $orderId): Response|JsonResponse
    {
        if (!auth()->check() && $request->has('token')) {
            $token = PersonalAccessToken::findToken($request->token);
            if ($token) {
                auth()->login($token->tokenable);
            }
        }

        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $order = Order::with(['buyer', 'gig.user'])->findOrFail($orderId);

        if ($order->buyer_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($order->payment_status !== 'paid') {
            return response()->json(['error' => 'Invoice only available for paid orders'], 403);
        }

        $pdf = Pdf::loadView('invoice', compact('order'));
        return $pdf->stream('EduGigs_Invoice_'.$order->id.'.pdf');
    }
}