FROM php:7.4-cli
COPY --from=composer /usr/bin/composer /usr/bin/composer