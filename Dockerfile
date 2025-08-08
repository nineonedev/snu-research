# PHP 7.4 + Apache
FROM php:7.4-apache

# 필요한 패키지 및 PHP 확장 모듈 설치
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    curl \
    git \
    unzip \
    zip \
    npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql

# Apache 모듈 활성화
RUN a2enmod rewrite

# Node.js & npm 최신 버전 설치
RUN curl -fsSL https://deb.nodesource.com/setup_16.x | bash - && \
    apt-get install -y nodejs

# Composer 설치
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 프로젝트 파일을 Apache 루트로 복사
COPY . /var/www/html/

# Node.js 패키지 설치 및 빌드 실행
WORKDIR /var/www/html

# 업로드 폴더 권한 설정
RUN chown -R www-data:www-data /var/www/html/storage && chmod -R 775 /var/www/html/storage

# 문서 루트 설정
WORKDIR /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]
