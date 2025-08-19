<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\FounderInvitation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class AuthGoogleApiController extends Controller
{
  public function callback(Request $request)
    {
        Log::info('Google Auth Callback - Inicio', ['all_params' => $request->all()]);
        
        try {
            // Verificar si Google envió un error
            if ($request->has('error')) {
                Log::error('Google Auth Callback - Error de Google', [
                    'error' => $request->input('error'),
                    'error_description' => $request->input('error_description'),
                    'state' => $request->input('state')
                ]);
                throw new CustomException('Error de autenticación con Google: ' . $request->input('error_description', $request->input('error')));
            }

            $code = $request->input('code');
            $url = $request->input('state', '');
            
            if (!$code) {
                Log::error('Google Auth Callback - Código de autorización faltante');
                throw new CustomException('Código de autorización faltante');
            }
            
            Log::info('Google Auth Callback - Parámetros', [
                'code_presente' => !empty($code),
                'state_url' => $url,
                'codigo_longitud' => strlen($code ?? '')
            ]);
            
            logger()->info('Google callback URL: ' . $url);
            
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => env('GOOGLE_CLIENT_ID'),
                'client_secret' => env('GOOGLE_CLIENT_SECRET'),
                'redirect_uri' => env('GOOGLE_REDIRECT_URI'),
                'grant_type' => 'authorization_code',
            ]);

            Log::info('Google Token Exchange', [
                'status' => $response->status(),
                'response_keys' => array_keys($response->json() ?? [])
            ]);

            if ($response->failed()) {
                Log::error('Error obteniendo token de Google', [
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
                throw new CustomException('No se pudo obtener el token de acceso.');
            }

            $data = $response->json();

            $info = Http::withToken($data['access_token'])->get('https://www.googleapis.com/userinfo/v2/me');

            Log::info('Google User Info Request', [
                'status' => $info->status(),
                'has_email' => isset($info->json()['email'])
            ]);

            if ($info->failed()) {
                Log::error('Error obteniendo info de usuario', [
                    'status' => $info->status(),
                    'response' => $info->json()
                ]);
                throw new CustomException('No se pudo obtener la información del usuario.');
            }

            $email = $info['email'];
            $name = $info['name'];

            Log::info('Datos del usuario obtenidos', [
                'email' => $email,
                'name' => $name
            ]);

            $user = $this->createOrUpdateUser($email, $name, $data);
            $token = $user->createToken('auth_token')->plainTextToken;

            $user->google_token_expires_at = now()->addSeconds($data['expires_in']);
            $user->save();
           // dd($token);

            Log::info('Google Auth Success', [
                'user_id' => $user->id_user,
                'redirect_url' => $url
            ]);

            $redirectUrl = $url . "?token=" . $token;
            return redirect($redirectUrl);
        } catch (CustomException $e) {
            Log::error('CustomException en Google Auth', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (\Throwable $th) {
            Log::error('Error inesperado en Google Auth', [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
                'trace' => $th->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Error en la solicitud. Por favor, vuelva a intentarlo más tarde.'
            ], 400);
        }
    }


    public function check(Request $request)
    {
        try {
            $r = $request->input('r', ''); // Obtener el type de la solicitud

            $user = User::where('google_data', '!=', null)->first();
            if (!$user) {
                throw new CustomException('No se encontró un usuario con datos de Google.');
            }

            // Verificar si el usuario está suspendido
            if ($user->is_suspend) {
                throw new CustomException('Tu cuenta está suspendida. Contacta al administrador para más información.');
            }

            $data = $user->google_data;

            $info = $this->getGoogleUser($data);

            if (!$info) {
                throw new CustomException('No se pudo obtener la información del usuario.');
            }

            $email = $info['email'];
            $name = $info['name'];

            $user = $this->createOrUpdateUser($email, $name, $info['data']);

            // Verificar nuevamente después de createOrUpdateUser por si cambió el estado
            if ($user->is_suspend) {
                throw new CustomException('Tu cuenta está suspendida. Contacta al administrador para más información.');
            }

            $token = $user->createToken('auth_token')->plainTextToken;
            $user->save();

            $redirectUrl = $r . "?token=" . $token;
            
            return redirect($redirectUrl);
        } catch (CustomException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error en la solicitud. Por favor, vuelva a intentarlo más tarde.'
            ], 400);
        }
    }

    public function login(Request $request)
    {
        Log::info('Google Auth Login - Inicio', ['all_params' => $request->all()]);

        $r = $request->input('r', '');

        Log::info('Google Auth Login - Parámetros', [
            'redirect_url' => $r,
            'google_client_id' => env('GOOGLE_CLIENT_ID'),
            'google_redirect_uri' => env('GOOGLE_REDIRECT_URI')
        ]);

        $scopes = [
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile',
        ];
        $url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'redirect_uri' => env('GOOGLE_REDIRECT_URI'),
            'response_type' => 'code',
            'scope' => implode(' ', $scopes),
            'access_type' => 'offline',
            'prompt' => 'consent',
            'state' => $r,
        ]);

        Log::info('Google Auth Login - URL generada', ['auth_url' => $url]);

        return redirect($url);
    }

    public function getGoogleUser($data, $intent = 0)
    {
        $response = Http::withToken($data['access_token'])->get('https://www.googleapis.com/userinfo/v2/me');

        if ($response->failed()) {
            if ($intent > 0) {
                throw new CustomException('No se pudo obtener la información del usuario después de intentar refrescar el token.');
            }

            $response_refresh = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'client_id' => env('GOOGLE_CLIENT_ID'),
                'client_secret' => env('GOOGLE_CLIENT_SECRET'),
                'refresh_token' => $data['refresh_token'],
                'grant_type' => 'refresh_token',
            ]);

            logger()->info('Refresh token response: ', ['response' => $response_refresh->json()]);

            if ($response_refresh->failed()) {
                throw new CustomException('No se pudo refrescar el token de acceso.');
            }

            $response_refresh = $response_refresh->json();
            $data['access_token'] = $response_refresh['access_token'];
            return $this->getGoogleUser($data, $intent + 1);
        }

        return [
            'email' => $response['email'],
            'name' => $response['name'],
            'data' => $data,
        ];
    }

    public function createOrUpdateUser($email, $name, $data)
    {
        Log::info('createOrUpdateUser - Inicio', [
            'email' => $email, 
            'name' => $name,
            'has_data' => !empty($data)
        ]);

        try {
            $user = User::where('email', $email)->first();

            if (!$user) {
                Log::info('createOrUpdateUser - Usuario no encontrado, creando nuevo', ['email' => $email]);
                
                $user = User::create([
                    'email' => $email,
                    'name' => $name,
                    'google_data' => $data,
                    'type' => 'student',
                    'password' => 'password', // No password for Google users
                    'email_verified_at' => now(), // Email verificado automáticamente por Google
                ]);
                
                Log::info('createOrUpdateUser - Usuario creado exitosamente', [
                    'user_id' => $user->id_user, 
                    'email' => $email
                ]);
            } else {
                Log::info('createOrUpdateUser - Usuario encontrado, actualizando', [
                    'user_id' => $user->id_user, 
                    'email' => $email,
                    'is_suspended' => $user->is_suspend ?? false
                ]);
                
                // Actualizar datos de Google
                $user->google_data = $data;
                
                // Si el email no estaba verificado, verificarlo ahora (Google ya lo verificó)
                if (!$user->email_verified_at) {
                    $user->email_verified_at = now();
                    Log::info('createOrUpdateUser - Email verificado automáticamente', ['user_id' => $user->id_user]);
                }
                
                $user->save();
                Log::info('createOrUpdateUser - Usuario actualizado exitosamente', ['user_id' => $user->id_user]);
            }

            return $user;
        } catch (\Exception $e) {
            Log::error('createOrUpdateUser - Error', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}