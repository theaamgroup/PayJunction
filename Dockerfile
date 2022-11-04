FROM php:7.4-apache as base

COPY scripts/ /var/www/html
COPY src/ /var/www/src
RUN chown -R 1000:1000 /var/www/src
COPY vendor/ /var/www/vendor
RUN chown -R 1000:1000 /var/www/vendor
USER 1000
