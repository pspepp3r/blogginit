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

# Install Node.js
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo pdo_mysql intl

RUN pecl install xdebug \
    && pecl install redis \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-enable redis
    

# Copy Composer if you use it
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY . /var/www

# Copy your custom Apache configuration from its new location
COPY ./docker/apache.conf /etc/apache2/sites-enabled/000-default.conf

RUN composer install --no-dev --optimize-autoloader --no-scripts --prefer-dist

# RUN npm ci --production
RUN npm install

RUN php blogginit app:generate-key

RUN php blogginit migrations:diff

RUN php blogginit migrations:migrate

# Create group if it doesn't exist, using --gid for ID. '|| true' prevents build failure if it exists.
RUN getent group $GROUP_ID || groupadd --gid $GROUP_ID $USER

# Create user if it doesn't exist, using --uid for ID.
# Add to the created group and also to the www-data group (-G www-data).
# '|| true' prevents build failure if user already exists.
RUN getent passwd $USER_ID || useradd -ms /bin/bash --uid $USER_ID --gid $GROUP_ID -G www-data $USER

# Set ownership of the application directory to www-data.
# Apache runs as www-data, so it needs to own/have write access to these files
# (e.g., for caches, logs, uploads). This applies to the *image's* files.
RUN chown -R www-data:www-data /var/www

# --- END User and Permissions Setup ---
