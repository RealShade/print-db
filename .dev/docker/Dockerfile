FROM debian:12

RUN apt-get update && apt-get install -y apt-utils
RUN apt-get install -y \
  rsyslog \
  ca-certificates \
  locales \
  lsb-release \
  debian-archive-keyring \
  curl \
  apt-transport-https \
  gpg \
  wget \
  ssh

RUN curl -fsSL https://deb.nodesource.com/setup_lts.x \
  | bash -

RUN echo "uk_UA.UTF-8 UTF-8" >> /etc/locale.gen \
    && locale-gen \
    && update-locale LANG=uk_UA.UTF-8 LC_ALL=uk_UA.UTF-8 \
    && echo 'LANG=uk_UA.UTF-8' >> /etc/environment \
    && echo 'LC_ALL=uk_UA.UTF-8' >> /etc/environment

RUN wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
RUN echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" \
  | tee /etc/apt/sources.list.d/php.list

RUN apt-get update && apt-get install -y \
  nodejs \
  build-essential \
  mc \
  openssl \
  nginx-full \
  gettext \
  imagemagick

RUN apt-get install -y \
  php8.3-fpm \
  php8.3-imagick \
  php8.3-curl \
  php8.3-mbstring \
  php8.3-mysql \
  php8.3-gd \
  php8.3-igbinary \
  php8.3-readline \
  php8.3-soap \
  php8.3-xml \
  php8.3-mysqli \
  php8.3-bcmath \
  php8.3-gmp \
  php8.3-zip \
  php8.3-xdebug \
  php8.3-mongodb \
  php8.3-redis \
  composer \
  ssl-cert \
  net-tools \
  redis

RUN apt-get install -y \
  mariadb-server \
  mariadb-client \
  bindfs \
  tmux \
  cron

ENV PHP_IDE_CONFIG=dev-print-db.realshade.me
ENV LANG=uk_UA.UTF-8 \
    LC_ALL=uk_UA.UTF-8

RUN useradd -ms /bin/bash inneruser
###RUN usermod -u 1000 www-data
###RUN groupmod -g 1000 www-data
#RUN chown -R 1000:1000 /var/www
RUN mkdir -p /mnt/external/vhosts
RUN mkdir -p /var/www/vhosts
RUN mkdir -p /var/log/php
RUN chown -R www-data:www-data /var/log/php
RUN mkdir -p /mnt/external/log/nginx
RUN chown -R inneruser: /mnt/external/

# Копируем ваш CA-сертификат в контейнер
COPY ./config/ssl/realshademeCA.pem /usr/local/share/ca-certificates/realshademeCA.crt

# Обновляем список доверенных сертификатов
RUN update-ca-certificates

CMD ["sleep", "infinity"]
