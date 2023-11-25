# GreeLiving
Website for GreeLiving. Written in PHP. Created by Group 6 of COS20031.

## Necessary software
This project requires the installation of PHP, Git, Composer (which is a package manager for PHP), and MySQL.

Download PHP: [PHP download](https://www.php.net/downloads.php)  (if you have XAMPP, you can use the `php.exe` file in `xampp/php/` instead. However, please make sure that the PHP version is above 8.2 because some packages might not work. You can check the version of PHP with `php -v` in the terminal.)

Download Composer: [Composer](https://getcomposer.org/download/) (during installation, please refer to the PHP executable from the previous step.)

Download Git: [Git](https://git-scm.com/downloads) (this is required for the installation of Composer packages).

Download MySQL: [MySQL Community](https://dev.mysql.com/downloads/)

Please check your environment variables to make sure that the paths to the PHP, Composer, and Git executables are present.

## Cloning the project and installing packages

Use Git to clone the project on https://github.com/gnut04/GreeLiving. Then, `cd` into the project directory and run `composer install` to install the necessary packages. You may encounter issues if you have not set up Composer correctly, for instance by not having it in PATH.

## Defining environment variables and creating the database

Copy the contents pertaining to the `.env` file on the project's `README.md` and change the information related to the database as necessary. Most importantly, please specify the password to connect to your MySQL server. If you use XAMPP, the password might not be necessary, so leave it as a blank `''`.

```
AUTH0_BASE_URL='http://127.0.0.1:3000'

AUTH0_APPLICANTS_DOMAIN='https://dev-26j7nd5wbpafvpg2.us.auth0.com'
AUTH0_APPLICANTS_CLIENT_ID='vDNw4iiDOHGHd7BblTusIKJ8V0jiANUx'
AUTH0_APPLICANTS_CLIENT_SECRET='2d9v7-OXT6xx-ux1ZQekw3CwNMtW0mC7Nfh3GGvG7oSLbnsgtkdjeq9BnhKpxFkz'
AUTH0_APPLICANTS_COOKIE_SECRET='c89c4ca664c98b58379aa03d351fb96775379319bdf17e8a446fee724ca30a0e'

AUTH0_COMPANIES_DOMAIN='https://greeliving-employer.us.auth0.com'
AUTH0_COMPANIES_CLIENT_ID='5u20cQ1jCqPBkBmaZq4ErGt3cuZUlXee'
AUTH0_COMPANIES_CLIENT_SECRET='MO_DbmlIHkaMBQzsQHf3-6yw2zZrGtwbNnDGDRmqhNgp-Zm-59FyuffsmeNsHHuy'
AUTH0_COMPANIES_COOKIE_SECRET='12171f2c09bea41648f7ec3c58e98c27a9bac5ecade7f437bf0dec34f3bf5b45'

DATABASE_HOST='localhost'
DATABASE_USER='root'
DATABASE_PASSWORD='<IMPORANT: YOUR MYSQL DATABASE PASSWORD>'
DATABASE_DATABASE='greeliving'
```

Then, run the `.sql` scripts on your database engine to create the database. Please go to the folder `/sql` in the project directory and run the file `schema.sql` followed by `dummydata.sql`.

## Running the project

In the project's directory, type `server` to start the PHP server. It runs a batch script that starts a server at `127.0.0.1:3000`. Please **do not** change this address. Provided you have done the above steps correctly, the website should run.


## Troubleshooting

Sometimes, you may encounter errors related to the installation of packages or the running of the server. Many of these errors are caused by the `php.ini` file in the same folder as the `php.exe` file. You can use the `php.ini` file included in the project’s repository as a possible quick fix.
