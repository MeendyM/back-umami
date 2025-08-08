<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contraseña restablecida</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    backgroundImage: {
                        'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-black via-gray-900 to-gray-800 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Header con gradiente -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">
                {{ config('app.name', 'Umami') }}
            </h1>
        </div>

        <!-- Card principal con glassmorphism -->
        <div class="bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-2xl border border-gray-700 p-8 text-center">
            <!-- Icono de éxito animado -->
            <div class="mb-6">
                <div class="mx-auto w-20 h-20 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full flex items-center justify-center animate-pulse">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>

            <!-- Título de éxito -->
            <h2 class="text-3xl font-bold text-gray-300 mb-4">
                ¡Contraseña cambiada!
            </h2>

            <!-- Mensaje principal -->
            <p class="text-gray-400 mb-8 leading-relaxed">
                Tu contraseña ha sido restablecida exitosamente. Puedes iniciar sesión con tu nueva contraseña.
            </p>

            <!-- Mensaje de éxito adicional -->
            <div class="bg-green-500/10 border border-green-500/30 rounded-lg p-4 mb-6">
                <p class="text-green-400 text-sm">
                    🔐 Tu cuenta está ahora segura con la nueva contraseña
                </p>
            </div>

            <!-- Botón para ir al login -->
            <a href="{{ $frontUrl }}" 
               class="inline-block w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white py-4 rounded-lg font-medium transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl text-decoration-none text-center">
                Ir al login
            </a>

            <!-- Botón secundario para cerrar ventana -->
            <button onclick="window.close()" 
                    class="w-full mt-3 text-gray-300 hover:text-white border border-gray-600/30 hover:border-gray-500/50 py-2 rounded-lg transition-all duration-200">
                Cerrar ventana
            </button>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6">
            <p class="text-gray-500 text-sm">
                Serás redirigido al login para iniciar sesión
            </p>
        </div>
    </div>

    <script>
        // Auto-redirect después de 5 segundos si no se hace clic
        setTimeout(() => {
            if (confirm('¿Te gustaría ser redirigido al login ahora?')) {
                window.location.href = '{{ $frontUrl }}';
            }
        }, 5000);
    </script>
</body>
</html>
