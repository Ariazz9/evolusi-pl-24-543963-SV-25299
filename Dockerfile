# =========================
# Stage 1: Builder
# =========================
FROM php:8.2.29-cli-alpine AS builder

WORKDIR /var/www

RUN apk add --no-cache \
        git \
        unzip \
        curl \
    && docker-php-ext-install pdo_mysql bcmath

# Pin Composer version instead of using the floating "2" tag
COPY --from=composer:2.8.12 /usr/bin/composer /usr/bin/composer

# Copy dependency definitions first for Docker layer caching
COPY composer.json composer.lock ./

# Install production dependencies
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

# Copy application source after dependencies
COPY . .

# Generate optimized autoload files
RUN composer dump-autoload --optimize --no-interaction

## =========================
# Stage 2: Runtime
# =========================
FROM php:8.2.29-cli-alpine AS runtime

# Create non-root user
RUN addgroup -S laravel && \
    adduser -S laravel -G laravel && \
    mkdir -p /var/www && \
    chown laravel:laravel /var/www

WORKDIR /var/www

# Runtime only needs curl and the PHP extensions
# required by the Laravel application.
RUN apk add --no-cache \
        curl \
    && docker-php-ext-install pdo_mysql bcmath

# Copy application and dependencies from builder
# while assigning ownership directly.
COPY --from=builder --chown=laravel:laravel /var/www /var/www

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

# Run application as a non-root user
USER laravel

EXPOSE 8000

HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
    CMD curl -f http://127.0.0.1:8000/ || exit 1

CMD ["sh", "-c", "cp .env.example .env && sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=mysql/' .env && sed -i 's/^DB_HOST=.*/DB_HOST=evolusi-pl-mysql/' .env && sed -i 's/^DB_PORT=.*/DB_PORT=3306/' .env && sed -i 's/^DB_DATABASE=.*/DB_DATABASE=evolusi_pl/' .env && sed -i 's/^DB_USERNAME=.*/DB_USERNAME=root/' .env && sed -i 's/^DB_PASSWORD=.*/DB_PASSWORD=root/' .env && sed -i 's/^SESSION_DRIVER=.*/SESSION_DRIVER=file/' .env && sed -i 's/^CACHE_STORE=.*/CACHE_STORE=file/' .env && sed -i 's/^QUEUE_CONNECTION=.*/QUEUE_CONNECTION=sync/' .env && php artisan key:generate --force && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000"]