FROM php:8.2-fpm
ARG WITH_XDEBUG=0
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions apcu sockets redis
COPY --chown=www-data:www-data . /app
RUN test $WITH_XDEBUG -eq 1 && install-php-extensions xdebug-^3.2 || true

USER www-data
RUN /app/bin/console broadway:event-store:create
