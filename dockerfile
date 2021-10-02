FROM php:7.4-cli

RUN apt-get update 
RUN apt-get install -y git
COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN composer global require "phpunit/phpunit"
ENV PATH /root/.composer/vendor/bin:$PATH
RUN ln -s /root/.composer/vendor/bin/phpunit /usr/bin/phpunit