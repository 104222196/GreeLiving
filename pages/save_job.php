<?php

// If have time: Error handling to prevent saving jobs whose deadlines have passed.

if ($_POST && isset($_POST["jobId"])) {

    require_once("./functions/check_applicant_login.php");

    checkFirstTimeLogIn();

    $authenticationId = $GLOBALS["auth0_applicants"]->getCredentials()->user["sub"];

    $db = $GLOBALS["db"];

    $query = 'SELECT ApplicantID FROM Applicant WHERE AuthenticationID = "' . $authenticationId .'"';
    $applicantId = $db->query($query)->fetch_assoc()["ApplicantID"];

    $query = 'SELECT ApplicationID FROM JobApplication WHERE JobID = "' . trim($_POST["jobId"]) . '" AND ApplicantID = "' . $applicantId . '" AND ApplicationStatus = "Saved"';
    $result = $db->query($query);

    if ($result->num_rows == 0) {
        $query = 'INSERT INTO JobApplication (JobID, ApplicantID, TimeOfApplication, CV, StatementOfPurpose, ExpectToGain, Questions, ApplicationStatus) VALUE ("' . trim($_POST["jobId"]) . '", "' . $applicantId . '", NOW(), "", "", "", "", "Saved");';
        $result = $db->query($query);
    }

    header("Location: /applicant/job/" . trim($_POST["jobId"]));
    exit();
}

?>