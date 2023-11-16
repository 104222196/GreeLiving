<?php

require_once("./functions/init.php");
require_once("./functions/applicant_auth.php");
require_once("./functions/employer_auth.php");

use Steampixel\Route;

Route::add("/", function() {
    if (isset($_SESSION)) {
        echo "Session is set";

        if (isset($_SESSION["applicantId"])) {
            echo "Applicant ID is " . $_SESSION["applicantId"];
        } else {
            echo "Applicant ID is not set.";
        }

        if (isset($_SESSION["employerId"])) {
            echo "Employer ID is " . $_SESSION["employerId"];
        } else {
            echo "Employer ID is not set.";
        }
    } else {
        echo "Session is not set.";
    }

    $applicantCreds = $GLOBALS["auth0_applicant"]->getCredentials();
    if ($applicantCreds !== null) {
        print_r($applicantCreds->user);
    } else {
        echo "Applicant credentials non-existent.";
    }

    $employerCreds = $GLOBALS["auth0_employer"]->getCredentials();
    if ($employerCreds !== null) {
        print_r($employerCreds->user);
    } else {
        echo "Employer credentials non-existent.";
    }
});

Route::add('/change-language', function () {
    require("./pages/change_language.php");
});

Route::add('/applicant/home', function () {
    require("./pages/home.php");
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

Route::add("/applicant/job/([0-9]*)", function ($var1) {
    $jobId = $var1;
    require("./pages/job_details.php");
}, array("get", "post"));

Route::add("/applicant/apply/([0-9]*)", function ($var1) {
    $jobId = $var1;
    require("./pages/apply_job.php");
}, array("get", "post"));

Route::add("/employer/post-job", function () {
    require("./pages/job_post.php");
}, array("get", "post"));

Route::add("/employer/edit-job/([0-9]*)", function ($var1) {
    $jobId = $var1;
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

Route::add('/employer/view-application/([0-9]*)', function($var1) {
    $applicationId = $var1;
    require("./pages/application_view_employer.php");
}, array("get", "post"));

Route::add("/test", function () {
    require("./pages/test.php");
}, array("get", "post"));

Route::run('/');
?>