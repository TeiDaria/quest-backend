FROM php:8.4-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends procps \
    && docker-php-ext-install pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# корень сайта теперь в папке /public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# mod_rewrite для ЧПУ
RUN a2enmod rewrite

RUN sed -i 's|AllowOverride None|AllowOverride All|g' /etc/apache2/apache2.conf
COPY . /var/www/html/