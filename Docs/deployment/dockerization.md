# Dockerzation

Docker is a platform designed to help developers build, share, and run modern applications. We handle the tedious setup, so you can focus on the code.

In this project we will use docker to build source code to container.
![Diagram of the CI-CD pipe line](pipe-line.svg)

In hosting server, we install docker and use this yml script to build:

```dockerfile
    FROM php:7.4-apache

    # Copy virtual host into container
    COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

    # Enable rewrite mode
    RUN a2enmod rewrite

    # Install necessary packages
    RUN apt-get update && \
        apt-get install \
        libzip-dev \
        wget \
        git \
        unzip \
        -y --no-install-recommends

    # Install PHP Extensions
    RUN docker-php-ext-install zip pdo_mysql

    RUN pecl install -o -f xdebug-3.1.5 \
        && docker-php-ext-enable xdebug
    #     && rm -rf /tmp/pear

    # Copy composer installable
    COPY ./install-composer.sh ./

    # Copy php.ini
    COPY ./php.ini /usr/local/etc/php/

    # Cleanup packages and install composer
    RUN apt-get purge -y g++ \
        && apt-get autoremove -y \
        && rm -r /var/lib/apt/lists/* \
        && rm -rf /tmp/* \
        && sh ./install-composer.sh \
        && rm ./install-composer.sh

    # Change the current working directory
    WORKDIR /var/www

    # Change the owner of the container document root
    RUN chown -R www-data:www-data /var/www

    # Start Apache in foreground
    CMD ["apache2-foreground"]
```

And `docker-compose` file to convinent run and control container:

```yml
version: "3.7"

services:
  app:
    build: ./docker
    image: callmedavid/bkphones
    container_name: bkphones
    ports:
      - "8000:80"
    volumes:
      # Mount source-code for development
      - ./:/var/www
    extra_hosts:
      - host.docker.internal:host-gateway
      
  db:
    image: mysql:8
    container_name: mysql
    ports:
      - "3306:3306"
    volumes:
      - mysql-data:/var/lib/mysql
      - ./docker/mysql-config.cnf:/etc/mysql/conf.d/config.cnf
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: php_mvc
      MYSQL_USER: php_mvc
      MYSQL_PASSWORD: php_mvc

volumes:
  mysql-data:
```