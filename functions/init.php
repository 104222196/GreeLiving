<?php

require('vendor/autoload.php');

// Read environment variables.
$Loader = (new josegonzalez\Dotenv\Loader('./.env'))->parse()->toEnv();

// Set up Auth0 objects for applicants and companies.
$GLOBALS["auth0_applicant"] = new \Auth0\SDK\Auth0([
    'domain' => $_ENV['AUTH0_APPLICANTS_DOMAIN'],
    'clientId' => $_ENV['AUTH0_APPLICANTS_CLIENT_ID'],
    'clientSecret' => $_ENV['AUTH0_APPLICANTS_CLIENT_SECRET'],
    'cookieSecret' => $_ENV['AUTH0_APPLICANTS_COOKIE_SECRET']
]);

$GLOBALS["auth0_employer"] = new \Auth0\SDK\Auth0([
    'domain' => $_ENV['AUTH0_COMPANIES_DOMAIN'],
    'clientId' => $_ENV['AUTH0_COMPANIES_CLIENT_ID'],
    'clientSecret' => $_ENV['AUTH0_COMPANIES_CLIENT_SECRET'],
    'cookieSecret' => $_ENV['AUTH0_COMPANIES_COOKIE_SECRET']
]);

// Route constants.
define('ROUTE_URL_INDEX', trim($_ENV['AUTH0_BASE_URL'], '/'));
define('ROUTE_URL_APPLICANT_INDEX', ROUTE_URL_INDEX . '/applicant/profile');
define('ROUTE_URL_APPLICANT_LOGIN', ROUTE_URL_INDEX . '/applicant/login');
define('ROUTE_URL_APPLICANT_CALLBACK', ROUTE_URL_INDEX . '/applicant/callback');
define('ROUTE_URL_APPLICANT_LOGOUT', ROUTE_URL_INDEX . '/applicant/logout');
define('ROUTE_URL_EMPLOYER_INDEX', ROUTE_URL_INDEX . '/employer/profile');
define('ROUTE_URL_EMPLOYER_LOGIN', ROUTE_URL_INDEX . '/employer/login');
define('ROUTE_URL_EMPLOYER_CALLBACK', ROUTE_URL_INDEX . '/employer/callback');
define('ROUTE_URL_EMPLOYER_LOGOUT', ROUTE_URL_INDEX . '/employer/logout');

// Set the default language, saving it in the session.
session_start();

if (!isset($_SESSION["language"])) {
    $_SESSION["language"] = "en";
}

// Initialize the database.
$GLOBALS["db"] = new mysqli(
    $_ENV["DATABASE_HOST"],
    $_ENV["DATABASE_USER"],
    $_ENV["DATABASE_PASSWORD"],
    $_ENV["DATABASE_DATABASE"]
);

if ($GLOBALS["db"]->connect_error) {
    die("Failed to connect to the database.");
}

// Initialize a directory to store applicants' CVs.
if (!file_exists("uploads") && !is_dir("uploads")) {
    mkdir("uploads");
}

?>