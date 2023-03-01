# ./docker/php/Dockerfile
FROM php:8.0-fpm

WORKDIR /usr/src/skills

RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    zlib1g-dev \
    zip \
    libzip-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && docker-php-ext-install zip \
    && docker-php-ext-install sockets

RUN rm -rf /var/lib/apt/lists/*

# xdebug
RUN pecl channel-update pecl.php.net \
    && pecl install xdebug \
    && pecl install apcu \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-enable apcu

ENV PHP_IDE_CONFIG 'serverName=php-skills-xdebug'
RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

COPY php.ini /usr/local/etc/php/

RUN echo "$(curl -sS https://composer.github.io/installer.sig) -" > composer-setup.php.sig \
    && curl -sS https://getcomposer.org/installer | tee composer-setup.php | sha384sum -c composer-setup.php.sig \
    && php composer-setup.php && rm composer-setup.php* \
    && chmod +x composer.phar && mv composer.phar /usr/bin/composer

RUN PATH=$PATH:/usr/src/apps/vendor/bin:bin
