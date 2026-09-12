# Use PHP 8.4 CLI image to match your composer.lock requirements
FROM php:8.4-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libxml2-dev zip unzip gnupg \
    libcurl4-openssl-dev libzip-dev

# Install Node.js 20 (Required for modern Vite/Rolldown)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
RUN apt-get install -y nodejs

# Configure GD extension with freetype and jpeg libraries
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# Install PHP extensions needed for Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath curl zip gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy all project files
COPY . .

# 1. Install Laravel dependencies 
WORKDIR /app/backend
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --optimize-autoloader --no-scripts

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