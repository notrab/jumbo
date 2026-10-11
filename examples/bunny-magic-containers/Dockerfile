# Magic Containers runs linux/amd64 images, so build for that platform:
#   docker build --platform linux/amd64 -t herd-guestbook .
FROM dunglas/frankenphp:php8.3-alpine

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && install-php-extensions gd exif opcache \
    && echo "opcache.validate_timestamps=0" > "$PHP_INI_DIR/conf.d/zz-jumbo.ini"

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Swap the image's welcome page for the site
WORKDIR /app
RUN rm -rf /app/*

# Install dependencies first, so content changes don't reinstall them
COPY composer.json composer.lock* ./
RUN composer install --no-dev --no-progress --no-interaction --optimize-autoloader

COPY . .
COPY Caddyfile /etc/frankenphp/Caddyfile

ENV DUMBO_ENV=production
# Where the guestbook is saved. Mount a persistent volume at /data to keep it.
ENV GUESTBOOK_DIR=/data/guestbook

EXPOSE 8080
