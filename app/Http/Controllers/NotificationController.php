<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    /**
     * Retrieve a collection of all notifications for the authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()->notifications()->latest()->get();

        return response()->json($notifications);
    }

    /**
     * Mark a specific notification as read, validating ownership.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function markAsRead(Request $request, $id): JsonResponse
    {
        $request->user()->notifications()->findOrFail($id)->update(['read' => true]);

        return response()->json(['message' => 'Notification marked as read.']);
    }

    /**
     * Mark all unread notifications for the user as read.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()->notifications()->where('read', false)->update(['read' => true]);

        return response()->json(['message' => 'All notifications marked as read.']);
    }

    /**
     * Delete a specific notification entry, validating ownership.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $request->user()->notifications()->findOrFail($id)->delete();

        return response()->json(['message' => 'Notification permanently deleted.']);
    }

    /**
     * Purge all notifications belonging to the authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function clearAll(Request $request): JsonResponse
    {
        $request->user()->notifications()->delete();

        return response()->json(['message' => 'All notifications cleared.']);
    }
}