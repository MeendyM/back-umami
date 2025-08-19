<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
{
    // Obtener todas las notificaciones del usuario autenticado
    public function getUserNotifications(Request $request)
    {
        $user = $request->user();
        
        // Obtener notificaciones que le corresponden al usuario
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'message' => 'Notificaciones obtenidas correctamente',
            'notifications' => $notifications->items(),
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
                'has_more_pages' => $notifications->hasMorePages(),
            ]
        ]);
    }

    // Obtener solo notificaciones no leídas
    public function getUnreadNotifications(Request $request)
    {
        $user = $request->user();
        
        $notifications = $user->unreadNotifications()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'message' => 'Notificaciones no leídas obtenidas correctamente',
            'notifications' => $notifications,
            'unread_count' => $notifications->count()
        ]);
    }

    // Marcar una notificación como leída
    public function markAsRead(Request $request, $notificationId)
    {
        $user = $request->user();
        
        // Verificar que la notificación existe y pertenece al usuario
        $notification = $user->notifications()->where('id_notification', $notificationId)->first();
        
        if (!$notification) {
            return response()->json([
                'message' => 'Notificación no encontrada'
            ], 404);
        }

        // Si ya está leída, no hacer nada
        if ($notification->read) {
            return response()->json([
                'message' => 'Notificación ya estaba marcada como leída',
                'notification' => $notification
            ]);
        }

        // Marcar como leída
        $notification->update([
            'read' => true,
            'read_at' => now()
        ]);

        return response()->json([
            'message' => 'Notificación marcada como leída correctamente',
            'notification' => $notification
        ]);
    }

    // Marcar todas las notificaciones como leídas
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        
        // Obtener notificaciones no leídas del usuario
        $unreadCount = $user->unreadNotifications()->count();
        
        if ($unreadCount === 0) {
            return response()->json([
                'message' => 'No hay notificaciones pendientes por leer',
                'marked_count' => 0
            ]);
        }

        // Marcar todas como leídas
        $user->unreadNotifications()->update([
            'read' => true
        ]);

        return response()->json([
            'message' => 'Todas las notificaciones marcadas como leídas',
            'marked_count' => $unreadCount
        ]);
    }

    // Obtener contador de notificaciones no leídas
    public function getUnreadCount(Request $request)
    {
        $user = $request->user();
        
        $unreadCount = $user->unreadNotifications()->count();

        return response()->json([
            'message' => 'Contador de notificaciones no leídas',
            'unread_count' => $unreadCount
        ]);
    }

    // Obtener una notificación específica
    public function getNotification(Request $request, $notificationId)
    {
        $user = $request->user();
        
        $notification = $user->notifications()
            ->where('id_notification', $notificationId)
            ->first();

        if (!$notification) {
            return response()->json([
                'message' => 'Notificación no encontrada'
            ], 404);
        }

        return response()->json([
            'message' => 'Notificación obtenida correctamente',
            'notification' => $notification
        ]);
    }

    // Eliminar notificación del usuario
    public function deleteUserNotification(Request $request, $notificationId)
    {
        $user = $request->user();
        
        $notification = $user->notifications()->where('id_notification', $notificationId)->first();
        
        if (!$notification) {
            return response()->json([
                'message' => 'Notificación no encontrada'
            ], 404);
        }

        // Eliminar la notificación
        $notification->delete();

        return response()->json([
            'message' => 'Notificación eliminada correctamente'
        ]);
    }
}
