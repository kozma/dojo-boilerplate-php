FROM php:7.4-cli-alpine

RUN curl -Ss https://getcomposer.org/installer | php && \
    mv composer.phar /usr/bin/composer

ADD . /app
VOLUME /app
WORKDIR /app
