#!/bin/bash
set -e

echo "──────────────────────────────────────────"
echo " LinceB - Iniciando aplicación..."
echo "──────────────────────────────────────────"

# Esperar a que MySQL esté listo
echo "[1/5] Esperando conexión a la base de datos..."
until php artisan db:monitor > /dev/null 2>&1; do
    echo "  Base de datos no disponible, reintentando en 2s..."
    sleep 2
done
echo "  ✓ Base de datos conectada"

# Ejecutar migraciones
echo "[2/5] Ejecutando migraciones..."
php artisan migrate --force
echo "  ✓ Migraciones completadas"

# Crear enlace simbólico de storage
echo "[3/5] Creando enlace de storage..."
php artisan storage:link --force 2>/dev/null || true
echo "  ✓ Storage enlazado"

# Optimizar para producción
echo "[4/5] Optimizando para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "  ✓ Caché generada"

echo "[5/5] Iniciando PHP-FPM..."
echo "──────────────────────────────────────────"
echo " ✓ Aplicación lista"
echo "──────────────────────────────────────────"

exec php-fpm
