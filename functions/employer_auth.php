<?php

function isEmployerLoggedIn() {
    $credentials = $GLOBALS["auth0_employer"]->getCredentials();
    return $credentials != null;
}

function checkEmployerLogIn() {
    if (!isEmployerLoggedIn()) {
        header("Location: " . ROUTE_URL_EMPLOYER_LOGIN);
        exit;
    }
}

function checkEmployerFirstLogIn() {
    checkEmployerLogIn();

    $credentials = $GLOBALS["auth0_employer"]->getCredentials();
    $authenticationId = $credentials->user["sub"];

    $db = $GLOBALS["db"];
    $statement = $db->prepare("SELECT CompanyID FROM Company WHERE AuthenticationID = ?");
    $statement->bind_param("s", $authenticationId);

    $statement->execute();
    $statement->store_result();

    if ($statement->num_rows() === 0) {
        header("Location: /employer/setup");
        exit;
    }
}

function checkEmployerNotFirstLogIn() {
    checkEmployerLogIn();

    $credentials = $GLOBALS["auth0_employer"]->getCredentials();
    $authenticationId = $credentials->user["sub"];

    $db = $GLOBALS["db"];
    $statement = $db->prepare("SELECT CompanyID FROM Company WHERE AuthenticationID = ?");
    $statement->bind_param("s", $authenticationId);

    $statement->execute();
    $statement->store_result();

    if ($statement->num_rows() !== 0) {
        header("Location: " . ROUTE_URL_EMPLOYER_INDEX);
        exit;
    }
}

// Saves the CompanyID in the session.
function saveEmployerId() {
    checkEmployerFirstLogIn();

    $credentials = $GLOBALS["auth0_employer"]->getCredentials();
    $authenticationId = $credentials->user["sub"];

    $db = $GLOBALS["db"];
    $statement = $db->prepare("SELECT CompanyID FROM Company WHERE AuthenticationID = ?");
    $statement->bind_param("s", $authenticationId);

    $statement->execute();
    
    $result = $statement->get_result();
    $employerId = $result->fetch_assoc()["CompanyID"];

    if (!isset($_SESSION)) {
        session_start();
    }

    $_SESSION["employerId"] = $employerId;
}

function deleteEmployerId() {
    if (isset($_SESSION)) {
        unset($_SESSION["employerId"]);
    }
}

function checkEmployerId() {
    if (isset($_SESSION) && isset($_SESSION["employerId"])) {
        return;
    }

    saveEmployerId();
}

function getEmployerAuthId() {
    if (isEmployerLoggedIn()) {
        return $GLOBALS["auth0_employer"]->getCredentials()->user["sub"];
    } else {
        return null;
    }
}

?>