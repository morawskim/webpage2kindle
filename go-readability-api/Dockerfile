FROM cgr.dev/chainguard/go:latest as build
USER root

WORKDIR /work

COPY . .
RUN CGO_ENABLED=0 go build -o go-readability-api .

FROM cgr.dev/chainguard/static:latest
COPY --from=build /work/go-readability-api /go-readability-api
ENTRYPOINT ["/go-readability-api"]
