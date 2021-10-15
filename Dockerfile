# - php (8), extensions, fpm, nginx (redis)
# - user permissions
FROM php:8.0-fpm-alpine

LABEL description="Weathy app on Cloud Run"

# define arguments
ARG PORT=8080
ARG UID=1000
ARG USER=www-data

ENV build_deps  \
		autoconf \
        icu-dev \
        libintl \
        libzip-dev \        
        libxml2-dev \
        libffi-dev \
		libgcrypt-dev \
		libjpeg-turbo-dev \
		libmcrypt-dev \
		libpng-dev \
        libressl-dev \
		libxslt-dev \
        linux-headers \
        curl-dev \
        musl-dev \
        sqlite-dev \
        oniguruma-dev 

ENV persistent_deps  \
        apk-cron \
        bash \
		build-base \
		unzip \
        curl \
        ca-certificates \
        g++ \
        gettext \
        git \
        gcc \
        libcurl \
        make \
        mysql-client \
        nginx \
        php-xml \
        php-zip \        
        rsync \
        supervisor \
        su-exec \
		wget

# Install build dependencies
RUN apk upgrade && apk update && \ 
    apk add --no-cache --virtual .build-dependencies $build_deps

# Install persistent
RUN apk add --update --no-cache --virtual .persistent-dependencies $persistent_deps \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install extensions and remove build deps
RUN apk update \
    && docker-php-ext-install mysqli \
        exif \
        opcache \
        pdo_mysql \
        pdo_sqlite \
        zip \
    && docker-php-ext-configure zip \
    && docker-php-source delete \
    && apk del -f .build-dependencies    

EXPOSE ${PORT}