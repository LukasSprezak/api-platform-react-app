FROM php:8.1-apache

ENV TZ=Europe/Warsaw
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions gd xdebug opcache pdo pdo_mysql mbstring bcmath xml zip intl apcu memcached amqp

RUN apt-get -y update
RUN apt-get install -y git nano curl

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/ --filename=composer

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
RUN apt install symfony-cli

RUN curl -sL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && npm i -g nodemon \
    && npm install -g npm

RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - \
    && echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list \
    && apt update && apt install yarn

COPY vhost.conf /etc/apache2/sites-enabled/000-default.conf
RUN a2enmod rewrite

RUN service apache2 restart
RUN usermod -u 1000 www-data

WORKDIR /var/www