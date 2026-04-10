<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    /**
     * Récupérer toutes les notifications de l'utilisateur connecté
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $notifications = $user->notifications()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'notifications' => $notifications
        ]);
    }

    /**
     * Marquer une notification comme lue
     */
    public function markAsRead(Request $request, $id)
    {
        $user = $request->user();

        $notification = $user->notifications()->where('id', $id)->firstOrFail();
        $notification->update(['lu' => true]);

        return response()->json([
            'message' => 'Notification marquée comme lue',
            'notification' => $notification
        ]);
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();

        $user->notifications()->where('lu', false)->update(['lu' => true]);

        return response()->json([
            'message' => 'Toutes les notifications ont été marquées comme lues'
        ]);
    }

    /**
     * Supprimer une notification
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        $notification = $user->notifications()->where('id', $id)->firstOrFail();
        $notification->delete();

        return response()->json([
            'message' => 'Notification supprimée avec succès'
        ]);
    }
}
