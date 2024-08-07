#
# Use this dockerfile to run the application.
#
# Start the server using docker-compose:
#
#   docker-compose build
#   docker-compose up
#
# NOTE: In future examples replace {{volume_name}} with your projects desired volume name
#
# You can install dependencies via the container:
#
#   docker-compose run {{volume_name}} composer install
#
# You can manipulate dev mode from the container:
#
#   docker-compose run {{volume_name}} composer development-enable
#   docker-compose run {{volume_name}} composer development-disable
#   docker-compose run {{volume_name}} composer development-status
#
# OR use plain old docker
#
#   docker build -f Dockerfile-dev -t {{volume_name}} .
#   docker run -it -p "8080:80" -v $PWD:/var/www {{volume_name}}
#
FROM php:8.1-apache

# system dependecies
RUN apt-get update \
 && apt-get remove -y mariadb-server mariadb-client \
 && apt-get install -y \
 git \
 libssl-dev \
 default-mysql-client \
 gnupg2 \
 libmcrypt-dev \
 libicu-dev \
 libpq-dev \
 libjpeg62-turbo-dev \
 libjpeg-dev  \
 libpng-dev \
 zlib1g-dev \
 libonig-dev \
 libxml2-dev \
 libzip-dev \
 unzip

ENV ACCEPT_EULA=Y 

RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add -
RUN curl https://packages.microsoft.com/config/debian/11/prod.list > /etc/apt/sources.list.d/mssql-release.list
# Update package lists and install dependencies
RUN apt-get update && apt-get install -y \
    unixodbc-dev \
    unixodbc \
    msodbcsql17

# Install pdo_sqlsrv extension
RUN pecl install sqlsrv pdo_sqlsrv

# PHP dependencies
RUN docker-php-ext-install \
 gd \
 intl \
 mbstring \
 pdo \
 pdo_mysql \
 mysqli \
 zip

RUN docker-php-ext-enable sqlsrv pdo_sqlsrv

# Xdebug
RUN pecl install xdebug \
 && docker-php-ext-enable xdebug \
 && echo 'xdebug.remote_autostart=0' >> /usr/local/etc/php/conf.d/xdebug.ini \
 && echo 'xdebug.remote_enable=1' >> /usr/local/etc/php/conf.d/xdebug.ini \
 && echo 'xdebug.remote_host=host.docker.internal' >> /usr/local/etc/php/conf.d/xdebug.ini \
 && echo 'xdebug.remote_port=9000' >>  /usr/local/etc/php/conf.d/xdebug.ini \
 && echo 'xdebug.remote_cookie_expire_time=36000' >>  /usr/local/etc/php/conf.d/xdebug.ini

# Apache
RUN a2enmod rewrite \
 && echo "ServerName docker" >> /etc/apache2/apache2.conf

# Composer
RUN curl -sS https://getcomposer.org/installer | php && \
 mv composer.phar /usr/local/bin/composer

WORKDIR /var/www/public
