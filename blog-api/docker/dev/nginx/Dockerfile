FROM nginx:1.26.3-alpine

RUN apk add --no-cache curl

WORKDIR /app/public

COPY docker/dev/nginx/nginx.conf /etc/nginx/conf.d/default.conf
COPY public ./

