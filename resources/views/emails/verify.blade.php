@component('mail::message')
# ¡Verifica tu correo electrónico!

Hola {{ $user->name ?? 'usuario' }},

Gracias por registrarte en nuestra plataforma. Para activar tu cuenta y disfrutar de todos los beneficios, por favor haz clic en el siguiente botón:

@component('mail::button', ['url' => $verificationUrl])
Verificar mi correo
@endcomponent

Si no creaste una cuenta, puedes ignorar este mensaje.

Gracias,<br>
{{ config('app.name') }}
@endcomponent
