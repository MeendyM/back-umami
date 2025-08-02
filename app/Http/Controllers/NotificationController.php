<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Vista principal de notificaciones
    public function index()
    {
        return view('notifications.index');
    }
}
