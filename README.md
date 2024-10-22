# Symfony Product Management System

This is a Symfony-based web application for managing products with functionalities such as creating, editing, deleting, and importing/exporting product data.

## Clone the repository
   - git clone https://github.com/Sanyuaung/Symfony.git
   - cd Symfony
   - composer install
## Configuration
**.env**:  - DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name"
    
    ```bash
     php bin/console doctrine:migrations:migrate
    
     php bin/console doctrine:fixtures:load
    
     symfony server:start
