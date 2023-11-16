<?php

$db = $GLOBALS["db"];

$statement = new mysqli_stmt($db, "SELECT * FROM Job 
                                   JOIN Specialization ON Job.SpecializationID = Specialization.SpecializationID 
                                   JOIN Company ON Job.CompanyID = Company.CompanyID WHERE JobID = ?");
$statement->bind_param("s", $jobId);
$success = $statement->execute();
$result = $statement->get_result();

if (!$success || $result->num_rows == 0) {
    header("Location: /applicant/jobsearch");
    exit();
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
    
    $statement = new mysqli_stmt($db, "SELECT * FROM SavedJob WHERE JobID = ? AND ApplicantID = ?");
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
    <title>Job details - GreeLiving</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</head>
<body>
    <h1><?php echo $job["JobTitle"] ?></h1>
    <p>Employer: <?php echo $job["CompanyName"] ?></p>
    <p>Application deadline: <?php echo $job["ApplicationDeadline"] ?></p>

    <form method="post" action="">
        <input type="hidden" name="jobId" value=<?php echo '"' . $jobId . '"'?>/>
        <input
            type="submit"
            <?php
                if (isset($saved)) {
                    if ($saved) {
                        echo 'name="unsave" value="Unsave"';
                    } else {
                        echo 'name="save" value="Save"';
                    }
                } else {
                    echo 'name="save" value="Save"';
                }
            ?>
        />
    </form>

    <a href="/applicant/apply/<?php echo $jobId?>">Apply</a>    
</body>
</html>