FROM php:8.1-cli-alpine

RUN curl -Ss https://getcomposer.org/installer | php && \
    mv composer.phar /usr/bin/composer

ADD . /app
VOLUME /app
WORKDIR /app
