FROM nginx:1.26.3-alpine AS nginx

RUN apk add --no-cache curl nano

WORKDIR /app

RUN rm /etc/nginx/conf.d/default.conf
#COPY ./prod/templates/https.conf.template /etc/nginx/templates/
COPY ./prod/templates/*.conf.template /etc/nginx/templates/

#CMD ["nginx-debug", "-g", "daemon off;"]

