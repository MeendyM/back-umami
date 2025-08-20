<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nuevo mensaje de contacto</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            background: #007bff;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .field {
            margin-bottom: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-left: 4px solid #007bff;
        }
        .field strong {
            color: #333;
        }
        .message-content {
            background: white;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>📧 Nuevo mensaje de contacto</h2>
        </div>
        
        <div class="field">
            <strong>👤 Nombre completo:</strong><br>
            {{ $full_name }}
        </div>
        
        <div class="field">
            <strong>📧 Correo electrónico:</strong><br>
            <a href="mailto:{{ $email }}">{{ $email }}</a>
        </div>
        
        <div class="field">
            <strong>📋 Asunto:</strong><br>
            {{ $subject }}
        </div>
        
        <div class="field">
            <strong>💬 Mensaje:</strong>
            <div class="message-content">
                {!! nl2br(e($user_message ?? '')) !!}
            </div>
        </div>
        
        <hr style="margin: 20px 0; border: none; border-top: 1px solid #ddd;">
        
        <p style="color: #666; font-size: 12px;">
            Este mensaje fue enviado desde el formulario de contacto de tu sitio web el {{ date('d/m/Y H:i:s') }}.
        </p>
    </div>
</body>
</html>
