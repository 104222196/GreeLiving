<?php

require_once("./functions/employer_auth.php");
checkEmployerId();

$db = $GLOBALS["db"];

// Gets the employer's information.
$statement = new mysqli_stmt($db, "SELECT * FROM Company WHERE CompanyID = ?");
$statement->bind_param("s", $_SESSION["employerId"]);
$success = $statement->execute();

if (!$success) {
    echo "An error happened. We cannot show your profile at this time.";
    exit;
}

$result = $statement->get_result();
$employer = $result->fetch_assoc();

// Get the list of job postings.
$statement = new mysqli_stmt($db, "SELECT * FROM Job JOIN Specialization ON Job.SpecializationID = Specialization.SpecializationID WHERE CompanyID = ? ORDER BY DatePosted DESC");
$statement->bind_param("s", $_SESSION["employerId"]);
$success = $statement->execute();

if (!$success) {
    $jobs = array();
} else {
    $jobs = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Gets the list of applications to the company.
$statement = new mysqli_stmt($db, "SELECT * FROM JobApplication JOIN Applicant ON JobApplication.ApplicantID = Applicant.ApplicantID JOIN Job ON JobApplication.JobID = Job.JobID WHERE CompanyID = ? ORDER BY TimeOfApplication DESC");
$statement->bind_param("s", $_SESSION["employerId"]);
$success = $statement->execute();

if (!$success) {
    $applications = array();
} else {
    $applications = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Gets the list of upcoming interviews.
$interviews = array();

$statement = new mysqli_stmt($db, "SELECT InPersonInterview.ApplicationID, InPersonInterview.InterviewTypeID, Applicant.FirstName, Applicant.LastName, InterviewTimeFrom, InterviewTimeTo, Job.JobTitle
                                   FROM InPersonInterview
                                   JOIN InPersonInterviewDate ON InPersonInterview.ApplicationID = InPersonInterviewDate.ApplicationID
                                   JOIN JobApplication ON InPersonInterview.ApplicationID = JobApplication.ApplicationID
                                   JOIN Job ON JobApplication.JobID = Job.JobID
                                   JOIN Applicant ON JobApplication.ApplicantID = Applicant.ApplicantID
                                   WHERE Job.CompanyID = ? AND Booked != '0' AND InterviewTimeTo > NOW();");
$statement->bind_param("s", $_SESSION["employerId"]);
$statement->execute();
$result = $statement->get_result();

while ($row = $result->fetch_assoc()) {
    $interviews[] = $row;
}

$statement = new mysqli_stmt($db, "SELECT OnTheGoInterview.ApplicationID, OnTheGoInterview.InterviewTypeID, Applicant.FirstName, Applicant.LastName, InterviewTimeFrom, InterviewTimeTo, InterviewLink, Job.JobTitle
                                   FROM OnTheGoInterview
                                   JOIN JobApplication ON OnTheGoInterview.ApplicationID = JobApplication.ApplicationID
                                   JOIN Job ON JobApplication.JobID = Job.JobID
                                   JOIN Applicant ON JobApplication.ApplicantID = Applicant.ApplicantID
                                   WHERE Job.CompanyID = ? AND InterviewTimeTo > NOW();");
$statement->bind_param("s", $_SESSION["employerId"]);
$statement->execute();
$result = $statement->get_result();

while ($row = $result->fetch_assoc()) {
    $interviews[] = $row;
}

function compareInterviewDates($a, $b)
{
    if ($a["InterviewTimeFrom"] < $b["InterviewTimeTo"]) {
        return -1;
    } else {
        return 1;
    }
}

usort($interviews, "compareInterviewDates");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Profile - GreeLiving for Employers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <link href="/assets/css/header.css" rel="stylesheet" />
    <link href="/assets/css/footer.css" rel="stylesheet" />
</head>

<body>
    <?php require("./components/header_employer.php") ?>

    <main style="padding-top:100px">

        <h1>Hello
            <?= $employer["CompanyName"] ?>
        </h1>
        <a href="/employer/logout">Logout</a>

        <h2>Your profile</h2>
        <a href="/employer/edit-profile">Edit profile</a>
        <p>Name:
            <?= $employer["CompanyName"] ?>
        </p>
        <p>Phone number:
            <?= $employer["Phone"] ?>
        </p>
        <p>Email:
            <?= $employer["Email"] ?>
        </p>
        <p>Size:
            <?= $employer["CompanySize"] ?>
        </p>
        <p>Introduction:
            <?= $employer["Introduction"] ?>
        </p>

        <h2>Posted jobs</h2>
        <a href="/employer/post-job">Post a new job</a>

        <?php if (count($jobs) === 0): ?>
            <p>You have not posted any jobs.</p>
        <?php else: ?>
            <?php foreach ($jobs as $job): ?>
                <div>
                    <h3>
                        <?= $job["JobTitle"] ?>
                    </h3>
                    <p>Date posted:
                        <?= $job["DatePosted"] ?>
                    </p>
                    <p>Deadline:
                        <?= $job["ApplicationDeadline"] ?>
                    </p>
                    <p>Salary: $
                        <?= $job["Salary"] ?>
                    </p>
                    <p>Working location:
                        <?= $job["WorkingLocation"] ?>
                    </p>
                    <p>Specialization:
                        <?= $job["SpecializationName"] ?>
                    </p>
                    <p>Experience requirement:
                        <?= $job["ExperienceRequirement"] ?>
                    </p>
                    <p>Working format:
                        <?= $job["WorkingFormat"] ?>
                    </p>
                    <p>Scope of work:
                        <?= $job["ScopeOfWork"] ?>
                    </p>
                    <p>Benefits:
                        <?= $job["Benefits"] ?>
                    </p>
                    <a href="/employer/edit-job/<?=$job["JobID"]?>">Edit job posting</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <h2>My interviews</h2>

        <?php if (count($interviews) === 0): ?>
            <p>You do not have any upcoming interviews.</p>
        <?php else: ?>
            <?php foreach ($interviews as $interview): ?>
                <div>
                    <h3>
                        <?= $interview["FirstName"] . " " . $interview["LastName"] ?>
                    </h3>
                    <p>for position
                        <?= $interview["JobTitle"] ?>
                    </p>
                    <p>Interview format:
                        <?= ($interview["InterviewTypeID"] == "1") ? "In-person" : "Online" ?>
                    </p>
                    <?php if (isset($interview["InterviewLink"])): ?>
                        <p>Interview link: <?=$interview["InterviewLink"]?></p>
                    <?php endif; ?>
                    <p>From:
                        <?= DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $interview["InterviewTimeFrom"])->format("d/m/Y H:i:s") ?>
                        to
                        <?= DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $interview["InterviewTimeTo"])->format("d/m/Y H:i:s") ?>
                    </p>
                    <a href="/employer/view-application/<?= $interview["ApplicationID"] ?>">Edit interview details</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <h2>My applicants</h2>

        <?php if (count($applications) === 0): ?>
            <p>You do not have any applications.</p>
        <?php else: ?>
            <?php foreach ($applications as $application): ?>
                <div>
                    <h3>
                        <?= $application["FirstName"] . " " . $application["LastName"] ?>
                    </h3>
                    <p>for position
                        <?= $application["JobTitle"] ?>
                    </p>
                    <p>Submitted:
                        <?= DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $application["TimeOfApplication"])->format("d/m/Y H:i:s") ?>
                    </p>
                    <p>Status:
                        <?= $application["ApplicationStatus"] ?>
                    </p>
                    <a href="/employer/view-application/<?= $application["ApplicationID"] ?>">More details</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </main>

    <?php require("./components/footer.php") ?>
</body>

</html>