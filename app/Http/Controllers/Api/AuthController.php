<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Exceptions\CustomException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Institution;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        try {
            // Validación
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'email' => 'required|string|email',
                'password' => 'required|string',
            ], [
                'email.required' => 'El correo electrónico es obligatorio.',
                'email.email' => 'El formato del correo electrónico no es válido.',
                'password.required' => 'La contraseña es obligatoria.',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors()->first();
                throw new CustomException($errors);
            }

            $user = User::where('email', $request->email)->first();

            // Verificar si el usuario existe y tiene registro de Google
            if ($user && $user->google_data) {
                throw new CustomException('Este correo está registrado con Google. Por favor, inicia sesión con Google.');
            }

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw new CustomException('Las credenciales proporcionadas son incorrectas.');
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login exitoso',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ], 200);

        } catch (CustomException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error en la solicitud. Por favor, vuelva a intentarlo más tarde.'
            ], 400);
        }
    }
    public function register(Request $request)
    {
        try {


            // Verificar si el email ya existe con registro de Google
            $existingUser = User::where('email', $request['email'])->first();
            if ($existingUser && $existingUser->google_data) {
                throw new CustomException('Este correo ya está registrado con Google. Por favor, inicia sesión con Google.');
            }

            // Validación más robusta
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'name' => 'required|string|max:255|min:2',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'password_confirmation' => 'required|string|min:8',
            ], [
                // Mensajes personalizados
                'name.required' => 'El nombre es obligatorio.',
                'name.min' => 'El nombre debe tener al menos 2 caracteres.',
                'name.max' => 'El nombre no puede exceder 255 caracteres.',
                'email.required' => 'El correo electrónico es obligatorio.',
                'email.email' => 'El formato del correo electrónico no es válido.',
                'email.unique' => 'Este correo electrónico ya está registrado.',
                'password.required' => 'La contraseña es obligatoria.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
                'password.confirmed' => 'La confirmación de contraseña no coincide.',
                'password_confirmation.required' => 'La confirmación de contraseña es obligatoria.',
            ]);

            // Si la validación falla, lanzar CustomException con el primer error
            if ($validator->fails()) {
                $errors = $validator->errors()->first();
                throw new CustomException($errors);
            }

            $validatedData = $validator->validated();

            // Crear el usuario
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'type' => 'student',
            ]);

            // Crear token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Usuario registrado correctamente',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'type' => $user->type,
                    'first_steps_completed' => $user->first_steps_completed ?? false,
                ],
            ], 200);

        } catch (CustomException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error en la solicitud. Por favor, vuelva a intentarlo más tarde.'
            ], 400);
        }
    }

    public function updateUserInfo(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                throw new CustomException('Usuario no autenticado.');
            }


            $data = $request->only([
                'name',
                'profile_photo_path',
                'id_institution',
                'email',
            ]);

            $emailChanged = false;
            foreach ($data as $key => $value) {
                if (!is_null($value)) {
                    if ($key === 'email' && $user->email !== $value) {
                        $emailChanged = true;
                    }
                    $user->$key = $value;
                }
            }
            if ($emailChanged) {
                $user->verification_status = \App\Enums\VerificationStatus::UNVERIFIED;
                $user->email_verified_at = null;
            }
            $user->first_steps_completed = true;
            $user->save();

            $user->load('institution');

            $userArray = $user->toArray();
            $userArray['institution_name'] = $user->institution ? $user->institution->name : null;

            return response()->json([
                'message' => 'user info updated successfully',
                'user' => $userArray,
            ], 200);

        } catch (CustomException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error en la solicitud. Por favor, vuelva a intentarlo más tarde.'
            ], 400);
        }
    }


    // Logout
    public function logout(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                throw new CustomException('Usuario no autenticado.');
            }

            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Sesión cerrada correctamente'
            ], 200);

        } catch (CustomException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error en la solicitud. Por favor, vuelva a intentarlo más tarde.'
            ], 400);
        }
    }

    // Obtener información del usuario
    public function getUserInfo(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                throw new CustomException('Usuario no autenticado.');
            }

            $user->load('institution');

            $userArray = $user->toArray();
            $userArray['institution_name'] = $user->institution ? $user->institution->name : null;
            $userArray['email_verified'] = !is_null($user->email_verified_at);
            $userArray['is_google_user'] = !is_null($user->google_data);

            // Mostrar el label del status de verificación
            $userArray['status_verificacion'] = $user->verification_status?->label() ?? 'Sin verificar';

            return response()->json([
                'message' => 'Información del usuario obtenida correctamente',
                'user' => $userArray,
            ], 200);

        } catch (CustomException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error en la solicitud. Por favor, vuelva a intentarlo más tarde.'
            ], 400);
        }
    }


    // Obtener todas las instituciones
    public function getInstitutions()
    {
        try {
            $institutions = Institution::select('id_institution', 'name')->get();

            return response()->json([
                'message' => 'Instituciones obtenidas correctamente',
                'institutions' => $institutions
            ], 200);

        } catch (CustomException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error en la solicitud. Por favor, vuelva a intentarlo más tarde.'
            ], 400);
        }
    }

    // Reenviar email de verificación
    public function resendEmailVerification(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                throw new CustomException('Usuario no autenticado.');
            }

            if ($user->hasVerifiedEmail()) {
                throw new CustomException('El email ya está verificado.');
            }

            $user->verification_status = \App\Enums\VerificationStatus::PENDING;
            $user->save();

            $user->sendEmailVerificationNotification();

            return response()->json([
                'message' => 'Email de verificación enviado correctamente'
            ], 200);

        } catch (CustomException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error en la solicitud. Por favor, vuelva a intentarlo más tarde.'
            ], 400);
        }
    }


}
