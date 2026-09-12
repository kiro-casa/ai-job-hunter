# Use PHP 8.2 CLI image
FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip gnupg

# Install Node.js 18 (Required for React 19/Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
RUN apt-get install -y nodejs

# Install PHP extensions needed for Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy all project files
COPY . .

# 1. Install Laravel dependencies (skip scripts to prevent artisan crash without .env)
WORKDIR /app/backend
RUN composer install --no-dev --optimize-autoloader --no-scripts

# 2. Create a dummy .env and generate key so artisan doesn't crash later
RUN cp .env.example .env || echo "APP_NAME=Laravel" > .env
RUN php artisan key:generate --force

# 3. Now run the scripts safely
RUN composer dump-autoload

# 4. Build React frontend and copy to Laravel public folder
WORKDIR /app/frontend
RUN npm install && npm run build
RUN cp -r dist/* ../backend/public/

# Set working directory back to backend
WORKDIR /app/backend

# Expose port 10000 (Render's default for Docker)
EXPOSE 10000

# Run migrations and start the server
CMD ["sh", "-c", "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=10000"]