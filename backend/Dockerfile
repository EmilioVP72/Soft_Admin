FROM php:8.4-fpm-alpine3.21
RUN apk add --no-cache bash git curl unzip libzip-dev postgresql-dev icu-dev sudo nodejs npm chromium nss freetype harfbuzz ca-certificates ttf-freefont \
    freetype-dev libjpeg-turbo-dev libpng-dev libwebp-dev
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp
RUN docker-php-ext-install pdo pdo_pgsql zip intl gd
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN addgroup -g 1000 devteam && adduser -u 1000 -G devteam -s /bin/sh -D emilio
RUN echo "emilio ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers
WORKDIR /app
RUN mkdir -p /app/vendor /app/storage /app/bootstrap/cache && \
    chown -R emilio:devteam /app && \
    chmod -R 775 /app/storage /app/bootstrap/cache
USER emilio
COPY --chown=emilio:devteam . .
EXPOSE 8000