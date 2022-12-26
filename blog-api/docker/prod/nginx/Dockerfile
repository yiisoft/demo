FROM nginx:1.21.1-alpine

RUN apk add --no-cache curl

COPY docker/prod/nginx/nginx.conf /etc/nginx/conf.d/default.conf

WORKDIR /app/public

HEALTHCHECK --interval=30s --timeout=5s --start-period=1s CMD curl --fail http://127.0.0.1/health || exit 1
