FROM php:7.4-cli
RUN apt-get update 
RUN apt-get install -y git zip
COPY --from=composer /usr/bin/composer /usr/bin/composer