# ========================================================
# Stage 1: Builder (Composer Dependencies)
# ========================================================
FROM composer:2.8.5 AS builder

WORKDIR /app

# Copy dependency definitions first for Docker layer caching
COPY composer.json composer.lock ./

# Install production dependencies without dev tools and scripts
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

# Copy application source code
COPY . .

# Generate optimized autoload files
RUN composer dump-autoload --optimize --no-interaction

# ========================================================
# Stage 2: Runtime (FrankenPHP Application Server)
# ========================================================
FROM dunglas/frankenphp:1.4-php8.2-alpine AS runtime

WORKDIR /app

# Install necessary PHP extensions for Laravel & MySQL
RUN install-php-extensions pdo_mysql bcmath opcache pcntl

# Copy application and production dependencies from builder
COPY --from=builder /app /app

# Copy container entrypoint script
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Set directory permissions for Laravel writable directories
RUN mkdir -p /app/storage /app/bootstrap/cache && \
    chmod -R 775 /app/storage /app/bootstrap/cache

# Environment variables
ENV SERVER_NAME=":8000"
ENV APP_ENV="production"
ENV APP_DEBUG="false"
ENV DB_CONNECTION="mysql"
ENV DB_HOST="db"
ENV DB_PORT="3306"
ENV DB_DATABASE="evolusi_pl"
#ENV DB_USERNAME="root"
#ENV DB_PASSWORD="rootpassword"
ENV SESSION_DRIVER="file"
ENV CACHE_STORE="file"
ENV QUEUE_CONNECTION="sync"

EXPOSE 8000

HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
    CMD curl -f http://127.0.0.1:8000/up || exit 1

ENTRYPOINT ["/entrypoint.sh"]