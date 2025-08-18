<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\FounderInvitation;
use Illuminate\Support\Facades\Http;


class AuthGoogleApiController extends Controller
{
  public function callback(Request $request)
    {
        try {
            $code = $request->input('code');
            $url = $request->input('state', '');
            logger()->info('Google callback URL: ' . $url);
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => env('GOOGLE_CLIENT_ID'),
                'client_secret' => env('GOOGLE_CLIENT_SECRET'),
                'redirect_uri' => env('GOOGLE_REDIRECT_URI'),
                'grant_type' => 'authorization_code',
            ]);

            if ($response->failed()) {
                throw new CustomException('No se pudo obtener el token de acceso.');
            }

            $data = $response->json();

            $info = Http::withToken($data['access_token'])->get('https://www.googleapis.com/userinfo/v2/me');

            if ($info->failed()) {
                throw new CustomException('No se pudo obtener la información del usuario.');
            }

            $email = $info['email'];
            $name = $info['name'];

            $user = $this->createOrUpdateUser($email, $name, $data);
            $token = $user->createToken('auth_token')->plainTextToken;

            $user->google_token_expires_at = now()->addSeconds($data['expires_in']);
            $user->save();
           // dd($token);

            $redirectUrl = $url . "?token=" . $token;
            return redirect($redirectUrl);
        } catch (CustomException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (\Throwable $th) {
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

        $r = $request->input('r', '');

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
        logger()->info('Attempting to create or update user', ['email' => $email, 'name' => $name]);

        $user = User::where('email', $email)->first();

        if (!$user) {
            logger()->info('User not found, creating new user', ['email' => $email]);
            $user = User::create([
                'email' => $email,
                'name' => $name,
                'google_data' => $data,
                'type' => 'student',
                'password' => 'password', // No password for Google users
                'email_verified_at' => now(), // Email verificado automáticamente por Google
            ]);
            logger()->info('New user created successfully with verified email', ['user_id' => $user->id_user, 'email' => $email]);
        } else {
            logger()->info('User found, updating google_data', ['user_id' => $user->id_user, 'email' => $email]);
            
            // Actualizar datos de Google
            $user->google_data = $data;
            
            // Si el email no estaba verificado, verificarlo ahora (Google ya lo verificó)
            if (!$user->email_verified_at) {
                $user->email_verified_at = now();
                logger()->info('Email verified automatically via Google', ['user_id' => $user->id_user]);
            }
            
            $user->save();
            logger()->info('User updated successfully', ['user_id' => $user->id_user]);
        }

        return $user;
    }
}