<?php

require_once("./functions/employer_auth.php");
checkEmployerLogIn();

$db = $GLOBALS["db"];

$statement = new mysqli_stmt($db, "SELECT * FROM Applicant WHERE ApplicantID = ?");
$statement->bind_param("s", $applicantId);
$statement->execute();
$result = $statement->get_result();

if($result->num_rows === 0) {
    echo "Applicant does not exist.";
    exit;
}

$applicant = $result->fetch_assoc();

// Get the applicant's registered courses.
$statement = new mysqli_stmt($db, "SELECT CourseName, CourseStatus FROM CourseApplicant
                                   JOIN Course ON CourseApplicant.CourseID = Course.CourseID
                                   WHERE ApplicantID = ?");
$statement->bind_param("s", $applicant["ApplicantID"]);
$statement->execute();
$courses = $statement->get_result()->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?=$applicant["FirstName"] . " " . $applicant["LastName"]?> profile - GreeLiving for Employers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <link href="/assets/css/header.css" rel="stylesheet"/>
    <link href="/assets/css/footer.css" rel="stylesheet"/>
</head>

<body>
    <?php require("./components/header_employer.php") ?>

    <main style="padding-top:100px">

        <h1>Applicant <?=$applicant["FirstName"] . " " . $applicant["LastName"]?></h1>

        <h2>Applicant profile</h2>
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
            <p>This applicant has not registered for any courses.</p>
        <?php else: ?>
            <?php foreach ($courses as $course): ?>
                <div>
                    <p><?=$course["CourseName"]?></p>
                    <p>Status: <?= $course["CourseStatus"]?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </main>

    <?php require("./components/footer.php") ?>
</body>

</html>