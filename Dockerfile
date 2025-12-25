FROM webdevops/php-apache:7.4

ENV WEB_DOCUMENT_ROOT=/app

COPY . /app

RUN chown -R application:application /app

EXPOSE 80
