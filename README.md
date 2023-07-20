A simple bank transaction management API system by using Laravel.

## Running Locally
This project is built with Laravel Framework 10.10 and would require [PHP](https://php.net) 8.1+ to run smoothly.

1. Open your terminal and `cd` to any directory of your choice
    ```bash
    cd your-directory
   ```
2. Clone this repository
    ```bash
    git clone https://github.com/sasani72/banking-system.git
    ```
3. `cd` into the folder created for cloned project:
    ```bash
    cd banking-system
   ```

4. Install packages with composer (Make sure composer is installed already)
    ```bash
    composer install
   ```

5. Make a copy of .env.example as .env
    ```bash
    cp .env.example .env
   ```

6. Generate app key
    ```bash
    php artisan key:generate
   ```

7. Create an empty database and add the database credentials to `.env` file
    ```angular2html
        DB_HOST=localhost
        DB_DATABASE=your_database_name
        DB_USERNAME=root
        DB_PASSWORD=your_password
   ```

8. Run migration and seed the database
   ```bash
   php artisan migrate
   php artisan db:seed
   ```
9. Start Laravel local server
   ```bash
    php artisan serve
    ```
10. You can now use login endpoint with user "admin@banking.com", password "admin@1234!"

## Run Tests
Run application tests
   ```bash
    php artisan test
   ```
## License

Licensed under the [MIT license](https://opensource.org/licenses/MIT).