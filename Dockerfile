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
## 2. Apache configs + document root.
#RUN echo "ServerName moneff-app.local" >> /etc/apache2/apache2.conf
#
ENV APACHE_DOCUMENT_ROOT=/srv/www/openvpn-admin/
#RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
#RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
#
## 3. mod_rewrite for URL rewrite and mod_headers for .htaccess extra headers like Access-Control-Allow-Origin-
#RUN a2enmod rewrite headers
#
## 4. Settings project
#RUN mkdir -p /var/www/html/application/runtime/ && \
#    mkdir -p  /var/www/html/mail-attachment/ && \
#    mkdir -p /var/www/html/assets/ && \
#    mkdir -p /var/www/html/uploads/ && \
#    chgrp www-data /var/www/html/assets /var/www/html/application/runtime && \
#    chgrp www-data /var/www/html/mail-attachment/ /var/www/html/uploads/ && \
#    chmod g+w /var/www/html/assets/ /var/www/html/application/runtime/ && \
#    chmod g+w /var/www/html/mail-attachment/ /var/www/html/uploads/
##    && chmod u=rw,g=rw,o= -R /var/www/html/assets/ #bug security
#
## 5. Start with base PHP config, then add extensions.
#RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
#
## 6. We need a user with the same UID/GID as the host user
## so when we execute CLI commands, all the host file's permissions and ownership remain intact.
## Otherwise commands from inside the container would create root-owned files and directories.
## ARG uid
## RUN useradd -G www-data,root -u $uid -d /home/devuser devuser
## RUN mkdir -p /home/devuser/.composer && \
##     chown -R devuser:devuser /home/devuser