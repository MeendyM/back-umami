<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Listar notificaciones del usuario autenticado
    public function index(Request $request)
    {
        $userId = $request->user()->id_user;
        $notifications = \App\Models\Notification::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get();
        return response()->json($notifications);
    }

    // Crear una notificación para un usuario
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id_user',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|string|in:success,error,warning,info',
        ]);

        $notification = \App\Models\Notification::create([
            'user_id' => $validated['user_id'],
            'title' => $validated['title'],
            'message' => $validated['message'],
            'type' => $validated['type'],
            'read' => false,
        ]);

        return response()->json($notification, 201);
    }

    // Marcar una notificación como leída
    public function markAsRead(Request $request, $id)
    {
        $userId = $request->user()->id_user;
        $notification = \App\Models\Notification::where('id_notification', $id)
            ->where('user_id', $userId)
            ->firstOrFail();
        $notification->read = true;
        $notification->save();
        return response()->json(['message' => 'Notificación marcada como leída']);
    }
}
