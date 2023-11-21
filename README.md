# GreeLiving
Website for GreeLiving. Written in PHP.

# Setting up

Some environment variables needed to run this project must be specified before it can start. PLEASE CREATE A FILE CALLED `.env` AND PUT IT IN THE SAME DIRECTORY AS `index.php`.

In the `.env` PLEASE ADD THE FOLLOWING INFORMATION.

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

This project requires Composer to run. Composer is a package manager for PHP that can be downloaded from [the official website](https://getcomposer.org/download/).

The Composer installer will ask for a PHP executable (a.k.a. php.exe) on your system. If you already have XAMPP installed, you can refer it to the executable file found in `/xampp/php/php.exe` (the location of XAMPP will vary based on your past installation.)

On the terminal, `cd` to the project's directory and run `composer install`. This will install all the packages needed for the website to run. 

To start the development server, type `php -S 127.0.0.1:3000` in the terminal. Please do **NOT** type `localhost:3000` because it will not work with the authentication module.

# Database initialization

Using phpMyAdmin, MySQL Workbench, or a preferred tool of your choice, run the scripts `schema.sql` and `dummydata.sql` in the `/sql` folder to create the database. THE WEBSITE WILL NOT FUNCTION WITHOUT THESE TABLES.

