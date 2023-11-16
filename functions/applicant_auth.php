<?php

function isApplicantLoggedIn() {
    $credentials = $GLOBALS["auth0_applicant"]->getCredentials();
    return $credentials != null;
}

function checkApplicantLogIn() {
    if (!isApplicantLoggedIn()) {
        header("Location: " . ROUTE_URL_APPLICANT_LOGIN);
        exit;
    }
}

function checkApplicantFirstLogIn() {
    checkApplicantLogIn();

    $credentials = $GLOBALS["auth0_applicant"]->getCredentials();
    $authenticationId = $credentials->user["sub"];

    $db = $GLOBALS["db"];
    $statement = $db->prepare("SELECT ApplicantID FROM Applicant WHERE AuthenticationID = ?");
    $statement->bind_param("s", $authenticationId);

    $statement->execute();
    $statement->store_result();

    if ($statement->num_rows() === 0) {
        header("Location: /applicant/setup");
        exit;
    }
}

function checkApplicantNotFirstLogIn() {
    checkApplicantLogIn();

    $credentials = $GLOBALS["auth0_applicant"]->getCredentials();
    $authenticationId = $credentials->user["sub"];

    $db = $GLOBALS["db"];
    $statement = $db->prepare("SELECT ApplicantID FROM Applicant WHERE AuthenticationID = ?");
    $statement->bind_param("s", $authenticationId);

    $statement->execute();
    $statement->store_result();

    if ($statement->num_rows() !== 0) {
        header("Location: " . ROUTE_URL_APPLICANT_INDEX);
        exit;
    }
}

// Saves the ApplicantID in the session.
function saveApplicantId() {
    checkApplicantFirstLogIn();

    $credentials = $GLOBALS["auth0_applicant"]->getCredentials();
    $authenticationId = $credentials->user["sub"];

    $db = $GLOBALS["db"];
    $statement = $db->prepare("SELECT ApplicantID FROM Applicant WHERE AuthenticationID = ?");
    $statement->bind_param("s", $authenticationId);

    $statement->execute();
    
    $result = $statement->get_result();
    $applicantId = $result->fetch_assoc()["ApplicantID"];

    if (!isset($_SESSION)) {
        session_start();
    }

    $_SESSION["applicantId"] = $applicantId;
}

function deleteApplicantId() {
    if (isset($_SESSION)) {
        unset($_SESSION["applicantId"]);
    }
}

function checkApplicantId() {
    if (isset($_SESSION) && isset($_SESSION["applicantId"])) {
        return;
    }

    saveApplicantId();
}



?>