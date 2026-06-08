FROM dunglas/frankenphp:1-php8.5-alpine
ENV APP_RUNTIME=Runtime\\FrankenPhpSymfony\\Runtime

ARG WITH_XDEBUG=0
RUN install-php-extensions sockets redis pcntl sodium opentelemetry
RUN test $WITH_XDEBUG -eq 1 && install-php-extensions xdebug-^3.5 || true
COPY Caddyfile /etc/frankenphp/Caddyfile
COPY --chown=www-data:www-data . /app

RUN /app/bin/console broadway:event-store:create
