<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Conversation;
use App\Models\Order; 
use App\Models\Notification; 
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MessageController extends Controller
{
    /**
     * Retrieve all messages associated with a specific order context.
     *
     * @param int $orderId
     * @return JsonResponse
     */
    public function getMessages($orderId): JsonResponse
    {
        $conversation = Conversation::where('order_id', $orderId)->first();
        
        if (!$conversation) {
            return response()->json([
                'messages' => [], 
                'is_locked' => false, 
                'is_gig_owner' => false
            ]);
        }

        $isGigOwner = (auth()->id() === $conversation->receiver_id);

        $messages = Message::where('conversation_id', $conversation->id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'messages' => $messages,
            'is_locked' => (bool) $conversation->is_locked,
            'is_gig_owner' => $isGigOwner
        ]);
    }

    /**
     * Dispatch an instant text message payload and dispatch transactional notifications.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'body' => 'required|string|max:5000'
        ]);

        $conversation = Conversation::where('order_id', $validated['order_id'])->first();

        if ($conversation && $conversation->is_locked) {
            return response()->json(['message' => 'Chat is locked for settlement.'], 403);
        }

        if (!$conversation) {
            $order = Order::with('gig')->findOrFail($validated['order_id']);
            $conversation = Conversation::create([
                'order_id' => $order->id,
                'sender_id' => $order->buyer_id,
                'receiver_id' => $order->gig->user_id,
            ]);
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            'body' => $validated['body']
        ]);

        $orderInfo = Order::with(['gig', 'buyer', 'gig.user'])->find($validated['order_id']);
        
        $receiverId = ($conversation->sender_id === auth()->id()) 
            ? $conversation->receiver_id 
            : $conversation->sender_id;

        $title = ($receiverId === $orderInfo->gig->user_id) 
            ? 'Message from Buyer' 
            : 'Message from Seller';

        $senderName = auth()->user()->name;
        $gigTitle = $orderInfo->gig->title;
        $humanReadableMessage = "{$senderName} sent a message regarding '{$gigTitle}'.";

        $existingNotif = Notification::where('user_id', $receiverId)
            ->where('title', $title)
            ->where('message', $humanReadableMessage)
            ->where('read', false)
            ->first();

        if (!$existingNotif) {
            Notification::create([
                'user_id' => $receiverId,
                'title' => $title,
                'message' => $humanReadableMessage,
                'type' => 'info',
                'read' => false
            ]);
        }

        return response()->json($message);
    }

    /**
     * Mark pending messages from the opposing participant as read.
     *
     * @param int $orderId
     * @return JsonResponse
     */
    public function markAsRead($orderId): JsonResponse
    {
        $conversation = Conversation::where('order_id', $orderId)->first();

        if ($conversation) {
            Message::where('conversation_id', $conversation->id)
                ->where('sender_id', '!=', auth()->id())
                ->where('read', false)
                ->update(['read' => true]);
        }

        return response()->json(['message' => 'Messages marked as read']);
    }

    /**
     * Administrative restriction locking communication pipelines upon service resolution.
     *
     * @param int $orderId
     * @return JsonResponse
     */
    public function lockChat($orderId): JsonResponse
    {
        $conversation = Conversation::where('order_id', $orderId)->first();
        
        if ($conversation) {
            $conversation->is_locked = true;
            $conversation->save();
        }

        return response()->json(['message' => 'Chat locked successfully.']);
    }
}