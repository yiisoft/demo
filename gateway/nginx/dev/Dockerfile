FROM nginx:1.21.1-alpine AS nginx

RUN apk add --no-cache curl nano

WORKDIR /app

RUN rm /etc/nginx/conf.d/default.conf
COPY ./dev/templates/*.conf.template /etc/nginx/templates/
