# 🎁 Giftbox – PHP Console Project with Eloquent ORM

This project was developed as part of a **university assignment at IUT Nancy-Charlemagne** for the PHP/Eloquent TD series (TD1).

It is a **console-based PHP application** that interacts with a MySQL database using **Eloquent ORM**, simulating the core features of a gift box management system.

---

## 📚 Project Context

- 📍 Institution: **IUT Nancy-Charlemagne**
- 🧑‍🏫 Module: Advanced PHP – Object-Oriented Programming & ORM
- 📘 TD: **TD1 – Modeling and querying with Eloquent**
- 🧑‍💻 DeveloperS: Nathan, Robin, Paul

---

## 🚀 Installation and Project Startup

### Prerequisites
- **PHP** (version 7.4 or higher)
- **Composer** (PHP dependency manager)
- **MySQL** (or another database compatible with Eloquent)

### Installation Steps

1. **Clone the repository**  
   Clone this project to your local machine:  
   ```bash  
   git clone https://github.com/vraiSlophil/giftbox.git  
   cd giftbox  
   ```

2. **Install dependencies**  
   Use Composer to install the required libraries:  
   ```bash  
   composer install  
   ```

3. **Configure the database**
    - Create a MySQL database.
    - Update the `gift.appli/conf/conf.ini` file with your database connection details:  
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

4. **Initialize Eloquent**  
   Ensure the database is properly configured and the required tables are created.

5. **Start the server**  
   Launch the built-in PHP server with the following command:  
   ```bash  
   php -S localhost:8000 -t gift.appli/public/  
   ```
   
## Execute the tests
## Running Tests via Command Line
To run all tests, use the following command in your terminal:
```bash
docker-compose exec giftbox-php php vendor/bin/phpunit
```
### Running a Specific Test File
If you want to run only a specific test file:
```bash
docker-compose exec giftbox-php php vendor/bin/phpunit --bootstrap /var/www/tests/bootstrap.php /var/www/tests/TestName.php
```
For example:
````bash
docker-compose exec giftbox-php php vendor/bin/phpunit --bootstrap /var/www/tests/bootstrap.php /var/www/tests/AuthMiddlewareTest.php
````
### Running a Specific Test Method
If you want to run only a specific test method:
```bash
docker-compose exec giftbox-php php vendor/bin/phpunit --bootstrap /var/www/tests/bootstrap.php --filter=methodName /var/www/tests/TestName.php
````
For example:
```bash
docker-compose exec giftbox-php php vendor/bin/phpunit --bootstrap /var/www/tests/bootstrap.php --filter=testProcessWithAuthenticatedUser /var/www/tests/AuthMiddlewareTest.php
```

6. **Access the application**  
   Open your browser and navigate to [http://localhost:8000](http://localhost:8000).

### 📄 Notes
If you encounter errors, ensure all dependencies are installed and the database is correctly configured.

## 📄 Author & Acknowledgments

    Developed by Nathan (Slophil), Robin (CaretteRobin), Paul (paul5400)

    Supervised as part of coursework at IUT Nancy-Charlemagne

    Project base inspired by academic exercises from TD1

--- 

## 📦 License

This project is for educational purposes and should not be used in production. No license is provided.

---
