FROM php:7.4.16-cli

RUN apt-get update -y && apt-get install -y libmcrypt-dev libonig-dev

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install pdo mbstring pdo_mysql

WORKDIR /app
COPY . /app

RUN composer install --no-plugins --no-scripts

EXPOSE 8000
CMD php bin/console server:run 0.0.0.0:8000
