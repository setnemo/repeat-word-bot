FROM php:8.0.2-alpine3.13

# Copy the application code
COPY . /app

RUN apk update && apk add --no-cache \
    build-base shadow vim curl supervisor \
    gcc musl-dev make libmcrypt-dev libmcrypt libzip-dev \
    php8 \
    php8-fpm \
    php8-common \
    php8-pdo \
    php8-pdo_mysql \
    php8-mysqli \
    php8-mbstring \
    php8-xml \
    php8-simplexml \
    php8-openssl \
    php8-json \
    php8-phar \
    php8-zip \
    php8-gd \
    php8-dom \
    php8-session \
    php8-zlib \
    php8-redis \
    php8-session \
    php8-pear \
    php8-dev

RUN apk add libpng libpng-dev libjpeg-turbo-dev libwebp-dev zlib-dev libxpm-dev gd
RUN docker-php-ext-install gd

RUN pecl install mcrypt-1.0.4

# Add and Enable PHP Extenstions
RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-enable pdo_mysql



# Redis
RUN apk add --no-cache pcre-dev $PHPIZE_DEPS \
        && pecl install redis \
        && docker-php-ext-enable redis.so

# Install PHP Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Remove Cache
RUN rm -rf /var/cache/apk/*

# setup supervisor
ADD docker/supervisor/repeat-bot.conf /etc/supervisor/conf.d/repeat-bot.conf
ADD docker/supervisor/cleaner.conf /etc/supervisor/conf.d/cleaner.conf
ADD docker/supervisor/supervisord.conf /etc/supervisord.conf

VOLUME ["/app"]

WORKDIR /app

RUN composer install

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
