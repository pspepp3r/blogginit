# docker/Dockerfile (Unified for Apache + PHP using mod_php)
# --------------------------------------------------------------------------
FROM php:8.3-apache

ARG USER
ARG USER_ID
ARG GROUP_ID

WORKDIR /var/www

# Install any necessary PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    curl \
    vim \
    libicu-dev

RUN curl -sL https://deb.nodesource.com/setup_18.x | bash \
    && apt-get install nodejs -y

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo pdo_mysql intl

RUN pecl install xdebug \
    && pecl install redis \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-enable redis
    

# Copy Composer if you use it
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Copy your custom Apache configuration from its new location
COPY apache/httpd.conf /etc/apache2/sites-enabled/000-default.conf

# Copy your entire PHP application code into the container.
# This 'COPY .' still refers to the build context, which will be 'my-php-app/'
COPY . /var/www/html

# Apache's default user is 'www-data'. Set ownership to that user/group for the mounted volume.
RUN chown -R www-data:www-data /var/www


# If you have a 'composer.json' file, install dependencies here (optional)
# RUN composer install --no-dev --optimize-autoloader

EXPOSE 80

RUN groupadd --force -g $GROUP_ID $USER
RUN useradd -ms /bin/bash --no-user-group -g $GROUP_ID -u 1337 $USER
RUN usermod -u $USER_ID $USER

USER $USER
