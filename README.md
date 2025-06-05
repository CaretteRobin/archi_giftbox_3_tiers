# 🎁 Giftbox – PHP Console Project with Eloquent ORM

This project was developed as part of a **university assignment at IUT Nancy-Charlemagne** for the PHP/Eloquent TD series (TD1).

It is a **console-based PHP application** that interacts with a MySQL database using **Eloquent ORM**, simulating the core features of a gift box management system.

---

## 📚 Project Context

- 📍 Institution: **IUT Nancy-Charlemagne**
- 🧑‍🏫 Module: Advanced PHP – Object-Oriented Programming & ORM
- 📘 TD: **TD1 – Modeling and querying with Eloquent**
- 🧑‍💻 Developers: Nathan, Robin, Paul

---

## 🚀 Installation and Project Startup

### Prerequisites
- **PHP** (version 7.4 or higher)
- **Composer**
- **MySQL** (optional if using Docker)

---

## 🐳 Setup with Docker (Recommended)

### 1. Clone the repository
```bash
git clone https://github.com/vraiSlophil/giftbox.git
cd giftbox
```

### 2. Create `.env` file
At the root of the project, create a `.env` file:
```env
DB_ROOT_PASSWORD=root_password
DB_USERNAME=giftbox
DB_PASSWORD=giftbox
DB_DATABASE=giftbox_db
```

### 3. Build and start Docker
```bash
docker-compose up -d --build
```

Access the app:
- App: [http://localhost:8082](http://localhost:8082)
- Adminer: [http://localhost:8083](http://localhost:8083)
- PhpMyAdmin: [http://localhost:8084](http://localhost:8084)

---

### 📄 docker-compose.yml

```yaml
version: '3.8'

services:
  giftbox-php:
    build:
      context: .
      dockerfile: Dockerfile.gift
    ports:
      - "8082:80"
    volumes:
      - ./gift.appli/public:/var/www/html
      - ./gift.appli/src:/var/www/src
      - ./gift.appli/tests:/var/www/tests
      - ./.env:/var/www/src/.env
    working_dir: /var/www/src
    networks:
      - giftbox.net
    depends_on:
      - giftbox-db

  giftbox-db:
    image: bitnami/mariadb:11.4.6-debian-12-r0
    environment:
      ALLOW_EMPTY_PASSWORD: 0
      MARIADB_ROOT_PASSWORD: ${DB_ROOT_PASSWORD:-root_password}
      MARIADB_USER: ${DB_USERNAME}
      MARIADB_PASSWORD: ${DB_PASSWORD}
      MARIADB_DATABASE: ${DB_DATABASE}
    networks:
      - giftbox.net

  adminer:
    image: adminer
    depends_on:
      - giftbox-db
    environment:
      ADMINER_DEFAULT_SERVER: giftbox-db
      ADMINER_AUTOLOGIN: '1'
    ports:
      - "8083:8080"
    volumes:
      - ./adminer-theme.css:/var/www/html/adminer.css
    networks:
      - giftbox.net

  phpmyadmin:
    image: phpmyadmin:5.2.2
    depends_on:
      - giftbox-db
    environment:
      PMA_HOST: giftbox-db
      PMA_USER: ${DB_USERNAME}
      PMA_PASSWORD: ${DB_PASSWORD}
    ports:
      - "8084:80"
    networks:
      - giftbox.net

networks:
  giftbox.net:
    driver: bridge
```

---

### 📄 Dockerfile.gift

```Dockerfile
FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    zip unzip git curl libonig-dev libzip-dev libpng-dev libxml2-dev mariadb-client \
    && docker-php-ext-install pdo pdo_mysql mbstring zip gd

# Enable Apache rewrite module
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
```

---

## 🛠️ Manual Installation (Without Docker)

### 1. Clone the repository
```bash
git clone https://github.com/vraiSlophil/giftbox.git
cd giftbox
```

### 2. Install PHP dependencies
```bash
composer install
```

### 3. Configure the database
Edit `gift.appli/conf/conf.ini`:

```ini
[database]
driver = mysql
host = 127.0.0.1
database = your_database_name
username = your_username
password = your_password
charset = utf8mb4
collation = utf8mb4_unicode_ci
```

### 4. Start local server
```bash
php -S localhost:8000 -t gift.appli/public/
```

Go to: [http://localhost:8000](http://localhost:8000)

---

## ✅ Running Tests

### Run all tests:
```bash
docker-compose exec giftbox-php php vendor/bin/phpunit
```

### Run a specific test:
```bash
docker-compose exec giftbox-php php vendor/bin/phpunit --bootstrap /var/www/tests/bootstrap.php /var/www/tests/ExampleTest.php
```

### Run a specific test method:
```bash
docker-compose exec giftbox-php php vendor/bin/phpunit --bootstrap /var/www/tests/bootstrap.php --filter=testMethodName /var/www/tests/ExampleTest.php
```

---

## 📄 Author & Acknowledgments

Developed by:
- Nathan (Slophil)
- Robin (CaretteRobin)
- Paul (paul5400)

Supervised as part of coursework at IUT Nancy-Charlemagne

---

