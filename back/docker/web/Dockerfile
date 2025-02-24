FROM php:8.2.27-apache-bookworm

COPY --chown=root:root symfony.conf /etc/apache2/sites-available/symfony.conf

RUN apt-get -qq update && \
    apt-get -qq install git libpq-dev libicu-dev locales libzip-dev libgd-dev libxml2-dev && \
    echo -e "en_US.UTF-8 UTF-8\nfr_FR.UTF-8 UTF-8" >> /etc/locale.gen && locale-gen && \
    mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" && \
    echo "date.timezone = Europe/Paris" > $PHP_INI_DIR/conf.d/date-timezone.ini && \
    echo "session.save_path = /var/sessions" > $PHP_INI_DIR/conf.d/session-save_path.ini && \
    docker-php-ext-install gd intl opcache pdo_pgsql soap zip && \
    rm -fr /var/lib/apt/lists && \
    ln -sf /usr/share/zoneinfo/Europe/Paris /etc/localtime && \
    useradd --uid 1000 --home /var/www user && \
    mkdir -p /var/log/app /var/www/app /var/sessions && a2dissite 000-default && a2ensite symfony && \
    touch /var/log/app/access.log /var/log/app/error.log && \
    chown -R 1000:1000 /var/www /var/sessions && \
    chown -R 1000:1000 /var/log/app && \
    sed -i -e 's/www-data/user/' /etc/apache2/envvars && \
    curl https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

USER 1000
WORKDIR /var/www/app

ENV LANG=fr_FR.UTF-8
ENV LANGUAGE=fr_FR.UTF-8