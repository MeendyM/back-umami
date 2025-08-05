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
            'type' => 'required|string',
            'id_institution' => 'nullable|integer',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => $request->type,
            'id_institution' => $request->id_institution,
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

        return response()->json([
            'message' => 'Información del usuario obtenida correctamente',
            'user' => [
                'id_user' => $user->id_user,
                'name' => $user->name,
                'email' => $user->email,
                'type' => $user->type,
                'id_institution' => $user->id_institution,
                'institution_name' => $user->institution ? $user->institution->name : null,
                'email_verified_at' => $user->email_verified_at,
                'current_team_id' => $user->current_team_id,
                'profile_photo_url' => $user->profile_photo_url,
            ]
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
