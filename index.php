<?php

// Import the Composer Autoloader to make the SDK classes accessible:
require 'vendor/autoload.php';

// Load our environment variables from the .env file:
$Loader = (new josegonzalez\Dotenv\Loader('./.env'))
    ->parse()
    ->toEnv();

// Now instantiate the Auth0 class with our configuration:
$auth0_applicants = new \Auth0\SDK\Auth0([
    'domain' => $_ENV['AUTH0_APPLICANTS_DOMAIN'],
    'clientId' => $_ENV['AUTH0_APPLICANTS_CLIENT_ID'],
    'clientSecret' => $_ENV['AUTH0_APPLICANTS_CLIENT_SECRET'],
    'cookieSecret' => $_ENV['AUTH0_APPLICANTS_COOKIE_SECRET']
]);

$auth0_companies = new \Auth0\SDK\Auth0([
    'domain' => $_ENV['AUTH0_COMPANIES_DOMAIN'],
    'clientId' => $_ENV['AUTH0_COMPANIES_CLIENT_ID'],
    'clientSecret' => $_ENV['AUTH0_COMPANIES_CLIENT_SECRET'],
    'cookieSecret' => $_ENV['AUTH0_COMPANIES_COOKIE_SECRET']
]);

// Import our router library:
use Steampixel\Route;

// Define route constants:
define('ROUTE_URL_INDEX', rtrim($_ENV['AUTH0_BASE_URL'], '/'));
define('ROUTE_URL_APPLICANT_INDEX', ROUTE_URL_INDEX . '/applicants');
define('ROUTE_URL_APPLICANT_LOGIN', ROUTE_URL_INDEX . '/applicants/login');
define('ROUTE_URL_APPLICANT_CALLBACK', ROUTE_URL_INDEX . '/applicants/callback');
define('ROUTE_URL_APPLICANT_LOGOUT', ROUTE_URL_INDEX . '/applicants/logout');
define('ROUTE_URL_COMPANY_INDEX', ROUTE_URL_INDEX . '/companies');
define('ROUTE_URL_COMPANY_LOGIN', ROUTE_URL_INDEX . '/companies/login');
define('ROUTE_URL_COMPANY_CALLBACK', ROUTE_URL_INDEX . '/companies/callback');
define('ROUTE_URL_COMPANY_LOGOUT', ROUTE_URL_INDEX . '/companies/logout');

Route::add('/', function () {
    require("./pages/home.php");
});

Route::add("/applicants", function () use ($auth0_applicants) {
    $session = $auth0_applicants->getCredentials();

    if ($session === null) {
        header("Location: " . ROUTE_URL_APPLICANT_LOGIN);
        exit;
    }

    require("./pages/applicant_profile.php");    
});

Route::add('/applicants/setup', function () use ($auth0_applicants) {
    $session = $auth0_applicants->getCredentials();

    if ($session === null) {
        header("Location: " . ROUTE_URL_APPLICANT_LOGIN);
        exit;
    }

    require("./pages/applicant_setup.php");
});

Route::add('/applicants/handle-setup', function () use ($auth0_applicants) {
    $session = $auth0_applicants->getCredentials();

    if ($session === null) {
        header("Location: " . ROUTE_URL_APPLICANT_LOGIN);
        exit;
    }

    require("./pages/applicants_handle_setup.php");
}, "post");

Route::add('/applicants/login', function () use ($auth0_applicants) {
    $auth0_applicants->clear();
    header("Location: " . $auth0_applicants->login(ROUTE_URL_APPLICANT_CALLBACK));
    exit;
});

Route::add('/applicants/callback', function () use ($auth0_applicants) {
    $auth0_applicants->exchange(ROUTE_URL_APPLICANT_CALLBACK);
    header("Location: " . ROUTE_URL_APPLICANT_INDEX);
    exit;
});

Route::add('/applicants/logout', function () use ($auth0_applicants) {
    header("Location: " . $auth0_applicants->logout(ROUTE_URL_INDEX));
    exit;
});

Route::add("/companies", function () use ($auth0_companies) {
    $session = $auth0_companies->getCredentials();

    if ($session === null) {
        echo '<p>Please <a href="/companies/login">log in</a> as a company.</p>';
        return;
    }

    echo '<pre>';
    print_r($session->user);
    echo '</pre>';
    echo '<p>You have logged in as a company</p>';

    echo '<p>You can now <a href="/companies/logout">log out</a>.</p>';
});

Route::add('/companies/login', function () use ($auth0_companies) {
    // It's a good idea to reset user sessions each time they go to login to avoid "invalid state" errors, should they hit network issues or other problems that interrupt a previous login process:
    $auth0_companies->clear();

    // Finally, set up the local application session, and redirect the user to the Auth0 Universal Login Page to authenticate.
    header("Location: " . $auth0_companies->login(ROUTE_URL_COMPANY_CALLBACK));
    exit;
});

Route::add('/companies/callback', function () use ($auth0_companies) {
    // Have the SDK complete the authentication flow:
    $auth0_companies->exchange(ROUTE_URL_COMPANY_CALLBACK);

    // Finally, redirect our end user back to the / index route, to display their user profile:
    header("Location: " . ROUTE_URL_COMPANY_INDEX);
    exit;
});

Route::add('/companies/logout', function () use ($auth0_companies) {
    // Clear the user's local session with our app, then redirect them to the Auth0 logout endpoint to clear their Auth0 session.
    header("Location: " . $auth0_companies->logout(ROUTE_URL_COMPANY_INDEX));
    exit;
});


Route::run('/');
?>