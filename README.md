# GreeLiving
Website for GreeLiving. Written in PHP. Created by Group 6 of COS20031.

## Set up instructions

### Step 1 - Download and set up PHP

This project needs PHP to run and you will need a PHP excutable file available on your computer. If you already have XAMPP installed, you can use the `php.exe` file that comes with XAMPP. Otherwise, go to PHP's website on [this link](https://www.php.net/downloads.php) to find a suitable download option. Please make sure that the PHP version is above 8.2 because some packages might not work.

To check that you have a valid version of PHP installed, type `php -v` in the terminal. Note that you may need to add the path to the PHP executable to the system's environment variables so that the terminal can run it.

In the folder containing the `php.exe` file, copy and paste the file `cacert.pem` in the code directory into the directory `extras/ssl` (on the same directory as `php.exe`.) You will need this to run the server.

Afterwards, copy and paste the `php.ini` file in the source code into the directory containing `php.exe`. This file contains important settings to enable our server to work. You may wish to back up other `php.ini` files already present if you plan to use them in the future.

In the `php.ini` file, find the location of the `curl.cainfo` and `openssl.cafile` lines and replace the paths with the **absolute path** to the `cacert.pem` file you added earlier.

### Step 2 - Download Composer and install packages

Composer is a package manager for PHP and is used by this project to install several third-party libraries. To install Composer, check out the official documentation on [this link](https://getcomposer.org/download/). The link has options to install Composer globally, which allows you to use it everywhere on your system, or locally, which restricts Composer to the directory in which it is installed. If you do not plan on using Composer in the future and is only looking to test this project, you can install it locally with:

```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```

After Composer is installed, you need to install the necessary libraries with

```
composer install
```

if you installed Composer **globally**, or

```
php composer.phar install
```

if you installed it **locally**.

You can check the vendor directory to see if the packages have been installed.

### Step 3 - Set up MySQL

The database for this project is stored on MySQL. To download MySQL, refer to the official website at [this link](https://dev.mysql.com/downloads/).

With your MySQL server running, run the `schema.sql` script followed by the `dummydata.sql` script located in the `/sql` directory. These scripts set up the tables and populate them with dummy data.

### Step 4 - Set up the environment variables

This project makes use of several environment variables located in the `.env` file in the code directory. In this file, leave everything unchanged except `DATABASE_USER` and `DATABASE_PASSWORD`. These are the username and password of your MySQL user which are used to connect the website to the database.

### Step 5 - Run the web server

With everything set up correctly, you can run the server by typing this command in the terminal:

```
php -S 127.0.0.1:3000
```

**You must not change this address of the server as it will interfere with authentication.**

Alternatively, if you are on Windows, you can run the `server.bat` file instead of typing the above command for convenience.

```
server
```

## Contact

If you have any questions related to this project, please email me at tunggnut2004@gmail.com.