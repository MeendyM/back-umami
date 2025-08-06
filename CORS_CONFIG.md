# Configuración CORS para Laravel

## ✅ Cambios Realizados

### 1. Configuración de CORS (`config/cors.php`)
- ✅ Permitir todos los orígenes (`allowed_origins` => `['*']`)
- ✅ Permitir todos los métodos HTTP (`allowed_methods` => `['*']`)
- ✅ Permitir todos los headers (`allowed_headers` => `['*']`)
- ✅ Habilitar soporte para credenciales (`supports_credentials` => `true`)
- ✅ Cache de 24 horas (`max_age` => `86400`)
- ✅ Rutas incluidas: `['api/*', 'sanctum/csrf-cookie', 'login', 'logout', 'register']`

### 2. Middleware de CORS
- ✅ Middleware nativo de Laravel habilitado (`\Illuminate\Http\Middleware\HandleCors::class`)
- ✅ Middleware personalizado para mayor compatibilidad (`\App\Http\Middleware\CorsMiddleware::class`)
- ✅ Registrado en `bootstrap/app.php` para rutas API y web

### 3. Configuración de Sanctum (`.env`)
```env
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000,localhost:3001,localhost:8080,localhost:5173,127.0.0.1,127.0.0.1:8000,127.0.0.1:3000,127.0.0.1:3001,::1
```

### 4. Ruta de Prueba
- ✅ Ruta creada: `GET /api/test-cors`

## 🧪 Cómo Probar CORS

### Opción 1: Desde el navegador
```javascript
fetch('http://localhost:8000/api/test-cors')
  .then(response => response.json())
  .then(data => console.log(data))
  .catch(error => console.error('Error:', error));
```

### Opción 2: Con cURL
```bash
curl -H "Origin: http://localhost:3000" \
     -H "Access-Control-Request-Method: GET" \
     -H "Access-Control-Request-Headers: X-Requested-With" \
     -X OPTIONS \
     http://localhost:8000/api/test-cors
```

### Opción 3: Desde tu aplicación frontend
```javascript
// Ejemplo con axios
axios.get('http://localhost:8000/api/test-cors', {
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})
.then(response => {
  console.log('CORS funcionando:', response.data);
})
.catch(error => {
  console.error('Error CORS:', error);
});
```

## 📋 Headers CORS Configurados

El middleware personalizado añade automáticamente estos headers:
- `Access-Control-Allow-Origin: *`
- `Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS`
- `Access-Control-Allow-Headers: X-Requested-With, Content-Type, X-Token-Auth, Authorization`
- `Access-Control-Allow-Credentials: true`

## 🔐 Para Autenticación con Sanctum

Si usas tokens de Sanctum, asegúrate de incluir el token en el header:
```javascript
const token = 'tu-token-aqui';

fetch('http://localhost:8000/api/user', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})
```

## 🚨 Seguridad en Producción

**IMPORTANTE:** En producción, cambia estas configuraciones:

1. En `config/cors.php`:
```php
'allowed_origins' => [
    'https://tu-dominio-frontend.com',
    'https://otro-dominio-permitido.com'
],
```

2. En `.env`:
```env
SANCTUM_STATEFUL_DOMAINS=tu-dominio-frontend.com,otro-dominio.com
```

## 🐛 Solución de Problemas

### Si aún tienes problemas:

1. **Limpia el cache:**
```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

2. **Verifica que el servidor esté corriendo:**
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

3. **Revisa los logs:**
```bash
tail -f storage/logs/laravel.log
```

### Errores comunes:
- ❌ `blocked by CORS policy`: Verifica que el origen esté permitido
- ❌ `credentials mode is 'include'`: Asegúrate de que `supports_credentials` sea `true`
- ❌ `preflight request doesn't pass`: El middleware personalizado maneja las peticiones OPTIONS

## 📝 Notas Adicionales

- El servidor está configurado para aceptar conexiones desde cualquier IP (`0.0.0.0`)
- Los cambios se aplicaron automáticamente sin reiniciar
- La configuración es compatible con Laravel 12
- Se mantiene la compatibilidad con Jetstream y Sanctum
