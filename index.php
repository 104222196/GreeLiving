<?php

require_once("./functions/init.php");

use Steampixel\Route;

Route::add('/', function () {
    require("./pages/home.php");
});

Route::add('/change-language', function () {
    require("./pages/change_language.php");
});

Route::add('/applicant/jobsearch', function () {
    require("./pages/job_search.php");
});

Route::add("/applicant/job/([0-9]*)", function ($var1) {
    $jobId = $var1;
    require("./pages/job_details.php");
});

Route::add("/applicant/profile", function () {
    require("./pages/applicant_profile.php");
});

Route::add('/applicant/setup', function () {
    require("./pages/applicant_setup.php");
}, array("get", "post"));

Route::add('/applicant/login', function () {
    $GLOBALS["auth0_applicants"]->clear();
    header("Location: " . $GLOBALS["auth0_applicants"]->login(ROUTE_URL_APPLICANT_CALLBACK));
    exit;
});

Route::add('/applicant/callback', function () {
    $GLOBALS["auth0_applicants"]->exchange(ROUTE_URL_APPLICANT_CALLBACK);
    header("Location: " . ROUTE_URL_APPLICANT_INDEX);
    exit;
});

Route::add('/applicant/logout', function () {
    header("Location: " . $GLOBALS["auth0_applicants"]->logout(ROUTE_URL_INDEX));
    exit;
});

Route::add("/applicant/save-job", function () {
    require("./pages/save_job.php");
}, "post");

Route::add("/applicant/apply/([0-9]*)", function ($var1) {
    $jobId = $var1;
    require("./pages/apply_job.php");
}, array("get", "post"));

/* 
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
}); */


Route::run('/');
?>