<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recupera tu contraseña</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #000000, #1f2937, #374151);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            min-height: 100vh;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 32px;
        }
        .logo {
            font-size: 36px;
            font-weight: bold;
            background: linear-gradient(to right, #60a5fa, #a855f7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            color: #60a5fa; /* Fallback para clientes que no soportan gradientes */
        }
        .card {
            background: rgba(31, 41, 55, 0.8);
            border-radius: 16px;
            padding: 32px;
            border: 1px solid #374151;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        .icon-container {
            text-align: center;
            margin-bottom: 24px;
        }
        .icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(to right, #9333ea, #2563eb);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #d1d5db;
            text-align: center;
            margin-bottom: 16px;
        }
        .greeting {
            color: #9ca3af;
            text-align: center;
            margin-bottom: 24px;
        }
        .username {
            color: #60a5fa;
            font-weight: 500;
        }
        .message {
            color: #d1d5db;
            text-align: center;
            margin-bottom: 32px;
            line-height: 1.6;
        }
        .button-container {
            text-align: center;
            margin-bottom: 32px;
        }
        .button {
            display: inline-block;
            background: linear-gradient(to right, #9333ea, #7c3aed);
            color: white;
            padding: 16px 32px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .button:hover {
            background: linear-gradient(to right, #7c3aed, #6d28d9);
        }
        .warning {
            background: rgba(55, 65, 81, 0.5);
            border: 1px solid #4b5563;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
        }
        .warning-text {
            color: #9ca3af;
            font-size: 14px;
            text-align: center;
        }
        .warning-icon {
            color: #fbbf24;
        }
        .secondary-message {
            color: #9ca3af;
            text-align: center;
            font-size: 14px;
        }
        .footer {
            text-align: center;
            margin-top: 32px;
        }
        .footer-text {
            color: #6b7280;
            font-size: 14px;
        }
        .footer-app-name {
            font-weight: 500;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header con gradiente -->
        <div class="header">
            <div class="logo">{{ config('app.name', 'Umami') }}</div>
        </div>

        <!-- Card principal -->
        <div class="card">
            <!-- Icono de llave -->
            <div class="icon-container">
                <div class="icon">
                    <svg width="32" height="32" fill="none" stroke="white" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-3a1 1 0 011-1h2.586l6.414-6.414a6 6 0 019 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Título -->
            <div class="title">Recupera tu contraseña</div>

            <!-- Saludo personalizado -->
            <div class="greeting">
                Hola <span class="username">{{ $user->name ?? 'usuario' }}</span>,
            </div>

            <!-- Mensaje principal -->
            <div class="message">
                Recibimos una solicitud para restablecer tu contraseña. Haz clic en el siguiente botón para continuar:
            </div>

            <!-- Botón principal -->
            <div class="button-container">
                <a href="{{ $resetUrl }}" class="button">
                    Cambiar mi contraseña
                </a>
            </div>

            <!-- Mensaje de seguridad -->
            <div class="warning">
                <div class="warning-text">
                    <span class="warning-icon">⚠️</span> Este enlace expirará en 60 minutos por seguridad.
                </div>
            </div>

            <!-- Mensaje secundario -->
            <div class="secondary-message">
                Si no solicitaste este cambio, puedes ignorar este mensaje.
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-text">
                Gracias,<br>
                <span class="footer-app-name">{{ config('app.name', 'Umami') }}</span>
            </div>
        </div>
    </div>
</body>
</html>
