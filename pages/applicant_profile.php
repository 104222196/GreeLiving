<?php

require_once("./functions/applicant_auth.php");
checkApplicantId();

$db = $GLOBALS["db"];

// Get the applicant's personal info.
$statement = new mysqli_stmt($db,"SELECT * FROM Applicant WHERE ApplicantID = ?");
$statement->bind_param("s", $_SESSION["applicantId"]);
$success = $statement->execute();

if (!$success) {
    echo "An error happened. You cannot access this page right now.";
    exit;
}

$result = $statement->get_result();
$applicant = $result->fetch_assoc();

// Get the applicant's saved jobs.
$statement = new mysqli_stmt($db, "SELECT * FROM SavedJob 
                                   JOIN Job ON SavedJob.JobID = Job.JobID
                                   JOIN Company ON Job.CompanyID = Company.CompanyID
                                   WHERE SavedJob.ApplicantID = ?");
$statement->bind_param("s", $_SESSION["applicantId"]);
$statement->execute();

$savedJobs = $statement->get_result()->fetch_all(MYSQLI_ASSOC);

// Get the applicant's job applications.
$statement = new mysqli_stmt($db, "SELECT ApplicationID, Job.JobID, TimeOfApplication, ApplicationStatus, CompanyName, JobTitle, WorkingLocation, WorkingFormat FROM JobApplication 
                                   JOIN Job ON JobApplication.JobID = Job.JobID 
                                   JOIN Company ON Job.CompanyID = Company.CompanyID
                                   WHERE ApplicantID = ? 
                                   ORDER BY TimeOfApplication DESC");
$statement->bind_param("s", $_SESSION["applicantId"]);
$statement->execute();

$applications = $statement->get_result()->fetch_all(MYSQLI_ASSOC);

// Get the interviews associated with "Interviewing" applications.
$interviews = array();

foreach ($applications as $application) {
    if ($application["ApplicationStatus"] === "Interviewing") {
        $statement = new mysqli_stmt($db, "SELECT InterviewTypeID FROM Interview WHERE ApplicationID = ?");
        $statement->bind_param("s", $application["ApplicationID"]);
        $statement->execute();
        $result = $statement->get_result();
        if ($result->num_rows === 0) {
            $interviews[$application["ApplicationID"]] = "Awaiting employer's schedule.";
            continue;
        }
        $interviewType = $result->fetch_assoc()["InterviewTypeID"];

        if ($interviewType == "1") {
            $statement = new mysqli_stmt($db, "SELECT InterviewTimeFrom, InterviewTimeTo, Booked FROM InPersonInterviewDate WHERE ApplicationID = ?");
            $statement->bind_param("s", $application["ApplicationID"]);
            $statement->execute();
            $dates = $statement->get_result()->fetch_all(MYSQLI_ASSOC);

            if (count($dates) === 0) {
                $interviews[$application["ApplicationID"]] = "Awaiting employer's schedule.";
            } else {
                foreach ($dates as $date) {
                    if ($date["Booked"] == "1") {
                        $interviews[$application["ApplicationID"]] = array(
                            "From" => DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $date["InterviewTimeFrom"])->format("d/m/Y H:i:s"),
                            "To" => DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $date["InterviewTimeTo"])->format("d/m/Y H:i:s"),
                            "Format" => "In person"
                        );
                    }
                }

                if (!isset($interviews[$application["ApplicationID"]])) {
                    $interviews[$application["ApplicationID"]] = "Booking available!";
                }
            }
        } else if ($interviewType == "2") {
            $statement = new mysqli_stmt($db, "SELECT InterviewTimeFrom, InterviewTimeTo FROM OnTheGoInterview WHERE ApplicationID = ?");
            $statement->bind_param("s", $application["ApplicationID"]);
            $statement->execute();
            $date = $statement->get_result();

            if ($date->num_rows > 0) {
                $onlineInterview = $date->fetch_assoc();

                $interviews[$application["ApplicationID"]] = array(
                    "From" => DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $onlineInterview["InterviewTimeFrom"])->format("d/m/Y H:i:s"),
                    "To" => DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $onlineInterview["InterviewTimeTo"])->format("d/m/Y H:i:s"),
                    "Format" => "Online"
                );
            } else {
                $interviews[$application["ApplicationID"]] = "Awaiting employer's schedule.";
            }
        }
    }
}

