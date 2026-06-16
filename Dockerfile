FROM php:8.3-apache

RUN docker-php-ext-install pdo_mysql \
    && a2enmod rewrite headers \
    && echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername

COPY apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY docker/app-entrypoint.sh /usr/local/bin/app-entrypoint
RUN chmod +x /usr/local/bin/app-entrypoint

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

ENTRYPOINT ["app-entrypoint"]
CMD ["apache2-foreground"]
