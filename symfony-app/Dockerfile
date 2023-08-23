FROM dunglas/frankenphp:main-php8.2
ENV FRANKENPHP_CONFIG="worker ./public/index.php"
ENV APP_RUNTIME=Runtime\\FrankenPhpSymfony\\Runtime

ARG WITH_XDEBUG=0
RUN install-php-extensions apcu sockets redis
COPY --chown=www-data:www-data . /app
RUN test $WITH_XDEBUG -eq 1 && install-php-extensions xdebug-^3.2 || true

RUN /app/bin/console broadway:event-store:create
