FROM php:7.3-apache
EXPOSE 80
WORKDIR /opt/openvpn/

# Expose Mysql port 3306
EXPOSE 3306

#
# 1. Install recommend php extension (pdo_mysql, gd)
RUN apt-get update \
   && apt-get install -y whiptail systemctl net-tools
#  && apt-get install -y libjpeg-dev libfreetype6-dev \
#  && docker-php-ext-configure gd --with-jpeg-dir=/usr/lib --with-freetype-dir=/usr/include/freetype2 \
#  && docker-php-ext-install pdo_mysql gd

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN set -eux; apt-get update; apt-get install -y libzip-dev zlib1g-dev; docker-php-ext-install zip

ENV APACHE_DOCUMENT_ROOT=/srv/www/openvpn-admin/
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

## 5. Start with base PHP config, then add extensions.
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
