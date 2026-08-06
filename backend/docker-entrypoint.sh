#!/bin/sh
set -e

echo "⏳ Esperando a que la base de datos MySQL ($DB_HOST:$DB_PORT) esté lista..."
until nc -z -v -w30 "$DB_HOST" "$DB_PORT"; do
  echo "Esperando conexión con MySQL..."
  sleep 2
done

echo "✅ MySQL está listo."

# Generar clave de aplicación si no existe
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generando APP_KEY..."
    php artisan key:generate --force
fi

# Crear estructura de carpetas necesarias en storage y bootstrap
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chmod -R 777 storage bootstrap/cache

# Ejecutar migraciones y datos de prueba (Seeders)
echo "🗄️ Ejecutando migraciones y seeders en la base de datos..."
php artisan migrate --force --seed

# Generar documentación OpenAPI / Swagger
echo "📖 Generando documentación Swagger UI..."
php artisan l5-swagger:generate || true

# Optimizar caché de configuración y rutas si está en producción
if [ "$APP_ENV" = "production" ]; then
    echo "⚡ Optimizando cachés de Laravel para producción (config, routes)..."
    php artisan config:cache
    php artisan route:cache
fi

echo "🚀 Iniciando servidor de Laravel en 0.0.0.0:8000..."
exec php artisan serve --host=0.0.0.0 --port=8000
