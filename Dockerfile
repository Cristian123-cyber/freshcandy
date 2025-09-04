FROM php:8.2-apache

# Instalar extensiones PHP necesarias
RUN docker-php-ext-install mysqli pdo pdo_mysql && \
    docker-php-ext-enable mysqli

# Habilitar mod_rewrite para Apache
RUN a2enmod rewrite

# Configurar zona horaria
RUN apt-get update && apt-get install -y tzdata
ENV TZ=America/Bogota
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Configurar zona horaria en PHP
RUN echo "date.timezone = America/Bogota" > /usr/local/etc/php/conf.d/timezone.ini

# Configurar el directorio de trabajo
WORKDIR /var/www/html