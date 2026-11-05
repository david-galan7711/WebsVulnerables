FROM php:8.2-apache
RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable mysqli
COPY apache-security.conf /etc/apache2/conf-enabled/zz-mordor-security.conf
