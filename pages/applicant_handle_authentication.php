<?php

$session = $auth0_applicants->getCredentials();

if ($session === null) {
    header("Location: " . ROUTE_URL_APPLICANT_LOGIN);
    exit;
}

$userId = $session->user["sub"];

