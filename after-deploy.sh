#!/bin/bash

echo "🚀 Starting Laravel deployment..."

# Clear all caches
APP_ENV=production php artisan cache:clear
APP_ENV=production php artisan config:clear
APP_ENV=production php artisan route:clear
APP_ENV=production php artisan view:clear

# Optimize for production (optional)
# APP_ENV=production php artisan config:cache
# APP_ENV=production php artisan route:cache
# APP_ENV=production php artisan view:cache

# Run migrations if needed
# APP_ENV=production php artisan migrate --force

echo "✅ Deployment tasks completed successfully!"
