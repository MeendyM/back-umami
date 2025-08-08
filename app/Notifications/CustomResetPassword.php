<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends BaseResetPassword
{
    public function toMail($notifiable)
    {
        $resetUrl = url(route('password.reset.custom', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Recupera tu contraseña')
            ->markdown('emails.reset_password', [
                'user' => $notifiable,
                'resetUrl' => $resetUrl,
            ]);
    }
}
