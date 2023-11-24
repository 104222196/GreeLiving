<?php

require_once("./functions/employer_auth.php");
checkEmployerLogIn();

$db = $GLOBALS["db"];

$statement = new mysqli_stmt($db, "SELECT FirstName, LastName, Birthdate, Gender, Email, Phone, Nationality, CountryOfResidence, City, District, StreetAddress, JobTitle, ExperienceLevel, EducationBackground, CareerGoal 
                                   FROM Applicant 
                                   WHERE ApplicantID = ?");
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
    <link href="/assets/css/applicant_view_employer.css" rel="stylesheet"/>
</head>

<body>
    <?php require("./components/header_employer.php") ?>

    <main style="padding-top:100px; padding-bottom:100px" class="container-lg">

        <h1>Applicant <?=$applicant["FirstName"] . " " . $applicant["LastName"]?></h1>

        <hr>

        <h2>Applicant profile</h2>
        <h3>Basic information</h3>
        <p><strong>Name:</strong> <?=$applicant["FirstName"] . " " . $applicant["LastName"]?></p>
        <p><strong>Job title:</strong> <?=$applicant["JobTitle"]?></p>
        <p><strong>Experience level:</strong> <?=$applicant["ExperienceLevel"]?></p>

        <h3>Job information</h3>
        <p><strong>Job role:</strong> <?=$applicant["JobTitle"]?></p>
        <p><strong>Experience level:</strong> <?=$applicant["ExperienceLevel"]?></p>
        <p><strong>Career goal introduction:</strong> <?=$applicant["CareerGoal"]?></p>

        <h3>Personal information</h3>
        <p><strong>Name:</strong> <?=$applicant["FirstName"] . " " . $applicant["LastName"]?></p>
        <p><strong>Date of birth:</strong> <?=DateTimeImmutable::createFromFormat("Y-m-d", $applicant["Birthdate"])->format("d/m/Y")?></p>
        <p><strong>Gender:</strong> <?=$applicant["Gender"]?></p>
        <p><strong>Nationality:</strong> <?=$applicant["Nationality"]?></p>
        <p><strong>Phone number:</strong> <?=$applicant["Phone"]?></p>
        <p><strong>Email:</strong> <?=$applicant["Email"]?></p>
        <p><strong>Living location:</strong> <?=$applicant["StreetAddress"] . " " . $applicant["District"] . " " . $applicant["City"] . " " . $applicant["CountryOfResidence"]?></p>
        <p><strong>Education background:</strong> <?=$applicant["EducationBackground"]?></p>

        <hr>

        <h2>Courses</h2>
        <?php if (count($courses) === 0): ?>
            <p>This applicant has not registered for any courses.</p>
        <?php else: ?>
            <div class="card-container">
                <?php foreach ($courses as $course): ?>
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title"><?=$course["CourseName"]?></h3>
                            <p class="card-subtitle">Status: <?= $course["CourseStatus"]?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </main>

    <?php require("./components/footer.php") ?>
</body>

</html>