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
        gcc \
        libcurl \
        make \
        mysql-client \
        nginx \
        nodejs \ 
        npm \
        openrc \
        php-xml \
        php-zip \
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

# Override php.ini
COPY ./.docker/php/conf.d/php.ini /usr/local/etc/php/conf.d/zz-custom.ini

# Configure opcache.ini
COPY ./.docker/php/conf.d/opcache.ini /usr/local/etc/php/conf.d/zz-opcache.ini

RUN sed -i \ 
    -e "s/;catch_workers_output\s*=\s*yes/catch_workers_output = yes/g" \
		-e "s/pm.max_children = 5/pm.max_children = 20/g" \
		-e "s/pm.start_servers = 2/pm.start_servers = 3/g" \
		-e "s/pm.min_spare_servers = 1/pm.min_spare_servers = 2/g" \
		-e "s/pm.max_spare_servers = 3/pm.max_spare_servers = 4/g" \
		-e "s/;pm.max_requests = 500/pm.max_requests = 200/g" \
		-e "s/;listen.mode = 0660/listen.mode = 0666/g" \
        -e "s/;request_terminate_timeout = 0/request_terminate_timeout = 600/g" \
        -e "s/;rlimit_core = 0/rlimit_core = unlimited/g" \
        -e "s/;rlimit_files = 1024/rlimit_files = 131072/g" \
		-e "s/^;clear_env = no$/clear_env = no/" \
		/usr/local/etc/php-fpm.d/www.conf


### SETUP NGINX
# Copy Nginx conf files
COPY ./.docker/nginx /etc/nginx

RUN mkdir -p /etc/nginx && \
	mkdir -p /run/nginx && \
	mkdir -p /var/log/supervisor && \
	mkdir -p /etc/nginx/ssl/ && \
	mkdir -p /etc/nginx/vhost.common.d && \
	mkdir -p /etc/nginx/sites-enabled && \
    mkdir -p /etc/nginx/sites-available && \
    rm -rf /etc/nginx/conf.d/default.conf \
    && rm -rf /var/www/html \
    && rm -rf /var/www/localhost \
    && sh -c "envsubst < /etc/nginx/tmpl/template.conf > /etc/nginx/sites-available/default" \
	&& ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default
### EOF NGINX

### SUPERVISOR
COPY ./.docker/supervisor/supervisord.conf /etc/
### EOF SUPERVISOR

# Add entrypoint
COPY ./.docker/entrypoint.sh /opt/entrypoint.sh
RUN chmod -R +x /opt/*

### ACL
# Use UID and USER 
RUN set -x \
    && deluser ${USER} \
    && addgroup -g 82 www-data \
    && adduser -D -H -u ${UID} -s /bin/bash ${USER} -G www-data
### EOF ACL

### BOOT APP
# Copy all content to /var/www
COPY . /var/www

WORKDIR /var/www

# Run Composer
RUN set -xe \
    && composer install --no-dev --no-scripts --no-suggest --no-interaction --prefer-dist --optimize-autoloader \
    && npm install \
    && npm run production \ 
    && rm -rf node_modules \
    && mv .env.production .env
     
# EOF Run Composer

# Chown
RUN chown -R ${USER}:www-data ./* \
    && chmod -R 0755 ./* \
    && chmod -R 0775  ./storage \ 
    && chmod -R 0775  ./bootstrap
### EOF BOOT APP
        
EXPOSE ${PORT}

CMD ["/opt/entrypoint.sh"]