// Get the applicant's registered courses.
$statement = new mysqli_stmt($db, "SELECT CourseName, CourseStatus FROM CourseApplicant
                                   JOIN Course ON CourseApplicant.CourseID = Course.CourseID
                                   WHERE ApplicantID = ?");
$statement->bind_param("s", $_SESSION["applicantId"]);
$statement->execute();
$courses = $statement->get_result()->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Profile - GreeLiving for Job-seekers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <link href="/assets/css/header.css" rel="stylesheet"/>
    <link href="/assets/css/footer.css" rel="stylesheet"/>
    <link href="/assets/css/applicant_profile.css" rel="stylesheet"/>
</head>

<body>
    <?php require("./components/header_applicant.php") ?>

    <main style="padding-top:100px">

        <h1>Hello <?=$applicant["FirstName"] . " " . $applicant["LastName"]?></h1>
        <a href="/applicant/logout">Logout</a>

        <h2>Your profile</h2>
        <a href="/applicant/edit-profile">Edit profile</a>
        <h3>Basic information</h3>
        <p>Name: <?=$applicant["FirstName"] . " " . $applicant["LastName"]?></p>
        <p>Job title: <?=$applicant["JobTitle"]?></p>
        <p>Experience level: <?=$applicant["ExperienceLevel"]?></p>

        <h3>Job information</h3>
        <p>Job role: <?=$applicant["JobTitle"]?></p>
        <p>Experience level: <?=$applicant["ExperienceLevel"]?></p>
        <p>Career goal introduction: <?=$applicant["CareerGoal"]?></p>

        <h3>Personal information</h3>
        <p>Name: <?=$applicant["FirstName"] . " " . $applicant["LastName"]?></p>
        <p>Date of birth: <?=DateTimeImmutable::createFromFormat("Y-m-d", $applicant["Birthdate"])->format("d/m/Y")?></p>
        <p>Gender: <?=$applicant["Gender"]?></p>
        <p>Nationality: <?=$applicant["Nationality"]?></p>
        <p>Phone number: <?=$applicant["Phone"]?></p>
        <p>Email: <?=$applicant["Email"]?></p>
        <p>Living location: <?=$applicant["StreetAddress"] . " " . $applicant["District"] . " " . $applicant["City"] . " " . $applicant["CountryOfResidence"]?></p>
        <p>Education background: <?=$applicant["EducationBackground"]?></p>

        <h2>Courses</h2>
        <?php if (count($courses) === 0): ?>
            <p>You have not registered for any courses. Find a suitable course <a href="/applicant/courses">here</a>.</p>
        <?php else: ?>
            <?php foreach ($courses as $course): ?>
                <div>
                    <p><?=$course["CourseName"]?></p>
                    <p>Status: <?= $course["CourseStatus"]?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <h2>Saved jobs</h2>

        <?php if (count($savedJobs) === 0): ?>
            <p>You have not saved any jobs. Find a job of interest <a href="/applicant/jobsearch">here</a>.</p>
        <?php else: ?>
            <?php foreach ($savedJobs as $savedJob): ?>
                <div>
                    <a href="/applicant/job/<?=$savedJob["JobID"]?>">
                        <?=$savedJob["JobTitle"]?>
                    </a>
                    <p><?=$savedJob["CompanyName"]?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>    

        <h2>My applications and interviews</h2>

        <?php if (count($applications) === 0): ?>
            <p>You do not have any applications or interviews. Find a job of interest <a href="/applicant/jobsearch">here</a>.</p>
        <?php else: ?>
            <?php foreach ($applications as $application): ?>
                <div>
                    <h3><?=$application["JobTitle"]?></h3>
                    <p>at <?=$application["CompanyName"]?></p>
                    <p>Submitted: <?=DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $application["TimeOfApplication"])->format("d/m/Y")?></p>
                    <p>Status: <?=$application["ApplicationStatus"]?></p>

                    <?php if (isset($interviews[$application["ApplicationID"]])): ?>
                        <?php if(gettype($interviews[$application["ApplicationID"]]) === "string"): ?>
                            <p>Interview: <?=$interviews[$application["ApplicationID"]]?></p>
                        <?php else: ?>
                            <p>Interview format: <?=$interviews[$application["ApplicationID"]]["Format"]?></p>
                            <p>Interview schedule: From <?=$interviews[$application["ApplicationID"]]["From"]?> to <?=$interviews[$application["ApplicationID"]]["To"]?></p>
                        <?php endif; ?>
                    <?php endif; ?>
                    <a href="/applicant/view-application/<?= $application["ApplicationID"] ?>">More details</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </main>

    <?php require("./components/footer.php") ?>
</body>

</html>