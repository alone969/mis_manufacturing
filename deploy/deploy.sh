#!/bin/bash

# MIS Manufacturing - Production Deployment Script
# Run this script on the production server after pulling the latest code

set -e

echo "🚀 Starting MIS Manufacturing deployment..."

APP_DIR="/var/www/mis_manufacturing"
PHP_VERSION="8.3"

# Navigate to app directory
cd $APP_DIR

echo "📥 Pulling latest code..."
git pull origin main

echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "🔧 Setting environment..."
cp .env .env.backup 2>/dev/null || true

# Run migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

echo "🌱 Seeding database (if needed)..."
php artisan db:seed --force 2>/dev/null || true

echo "🧹 Clearing caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "🔗 Linking storage..."
php artisan storage:link 2>/dev/null || true

echo "👥 Setting permissions..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "🔄 Restarting queue workers..."
php artisan queue:restart

echo "⏰ Restarting scheduler..."
# The scheduler runs via cron, which is already configured
# Verify cron is active: crontab -l
# Should have: * * * * * cd /var/www/mis_manufacturing && php artisan schedule:run >> /dev/null 2>&1

echo "🏥 Health check..."
php artisan about 2>/dev/null | head -5

echo ""
echo "✅ Deployment complete!"
echo ""
echo "📋 Post-deployment checklist:"
echo "  1. Verify the site loads: https://mis.example.com"
echo "  2. Check queue workers: php artisan queue:work --status"
echo "  3. Monitor logs: tail -f storage/logs/laravel.log"
echo "  4. Verify cron: crontab -l"
echo ""
