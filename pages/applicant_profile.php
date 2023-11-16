<?php

require_once("./functions/applicant_auth.php");
checkApplicantId();

$db = $GLOBALS["db"];

$statement = new mysqli_stmt($db,"SELECT * FROM Applicant WHERE ApplicantID = ?");
$statement->bind_param("s", $_SESSION["applicantId"]);
$success = $statement->execute();

if (!$success) {
    echo "An error happened. You cannot access this page right now.";
    exit;
}

$result = $statement->get_result();
$applicant = $result->fetch_assoc();

$statement = new mysqli_stmt($db, "SELECT * FROM SavedJob 
                                   JOIN Job ON SavedJob.JobID = Job.JobID
                                   JOIN Company ON Job.CompanyID = Company.CompanyID
                                   WHERE SavedJob.ApplicantID = ?");
$statement->bind_param("s", $_SESSION["applicantId"]);
$statement->execute();

$savedJobs = $statement->get_result();

$statement = new mysqli_stmt($db, "SELECT Job.JobID, TimeOfApplication, ApplicationStatus, CompanyName, JobTitle, WorkingLocation, WorkingFormat FROM JobApplication 
                                   JOIN Job ON JobApplication.JobID = Job.JobID 
                                   JOIN Company ON Job.CompanyID = Company.CompanyID
                                   WHERE ApplicantID = ? 
                                   ORDER BY TimeOfApplication DESC");
$statement->bind_param("s", $_SESSION["applicantId"]);
$statement->execute();

$appliedJobs = $statement->get_result();

function appliedJob($job) {
    echo "<div>";
    echo '<a href="/applicant/job/' . $job["JobID"] . '">' . $job["JobTitle"] . '</a>';
    echo '<p>at ' . $job["CompanyName"] . '</p>';
    echo '<p>Status: ' . $job["ApplicationStatus"] . '</p>';
    echo '<p>Application date: ' . $job["TimeOfApplication"] . '</p>';
    echo "</div>";
}

function savedJob($job) {
    echo "<div>";
    echo '<a href="/applicant/job/' . $job["JobID"] . '">' . $job["JobTitle"] . '</a>';
    echo '<p>at ' . $job["CompanyName"] . '</p>';
    echo "</div>";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Applicant profile - GreeLiving</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="/assets/css/header.css" rel="stylesheet"/>
    <link href="/assets/css/footer.css" rel="stylesheet"/>
</head>

<body style="padding-top:100px">
    <?php require("./components/header.php") ?>

    <h1><?php echo "Hello " . $applicant["FirstName"] . " " . $applicant["LastName"]; ?></h1>

    <?php
    echo "<pre>";
    echo "Applicant information";
    print_r($applicant);
    echo "</pre>";
    ?>
    <h2>Saved jobs</h2>

    <?php
    while ($saved = $savedJobs->fetch_assoc()) {
        savedJob($saved);
    }
    ?>

    <h2>My applications</h2>
    <?php
    while ($applied = $appliedJobs->fetch_assoc()) {
        // print_r($applied);
        appliedJob($applied);
    }

    
    ?>

    <?php require("./components/footer.php") ?>
</body>

</html>