<?php

$db = $GLOBALS["db"];

$statement = new mysqli_stmt($db, "SELECT JobTitle, ApplicationDeadline, Salary, WorkingLocation, SpecializationName, ExperienceRequirement, WorkingFormat, ScopeOfWork, Benefits, CompanyName, Email, Phone, Introduction FROM Job 
                                   JOIN Specialization ON Job.SpecializationID = Specialization.SpecializationID 
                                   JOIN Company ON Job.CompanyID = Company.CompanyID WHERE JobID = ?");
$statement->bind_param("s", $jobId);
$success = $statement->execute();

if (!$success) {
    echo "An error happened. Please try again.";
    exit();
}

$result = $statement->get_result();

if ($result->num_rows === 0) {
    echo "Job does not exist";
    exit;
}

$job = $result->fetch_assoc();

require_once("./functions/applicant_auth.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST["save"]) && !isset($_POST["unsave"])) {
        header("Location: /applicant/job/" . $jobId);
        exit;
    }

    checkApplicantId();

    $applicantId = $_SESSION["applicantId"];
    $jobId = trim($jobId);

    if (isset($_POST["save"])) {
        $statement = new mysqli_stmt($db, "INSERT IGNORE INTO SavedJob VALUES (?,?)");
    } else if (isset($_POST["unsave"])) {
        $statement = new mysqli_stmt($db, "DELETE FROM SavedJob WHERE JobID = ? AND ApplicantID = ?");
    }

    $statement->bind_param("ss", $jobId, $applicantId);
    $statement->execute();
}

if (isApplicantLoggedIn()) {
    checkApplicantId();

    $applicantId = $_SESSION["applicantId"];
    $jobId = trim($jobId);

    $statement = new mysqli_stmt($db, "SELECT JobID FROM SavedJob WHERE JobID = ? AND ApplicantID = ?");
    $statement->bind_param("ss", $jobId, $applicantId);
    $statement->execute();
    $statement->store_result();

    if ($statement->num_rows() > 0) {
        $saved = true;
    } else {
        $saved = false;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>
        <?= $job["JobTitle"] ?> job details - GreeLiving for Job-seekers
    </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <link href="/assets/css/header.css" rel="stylesheet" />
    <link href="/assets/css/footer.css" rel="stylesheet" />
    <link rel="stylesheet" href="/assets/css/job_details.css">

</head>

<body>
    <?php require("./components/header_applicant.php") ?>

    <main style="padding-top:100px">
        <h1>
            <?= $job["JobTitle"] ?>
        </h1>
        
        <div class="info-cards">

            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">Employer information</h2>
                    <p class="card-text">
                        <strong>Company name:</strong>
                        <?= $job["CompanyName"] ?>
                    </p>
                    <p class="card-text">
                        <strong>Email:</strong>
                        <?= $job["Email"] ?>
                    </p>
                    <p class="card-text">
                        <strong>Phone:</strong>
                        <?= $job["Phone"] ?>
                    </p>
                    <p class="card-text">
                        <strong>Introduction:</strong>
                        <?= $job["Introduction"] ?>
                    </p>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <h2>Job information</h2>
                    <p>
                        <strong>Application deadline:</strong>
                        <?= $job["ApplicationDeadline"] ?>
                    </p>
                    <p>
                        <strong>Salary:</strong>
                        <?= $job["Salary"] ?>
                    </p>
                    <p>
                        <strong>Working location:</strong>
                        <?= $job["WorkingLocation"] ?>
                    </p>
                    <p>
                        <strong>Specialization:</strong>
                        <?= $job["SpecializationName"] ?>
                    </p>
                    <p>
                        <strong>Experience requirement:</strong>
                        <?= $job["ExperienceRequirement"] ?>
                    </p>
                    <p>
                    <strong>Working format:</strong>
                        <?= $job["WorkingFormat"] ?>
                    </p>
                    <p><strong>Scope of work:</strong>
                        <?= $job["ScopeOfWork"] ?>
                    </p>
                    <p><strong>Benefits:</strong>
                        <?= $job["Benefits"] ?>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="button-group">
            <form method="post" action="">
                <input type="hidden" name="jobId" value="<?= $jobId ?>" />
                <input type="submit" class="btn btn-secondary" <?php
                if (isset($saved)) {
                    if ($saved) {
                        echo 'name="unsave" value="Unsave"';
                    } else {
                        echo 'name="save" value="Save"';
                    }
                } else {
                    echo 'name="save" value="Save"';
                }
                ?> />
            </form>

            <a href="/applicant/apply/<?= $jobId ?>" class="btn btn-primary">Apply to this job</a>
        </div>
    </main>

    <?php require("./components/footer.php") ?>
</body>

</html>
