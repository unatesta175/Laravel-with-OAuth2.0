#!/bin/bash

echo "Setting up Laravel Passport with HTTP-only Cookies..."

# 1. Install dependencies
echo "Installing Passport dependencies..."
composer install

# 2. Run migrations (Passport needs its tables)
echo "Running Passport migrations..."
php artisan migrate

# 3. Install Passport
echo "Installing Passport keys..."
php artisan passport:install --force

# 4. Cache routes and config
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache

echo "Laravel Passport setup complete!"
echo ""
echo "Next steps:"
echo "1. Update your frontend to use the new authentication endpoints"
echo "2. Test authentication with HTTP-only cookies"
echo "3. Remove any JWT-related configuration files"
