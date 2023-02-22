## Dependencies
- Laravel 9
- PHP 8.0.27
- MYSQL 8.0.28

## Details
- This project has been built using Laravel 9. 
- Laravel Passport has been used for authentication
- Laravel Excel has been used to import and read the excel files
- Two roles (Admin, Customer) has been created. There is not admin user functionality as of now. Every customer we import from the excel, has been given the role of "Customer". When importing, a default
- While running the Customer and Product importer, the Logs will be created in Laravel Log

## Payment
- There are 3 payment methods available now
- SuperPay : This is the default payment gateway. If its successfull, then order will be saved. But for failure, we will returnt he failure reason and ask the user to use another payment mode
- COD : This payment methods can not fail. If the oreder is placed via COD, the order will be placed successfully
- RazorPay : This payment method will always faile with an error message "Payment setup is not complete"
- These are all dummy payment methods, and does not have the logic of actual payment where we redirect them to the bank. The payment flow is not maintained here as well.



## Installing
- Take a pull of the project from [github](https://github.com/pallabmandal/testorder)
- Go inside the project folder
- Copy the .env.example to .env
- Set up the database credentials in the .env file
- Set up the email credentials in the .env file (I am using mailtrap)
- run ``` composer install ```
- run ``` php artisan migrate ```
- Create the default Role from seeder ```  php artisan db:seed --class=RoleSeeder  ```
- run ``` php artisan passport:install ```
- Import customers. ``` php artisan import:customers ```
- Import Products. ``` php artisan import:products ```
- run ``` php artisan serve ``` this will start the server on localhost port 8000

## API
- The postman environment will be found [HERE](https://drive.google.com/file/d/19f3VsGaMlOGzpSiOxBp5Tbv84qHaNH-p/view?usp=sharing)
- The postman collection will be found [HERE](https://drive.google.com/file/d/1n8rla8jHysxUbRjrtKqBpP7oj2qXr5Jt/view?usp=sharing)