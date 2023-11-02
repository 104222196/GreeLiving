# GreeLiving
Website for GreeLiving. Written in PHP.

# Setting up
This project requires Composer to run. Composer is a package manager for PHP that can be downloaded from [the official website](https://getcomposer.org/download/).

The Composer installer will ask for a PHP executable (a.k.a. php.exe) on your system. If you already have XAMPP installed, you can refer it to the executable file found in `/xampp/php/php.exe` (the location of XAMPP will vary based on your past installation.)

On the terminal, `cd` to the project's directory and run `composer install`. This will install all the packages needed for the website to run. To start the development server, type `php -S 127.0.0.1:3000` in the terminal. Please do **NOT** type `localhost:3000` because it will not work with the authentication module.
