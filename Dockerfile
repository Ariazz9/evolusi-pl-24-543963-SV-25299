FROM php:8.2-cli-alpine

WORKDIR /var/www

RUN apk add --no-cache \
        git \
        unzip \
        curl \
    && docker-php-ext-install pdo_mysql bcmath

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy dependency definitions first for Docker layer caching
COPY composer.json composer.lock ./

# Install dependencies before copying application source
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

# Copy application source after dependencies
COPY . .

# Generate optimized autoload files after application source is available
RUN composer dump-autoload --optimize --no-interaction

# Use MySQL and file-based drivers inside the container
ENV DB_CONNECTION=mysql
ENV DB_HOST=evolusi-pl-mysql
ENV DB_PORT=3306
ENV DB_DATABASE=evolusi_pl
ENV DB_USERNAME=root
ENV DB_PASSWORD=root

ENV SESSION_DRIVER=file
ENV CACHE_STORE=file
ENV QUEUE_CONNECTION=sync

EXPOSE 8000

CMD ["sh", "-c", "cp .env.example .env && sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=mysql/' .env && sed -i 's/^DB_HOST=.*/DB_HOST=evolusi-pl-mysql/' .env && sed -i 's/^DB_PORT=.*/DB_PORT=3306/' .env && sed -i 's/^DB_DATABASE=.*/DB_DATABASE=evolusi_pl/' .env && sed -i 's/^DB_USERNAME=.*/DB_USERNAME=root/' .env && sed -i 's/^DB_PASSWORD=.*/DB_PASSWORD=root/' .env && sed -i 's/^SESSION_DRIVER=.*/SESSION_DRIVER=file/' .env && sed -i 's/^CACHE_STORE=.*/CACHE_STORE=file/' .env && sed -i 's/^QUEUE_CONNECTION=.*/QUEUE_CONNECTION=sync/' .env && php artisan key:generate --force && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000"]
