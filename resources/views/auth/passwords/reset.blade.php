<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer contraseña</title>
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
        <div class="bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-2xl border border-gray-700 p-8">
            <!-- Icono de llave -->
            <div class="text-center mb-6">
                <div class="mx-auto w-16 h-16 bg-gradient-to-r from-purple-600 to-blue-600 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-3a1 1 0 011-1h2.586l6.414-6.414a6 6 0 019 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Título -->
            <h2 class="text-2xl font-bold text-gray-300 text-center mb-6">
                Restablecer contraseña
            </h2>

            <!-- Mostrar errores si existen -->
            @if ($errors->any())
                <div class="bg-red-500/10 border border-red-500/30 rounded-lg p-4 mb-6">
                    @foreach ($errors->all() as $error)
                        <p class="text-red-400 text-sm">❌ {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- Formulario -->
            <form method="POST" action="{{ route('password.reset.custom.post') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <!-- Campo Nueva Contraseña -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                        Nueva contraseña
                    </label>
                    <div class="relative">
                        <input type="password" 
                               id="password"
                               name="password" 
                               required
                               class="w-full bg-gray-700/50 text-white border border-gray-600/50 rounded-lg px-4 py-3 pr-12 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 backdrop-blur-sm"
                               placeholder="Ingresa tu nueva contraseña">
                        <button type="button" 
                                onclick="togglePassword('password')"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-300 transition-colors duration-200">
                            <span id="password-toggle-text">Mostrar</span>
                        </button>
                    </div>
                </div>

                <!-- Campo Confirmar Contraseña -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-2">
                        Confirmar contraseña
                    </label>
                    <div class="relative">
                        <input type="password" 
                               id="password_confirmation"
                               name="password_confirmation" 
                               required
                               class="w-full bg-gray-700/50 text-white border border-gray-600/50 rounded-lg px-4 py-3 pr-12 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 backdrop-blur-sm"
                               placeholder="Confirma tu nueva contraseña">
                        <button type="button" 
                                onclick="togglePassword('password_confirmation')"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-300 transition-colors duration-200">
                            <span id="password_confirmation-toggle-text">Mostrar</span>
                        </button>
                    </div>
                </div>

                <!-- Mensaje de seguridad -->
                <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-3">
                    <p class="text-yellow-400 text-sm text-center">
                        🔒 Tu contraseña debe tener al menos 8 caracteres
                    </p>
                </div>

                <!-- Botón de envío -->
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white py-3 rounded-lg font-medium transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    Cambiar contraseña
                </button>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6">
            <p class="text-gray-500 text-sm">
                Este enlace expirará pronto por seguridad
            </p>
        </div>
    </div>

    <script>
        // Función para mostrar/ocultar contraseña
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const toggleText = document.getElementById(fieldId + '-toggle-text');
            
            if (field.type === 'password') {
                field.type = 'text';
                toggleText.textContent = 'Ocultar';
            } else {
                field.type = 'password';
                toggleText.textContent = 'Mostrar';
            }
        }

        // Validación en tiempo real
        const passwordField = document.getElementById('password');
        const confirmField = document.getElementById('password_confirmation');
        
        function validatePassword() {
            const password = passwordField.value;
            const confirm = confirmField.value;
            
            if (confirm && password !== confirm) {
                confirmField.setCustomValidity('Las contraseñas no coinciden');
            } else {
                confirmField.setCustomValidity('');
            }
        }
        
        passwordField.addEventListener('input', validatePassword);
        confirmField.addEventListener('input', validatePassword);
    </script>
</body>
</html>
