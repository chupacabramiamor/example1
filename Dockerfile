FROM richarvey/nginx-php-fpm

ARG PUID=1000
ARG PGID=1000

RUN /usr/sbin/addgroup -g $PGID mygroup && /usr/sbin/adduser --disabled-password --shell /bin/bash --ingroup mygroup --uid $PUID myuser

RUN apk update && \
    apk add curl npm yarn && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN echo "104.16.25.35 registry.npmjs.org" | tee -a /etc/hosts &&\
    npm install -g laravel-echo-server

COPY nginx.conf /etc/nginx/sites-available/default.conf

WORKDIR "/app/htdocs"
