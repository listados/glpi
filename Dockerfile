FROM php:8.3-apache

ENV TZ=America/Fortaleza

# Copia o VirtualHost customizado e os arquivos do GLPI
COPY config/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY glpi /var/www/html
COPY logos /var/www/html/pics/logos

RUN apt-get update && apt-get upgrade -y && apt-get install -y \
        cron \
        zlib1g \
        libzip-dev \
        libpng-dev \
        libicu-dev \
        libldb-dev \
        libldap2-dev \
        libbz2-dev \
        libssl-dev \
    && ln -s /usr/lib/x86_64-linux-gnu/libldap.so /usr/lib/libldap.so \
    && ln -s /usr/lib/x86_64-linux-gnu/liblber.so /usr/lib/liblber.so \
    && docker-php-ext-install mysqli pdo pdo_mysql exif gd intl ldap opcache zip bz2 phar \
    && a2enmod rewrite \
    && a2ensite 000-default.conf \
    && sed -i 's/session.cookie_httponly =/session.cookie_httponly = On/' /usr/local/etc/php/php.ini-production \
    && sed -i 's/;date.timezone =/date.timezone = America\/Fortaleza/' /usr/local/etc/php/php.ini-production \
    && cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini \
    && chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
