FROM php:8.3-apache

ENV TZ=America/Fortaleza

# Copia config, fontes do GLPI e o init.sh
COPY config/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY glpi /opt/glpi-src
COPY logos /opt/glpi-src/pics/logos
COPY init.sh /init.sh

# Instala dependências e configura
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
    && cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini \
    && sed -i '/^;error_log = php_errors.log$/s/^;//' /usr/local/etc/php/php.ini \
    && sed -i 's/^display_errors = Off/display_errors = On/' /usr/local/etc/php/php.ini \
    && sed -i 's/^error_reporting = .*/error_reporting = E_ALL/' /usr/local/etc/php/php.ini \
    && sed -i 's/^;date.timezone =/date.timezone = America\/Fortaleza/' /usr/local/etc/php/php.ini \
    && sed -i 's/^session.cookie_httponly =/session.cookie_httponly = On/' /usr/local/etc/php/php.ini \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && echo "Define DUMMY_CONNECTION 0" >> /etc/apache2/apache2.conf \
    && chmod +x /init.sh

WORKDIR /var/www/html

EXPOSE 80

ENTRYPOINT ["/init.sh"]