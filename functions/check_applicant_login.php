<?php

function checkLogIn() {
    $session = $GLOBALS["auth0_applicants"]->getCredentials();

    if ($session === null) {
        header("Location: " . ROUTE_URL_APPLICANT_LOGIN);
        exit;
    }
}

function checkFirstTimeLogIn() {
    checkLogIn();

    $session = $GLOBALS["auth0_applicants"]->getCredentials();
    $db = $GLOBALS["db"];

    $query = sprintf("SELECT * FROM Applicant WHERE AuthenticationID = '%s'", $db->real_escape_string($session->user["sub"]));
    $result = $db->query($query);

    if ($result->fetch_assoc() === null) {
        header("Location: /applicant/setup");
        exit;
    }
}

function checkNotFirstTimeLogIn() {
    checkLogIn();

    $session = $GLOBALS["auth0_applicants"]->getCredentials();
    $db = $GLOBALS["db"];

    $query = sprintf("SELECT * FROM Applicant WHERE AuthenticationID = '%s'", $db->real_escape_string($session->user["sub"]));
    $result = $db->query($query);

    if ($result->fetch_assoc() !== null) {
        header("Location: " . ROUTE_URL_APPLICANT_INDEX);
        exit;
    }
}

?>