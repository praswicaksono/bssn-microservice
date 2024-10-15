FROM alpine:3.20

ARG timezone

ENV TIMEZONE=${timezone:-"Asia/Jakarta"} \
    APP_ENV=prod \
    SCAN_CACHEABLE=(true)

RUN wget https://dl.static-php.dev/static-php-cli/bulk/php-8.3.11-cli-linux-x86_64.tar.gz \
    && tar zxvf php-8.3.11-cli-linux-x86_64.tar.gz \
    && mv php /usr/local/bin/php \
    && chmod +x /usr/local/bin/php

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# update
RUN set -ex \
    # show php version and extensions
    && php -v \
    && php -m \
    && php --ri swoole \
    #  ---------- some config ----------
#    # - config PHP
    && { \
        echo "upload_max_filesize=128M"; \
        echo "post_max_size=128M"; \
        echo "memory_limit=1G"; \
        echo "date.timezone=${TIMEZONE}"; \
    } | tee lib/php.ini \
    # - config timezone
    && ln -sf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime \
    && echo "${TIMEZONE}" > /etc/timezone \
    # ---------- clear works ----------
    && rm -rf /var/cache/apk/* /tmp/* /usr/share/man \
    && echo -e "\033[42;37m Build Completed :).\033[0m\n"

WORKDIR /opt/www

# Composer Cache
# COPY ./composer.* /opt/www/
# RUN composer install --no-dev --no-scripts

COPY . /opt/www
RUN composer install --no-dev -o

EXPOSE 9501

ENTRYPOINT ["php", "/opt/www/index.php"]
