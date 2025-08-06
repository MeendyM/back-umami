<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Enums\VerificationStatus;
use Illuminate\Support\Facades\Auth;

class VerifyEmailController extends Controller
{
    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'El enlace de verificación no es válido.');
        }

        if ($user->hasVerifiedEmail()) {
            return view('auth.email_already_verified');
        }

        $user->markEmailAsVerified();
        $user->verification_status = VerificationStatus::VERIFIED;
        $user->save();

        Auth::login($user);
        return view('auth.email_verified');
    }
}
