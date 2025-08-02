<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Institution;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    // Vista principal de usuarios
    public function users()
    {
        return view('users.index');
    }

    // Vista principal de instituciones
    public function institutions()
    {
        return view('institutions.index');
    }
}
