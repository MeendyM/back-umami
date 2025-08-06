<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Institution;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        // Verificar si el usuario existe y tiene registro de Google
        if ($user && $user->google_data) {
            return response()->json([
                'message' => 'Este correo está registrado con Google. Por favor, inicia sesión con Google.',
                'error_type' => 'google_login_required'
            ], 400);
        }

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales no son válidas.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login exitoso', 
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
            
        ]);

        // Verificar si el email ya existe con registro de Google
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser && $existingUser->google_data) {
            return response()->json([
                'message' => 'Este correo ya está registrado con Google. Por favor, inicia sesión con Google.',
                'error_type' => 'google_registration_exists'
            ], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => 'student', 
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }
    public function completeFirstSteps(Request $request)
    {
        $user = $request->user();

        $data = $request->only([
            'name',
            'profile_photo_path',
            'id_institution',
        ]);

        foreach ($data as $key => $value) {
            if (!is_null($value)) {
                $user->$key = $value;
            }
        }
        $user->first_steps_completed = true;
        $user->save();

        $user->load('institution');

        $userArray = $user->toArray();
        $userArray['institution_name'] = $user->institution ? $user->institution->name : null;

        return response()->json([
            'message' => 'First steps completed and user info updated successfully',
            'user' => $userArray,
        ]);
    }

    public function updateUserInfo(Request $request)
    {
        $user = $request->user();

        $data = $request->only([
            'name',
            'profile_photo_path',
            'id_institution',
        ]);

        foreach ($data as $key => $value) {
            if (!is_null($value)) {
                $user->$key = $value;
            }
        }
        $user->first_steps_completed = true;
        $user->save();

        $user->load('institution');

        $userArray = $user->toArray();
        $userArray['institution_name'] = $user->institution ? $user->institution->name : null;

        return response()->json([
            'message' => 'user info updated successfully',
            'user' => $userArray,
        ]);
    }
    

    // Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente'
        ]);
    }

    // Obtener información del usuario
    public function getUserInfo(Request $request)
    {
        $user = $request->user()->load('institution');

        $userArray = $user->toArray();
        $userArray['institution_name'] = $user->institution ? $user->institution->name : null;

        return response()->json([
            'message' => 'Información del usuario obtenida correctamente',
            'user' => $userArray,
        ]);
    }


    // Obtener todas las instituciones
    public function getInstitutions()
    {
        $institutions = Institution::select('id_institution', 'name')->get();

        return response()->json([
            'message' => 'Instituciones obtenidas correctamente',
            'institutions' => $institutions
        ]);
    }
}
