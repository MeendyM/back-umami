<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactApiController extends Controller
{
    public function send(Request $request) {
        // Validar los datos del formulario
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ], [
            'full_name.required' => 'El nombre completo es obligatorio.',
            'full_name.string' => 'El nombre completo debe ser texto.',
            'full_name.max' => 'El nombre completo no puede tener más de 255 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe tener un formato válido.',
            'email.max' => 'El correo electrónico no puede tener más de 255 caracteres.',
            'subject.required' => 'El asunto es obligatorio.',
            'subject.string' => 'El asunto debe ser texto.',
            'subject.max' => 'El asunto no puede tener más de 255 caracteres.',
            'message.required' => 'El mensaje es obligatorio.',
            'message.string' => 'El mensaje debe ser texto.',
            'message.max' => 'El mensaje no puede tener más de 2000 caracteres.',
        ]);

        try {
            // Enviar el correo
            Mail::send('emails.contact', $validated, function ($mail) use ($validated) {
                $mail->to(config('mail.contact_email', 'admin@tudominio.com'))
                     ->subject('Nuevo mensaje de contacto: ' . $validated['subject'])
                     ->replyTo($validated['email'], $validated['full_name']);
            });

            Log::info('Correo de contacto enviado', [
                'from' => $validated['email'],
                'name' => $validated['full_name'],
                'subject' => $validated['subject']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tu mensaje ha sido enviado correctamente. Te responderemos pronto.'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al enviar correo de contacto', [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Hubo un error al enviar tu mensaje. Por favor, inténtalo de nuevo más tarde.'
            ], 500);
        }
    }
}
