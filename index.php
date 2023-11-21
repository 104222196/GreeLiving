<?php

require_once("./functions/init.php");
require_once("./functions/applicant_auth.php");
require_once("./functions/employer_auth.php");

use Steampixel\Route;

Route::add("/", function () {
    header("Location: /applicant/home");
    exit;
});

Route::add('/change-language', function () {
    require("./pages/change_language.php");
});

Route::add("/about", function () {
    require("./pages/about.php");
});

Route::add("/contact", function () {
    require("./pages/contact.php");
});

Route::add('/applicant/home', function () {
    require("./pages/home_applicant.php");
});

Route::add("/applicant/profile", function () {
    require("./pages/applicant_profile.php");
});

Route::add('/applicant/setup', function () {
    require("./pages/applicant_setup.php");
}, array("get", "post"));

Route::add('/applicant/login', function () {
    deleteApplicantId();
    deleteEmployerId();
    $GLOBALS["auth0_applicant"]->clear(true);
    $GLOBALS["auth0_employer"]->clear(true);
    header("Location: " . $GLOBALS["auth0_applicant"]->login(ROUTE_URL_APPLICANT_CALLBACK));
    exit;
});

Route::add('/applicant/callback', function () {
    $GLOBALS["auth0_applicant"]->exchange(ROUTE_URL_APPLICANT_CALLBACK);
    saveApplicantId();
    header("Location: " . ROUTE_URL_APPLICANT_INDEX);
    exit;
});

Route::add('/applicant/logout', function () {
    deleteApplicantId();
    header("Location: " . $GLOBALS["auth0_applicant"]->logout(ROUTE_URL_INDEX));
    exit;
});

Route::add('/applicant/jobsearch', function () {
    require("./pages/job_search.php");
});

Route::add("/applicant/job/([0-9]*)", function ($jobId) {
    require("./pages/job_details.php");
}, array("get", "post"));

Route::add("/applicant/apply/([0-9]*)", function ($jobId) {
    require("./pages/apply_job.php");
}, array("get", "post"));

Route::add("/applicant/view-application/([0-9]*)", function($applicationId) {
    require("./pages/application_view_applicant.php");
}, array("get", "post"));

Route::add("/applicant/edit-profile", function () {
    require("./pages/edit_applicant_profile.php");
}, array("get", "post"));

Route::add("/employer/post-job", function () {
    require("./pages/job_post.php");
}, array("get", "post"));

Route::add("/employer/edit-job/([0-9]*)", function ($jobId) {
    require("./pages/job_edit.php");
}, array("get", "post"));

Route::add("/employer/profile", function () {
    require("./pages/employer_profile.php");
});

Route::add("/employer/setup", function () {
    require("./pages/employer_setup.php");
}, array("get", "post"));

Route::add('/employer/login', function () {
    deleteApplicantId();
    deleteEmployerId();
    $GLOBALS["auth0_applicant"]->clear(true);
    $GLOBALS["auth0_employer"]->clear(true);
    header("Location: " . $GLOBALS["auth0_employer"]->login(ROUTE_URL_EMPLOYER_CALLBACK));
    exit;
});

Route::add('/employer/callback', function () {
    $GLOBALS["auth0_employer"]->exchange(ROUTE_URL_EMPLOYER_CALLBACK);
    saveEmployerId();
    header("Location: " . ROUTE_URL_EMPLOYER_INDEX);
    exit;
});

Route::add('/employer/logout', function () {
    deleteEmployerId();
    header("Location: " . $GLOBALS["auth0_employer"]->logout(ROUTE_URL_INDEX));
    exit;
});

Route::add('/employer/view-application/([0-9]*)', function($applicationId) {
    require("./pages/application_view_employer.php");
}, array("get", "post"));

Route::add('/employer/view-applicant/([0-9]*)', function ($applicantId) {
    require("./pages/applicant_view_employer.php");
});

Route::add('/employer/edit-profile', function () {
    require('./pages/edit_employer_profile.php');
}, array('get','post'));

Route::add("/employer/home", function () {
    require("./pages/home_employer.php");
});

Route::add('/applicant/courses', function () {
    require('./pages/course_list.php');
});

Route::add("/applicant/courses/([0-9]*)", function ($courseId) {
    require("./pages/course_details.php");
}, array("get", "post"));

Route::run('/');
?>