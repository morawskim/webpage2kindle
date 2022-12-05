FROM golang:1.19 as go-readability
RUN go install github.com/go-shiori/go-readability/cmd/go-readability@latest

FROM php:8.1-fpm
ARG WITH_XDEBUG=0
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions apcu sockets
COPY --from=go-readability /go/bin/go-readability /usr/local/bin/
COPY --chown=www-data:www-data . /app
RUN test $WITH_XDEBUG -eq 1 && install-php-extensions xdebug-^3.1 || true

USER www-data
RUN /app/bin/console broadway:event-store:create
