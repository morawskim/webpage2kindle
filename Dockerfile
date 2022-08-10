FROM golang:1.14 as go-readability
RUN go get -u -v github.com/go-shiori/go-readability/cmd/...

FROM php:8.1-cli

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions apcu
COPY --from=go-readability /go/bin/go-readability /usr/local/bin/
COPY --chown=nobody:root . /app

USER nobody
RUN /app/bin/console broadway:event-store:create
EXPOSE 4200
CMD ["/usr/local/bin/php", "-S", "0.0.0.0:4200", "-t", "/app/public"]
