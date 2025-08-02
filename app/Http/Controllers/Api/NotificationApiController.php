<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationApiController extends Controller
{
    // Obtener todas las notificaciones del usuario autenticado
    public function getUserNotifications(Request $request)
    {
        $user = $request->user();
        
        // Obtener notificaciones que le corresponden al usuario
        $notifications = $user->notifications()
            ->with(['sender', 'targetInstitution'])
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
            ->with(['sender', 'targetInstitution'])
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
        if ($notification->pivot->read_at) {
            return response()->json([
                'message' => 'Notificación ya estaba marcada como leída',
                'notification' => $notification
            ]);
        }

        // Marcar como leída
        $user->notifications()->updateExistingPivot($notificationId, [
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
        
        // Obtener IDs de notificaciones no leídas
        $unreadNotificationIds = $user->unreadNotifications()->pluck('id_notification');
        
        if ($unreadNotificationIds->isEmpty()) {
            return response()->json([
                'message' => 'No hay notificaciones pendientes por leer',
                'marked_count' => 0
            ]);
        }

        // Marcar todas como leídas
        DB::table('user_notifications')
            ->where('user_id', $user->id_user)
            ->whereIn('notification_id', $unreadNotificationIds)
            ->whereNull('read_at')
            ->update(['read_at' => now(), 'updated_at' => now()]);

        return response()->json([
            'message' => 'Todas las notificaciones marcadas como leídas',
            'marked_count' => $unreadNotificationIds->count()
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
            ->with(['sender', 'targetInstitution'])
            ->where('id_notification', $notificationId)
            ->first();

        if (!$notification) {
            return response()->json([
                'message' => 'Notificación no encontrada'
            ], 404);
        }

        // Formatear la respuesta con información de lectura
        $notificationData = $notification->toArray();
        $notificationData['is_read'] = !is_null($notification->pivot->read_at);
        $notificationData['read_at'] = $notification->pivot->read_at;

        return response()->json([
            'message' => 'Notificación obtenida correctamente',
            'notification' => $notificationData
        ]);
    }

    // Eliminar notificación del usuario (soft delete en la tabla pivot)
    public function deleteUserNotification(Request $request, $notificationId)
    {
        $user = $request->user();
        
        $notification = $user->notifications()->where('id_notification', $notificationId)->first();
        
        if (!$notification) {
            return response()->json([
                'message' => 'Notificación no encontrada'
            ], 404);
        }

        // Eliminar de la tabla pivot (el usuario ya no verá esta notificación)
        $user->notifications()->detach($notificationId);

        return response()->json([
            'message' => 'Notificación eliminada correctamente'
        ]);
    }
}
