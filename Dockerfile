FROM php:7.2-apache

RUN apt-get update && apt-get install -y libpq-dev \
	&& a2enmod \
	rewrite

COPY ./ /var/www/html