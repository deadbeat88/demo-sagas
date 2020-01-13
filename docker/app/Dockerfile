FROM php:7.3-cli-stretch

RUN set -xe \
    && apt-get update \
    && apt-get install -y --no-install-recommends \
        autoconf \
        dpkg-dev \
        file \
        g++ \
        gcc \
        libc-dev \
        make \
        pkg-config \
        re2c \
        sudo \
        libevent-dev \
        libpq-dev \
        libcurl4-openssl-dev \
        libssl-dev \
        zip \
        unzip \
        zlib1g-dev \
        libzip-dev

# PHP configuration
COPY ./config/php.ini /usr/local/etc/php/
COPY ./config/php.ini /usr/local/etc/php/conf.d/

RUN set -xe \
    && docker-php-source extract \
    && docker-php-ext-install \
        bcmath \
        pcntl \
        sysvsem \
        pgsql \
        pdo_pgsql \
        sockets \
        zip

RUN set -xe \
    # raphf
    && pecl install raphf \
    && docker-php-ext-enable raphf \
    # pq
    && pecl install pq \
    && echo "extension=pq.so" > /usr/local/etc/php/conf.d/pq.ini \
    # event
    && pecl install event \
    && docker-php-ext-enable event

RUN docker-php-source delete && apt-get clean && rm -rf /var/lib/apt/lists/* /var/cache/apk/* /var/tmp/* /tmp/*

# Change www-data user to match the host system UID and GID and chown www directory
ARG userid
RUN mkdir -p /var/www

RUN usermod --non-unique --uid $userid www-data \
  && groupmod --non-unique --gid $userid www-data \
  && chown -R $userid:$userid /var/www

# Composer install
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
   && chmod +x /usr/local/bin/composer \
   && composer global require hirak/prestissimo \
   && composer clear-cache

WORKDIR /var/www

COPY ./tools/* /tools/