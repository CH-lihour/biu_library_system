# App image built from the official PHP image (Docker Hub) instead of Sail's runtime,
# which depends on the ondrej/php Launchpad PPA and breaks whenever that PPA is down.
FROM php:8.4-cli

ARG WWWUSER=1000
ARG WWWGROUP=1000

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git unzip curl ca-certificates \
        libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev libicu-dev libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql mbstring zip bcmath intl gd exif pcntl \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY --from=node:22-bookworm-slim /usr/local/bin/node /usr/local/bin/node
COPY --from=node:22-bookworm-slim /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

RUN groupadd --force -g "$WWWGROUP" sail \
    && useradd -m -s /bin/bash -g "$WWWGROUP" -u "$WWWUSER" sail

WORKDIR /var/www/html
USER sail

EXPOSE 8000

CMD ["bash", "/var/www/html/docker/app/start.sh"]
