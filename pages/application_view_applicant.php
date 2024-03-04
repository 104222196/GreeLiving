<?php

require_once("./functions/applicant_auth.php");
checkApplicantId();

$db = $GLOBALS["db"];

// Checks if this applicant has the rights to access this application.
$statement = new mysqli_stmt($db, "SELECT ApplicantID FROM JobApplication
                                   JOIN Job ON JobApplication.JobID = Job.JobID
                                   WHERE ApplicationID = ?");
$statement->bind_param("s", $applicationId);
$success = $statement->execute();

if (!$success) {
    echo "An error happened. Please try again.";
    exit;
}

$result = $statement->get_result();

if ($result->num_rows == 0) {
    echo "Application not found";
    exit;
}

$application = $result->fetch_assoc();

if ($application["ApplicantID"] !== $_SESSION["applicantId"]) {
    echo "You do not have permission to view this page.";
    exit;
}

// Handles post requests
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST, $_POST["bookedDate"])) {
        header("Location: /applicant/view-application/" . $applicationId);
        exit;
    }

    $dates = explode("|", $_POST["bookedDate"]);

    if (count($dates) !== 2) {
        header("Location: /applicant/view-application/" . $applicationId);
        exit;
    }

    $statement = new mysqli_stmt($db, "UPDATE InPersonInterviewDate SET Booked = '0' WHERE ApplicationID = ?");
    $statement->bind_param("s", $applicationId);
    $statement->execute();

    $statement = new mysqli_stmt($db, "UPDATE InPersonInterviewDate SET Booked = '1' WHERE ApplicationID = ? AND InterviewTimeFrom = ? AND InterviewTimeTo = ?");
    $statement->bind_param("sss", $applicationId, $dates[0], $dates[1]);
    $statement->execute();
}

// Gets the application info
$statement = new mysqli_stmt($db, "SELECT JobTitle, CompanyName, ApplicationStatus, TimeOfApplication, CV, StatementOfPurpose, ExpectToGain, Questions FROM JobApplication 
                                   JOIN Job ON JobApplication.JobID = Job.JobID
                                   JOIN Company ON Job.CompanyID = Company.CompanyID
                                   WHERE ApplicationID = ?");
$statement->bind_param("s", $applicationId);
$success = $statement->execute();

$result = $statement->get_result();
$application = $result->fetch_assoc();

// Gets the interview information, if the status is "Interviewing"
if ($application["ApplicationStatus"] === "Interviewing") {
    $statement = new mysqli_stmt($db, "SELECT ApplicationID, InterviewTypeID FROM Interview WHERE ApplicationID = ?");
    $statement->bind_param("s", $applicationId);
    $statement->execute();
    $result = $statement->get_result();
    if ($result->num_rows > 0) {
        $interview = $result->fetch_assoc();

        if ($interview["InterviewTypeID"] == "1") {
            $statement = new mysqli_stmt($db, "SELECT InterviewTimeFrom, InterviewTimeTo, Booked FROM InPersonInterviewDate WHERE ApplicationID = ?");
            $statement->bind_param("s", $applicationId);
            $statement->execute();

            $inPersonInterviewDates = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
        } else if ($interview["InterviewTypeID"] == "2") {
            $statement = new mysqli_stmt($db, "SELECT InterviewTimeFrom, InterviewTimeTo, InterviewLink FROM OnTheGoInterview WHERE ApplicationID = ?");
            $statement->bind_param("s", $applicationId);
            $statement->execute();

            $onTheGoInterview = $statement->get_result()->fetch_assoc();
        }
    }
}

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <title>View application - GreeLiving for Applicants</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <link href="/assets/css/header.css" rel="stylesheet" />
    <link href="/assets/css/footer.css" rel="stylesheet" />
    <link rel="stylesheet" href="/assets/css/application_view_applicant.css">
</head>

<body>

    <?php require("./components/header_applicant.php") ?>

    <main style="padding-top:100px">

        <h1>Application to
            <?= $application["JobTitle"] ?> at <?=$application["CompanyName"]?>
        </h1>

        <div class="info-cards">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">Details</h2>
                    <p class="card-text"><strong>Time of application:</strong> <?= $application["TimeOfApplication"] ?></p>
                    <p class="card-text"><strong>Application status:</strong> <?= $application["ApplicationStatus"] ?></p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">Attached documents</h2>
                    <p class="card-text"><strong>CV:</strong> <a href="/uploads/<?= $application["CV"] ?>">See attached CV</a></p>
                    <p class="card-text"><strong>Statement of purpose:</strong>
                        <?= $application["StatementOfPurpose"] ?>
                    </p>
                    <p class="card-text"><strong>Expect to gain:</strong>
                        <?= $application["ExpectToGain"] ?>
                    </p>
                    <p class="card-text"><strong>Questions:</strong>
                        <?= $application["Questions"] ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="interview-book">

            <?php if (isset($inPersonInterviewDates)): ?>
                <h2>Book in-person interview date</h2>
                <form method="post" action="">
                    <?php foreach ($inPersonInterviewDates as $inPersonInterviewDate): ?>
                        <div>
                            <label>
                                <input type="radio" name="bookedDate" value="<?=$inPersonInterviewDate['InterviewTimeFrom'] . '|' . $inPersonInterviewDate["InterviewTimeTo"]?>" <?=$inPersonInterviewDate["Booked"] ? "checked" : ""?>/>
                                <?=DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $inPersonInterviewDate['InterviewTimeFrom'])->format("l, d-M-o h:i:s A")?>
                                to
                                <?=DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $inPersonInterviewDate['InterviewTimeTo'])->format("l, d-M-o h:i:s A")?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                    <input type="submit" value="Book"/>
                </form>
            <?php endif; ?>

            <?php if (isset($onTheGoInterview)): ?>
                <h2>Online interview details</h2>
                <p>Link: <?=$onTheGoInterview["InterviewLink"]?></p>
                <p>
                    Time:
                    <?=DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $onTheGoInterview['InterviewTimeFrom'])->format("l, d-M-o h:i:s A")?>
                    to
                    <?=DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $onTheGoInterview['InterviewTimeTo'])->format("l, d-M-o h:i:s A")?>
                </p>
            <?php endif; ?>

        </div>

    </main>

    <?php require("./components/footer.php") ?>

</body>

</html>
