FROM php:7.4.16-cli
# FROM ubuntu:20.04

RUN apt-get update -y && apt-get install -y libmcrypt-dev libonig-dev

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# RUN composer config -g repo.packagist composer https://packagist.org
RUN docker-php-ext-install pdo mbstring pdo_mysql

WORKDIR /app
COPY . /app

# RUN sh -c "echo 'precedence ::ffff:0:0/96 100' >> /etc/gai.conf"
RUN composer install --no-plugins --no-scripts
# RUN wget https://get.symfony.com/cli/installer -O - | bash

EXPOSE 8000
CMD php bin/console server:run 0.0.0.0:8000
