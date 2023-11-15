<?php

require("./functions/check_applicant_login.php");

checkFirstTimeLogIn();

$authID = $GLOBALS["auth0_applicants"]->getCredentials()->user["sub"];
$db = $GLOBALS["db"];

$query = 'SELECT * FROM Applicant WHERE AuthenticationID = "' . $db->real_escape_string($authID) . '"';
$result = $db->query($query);

if ($result->num_rows === 0) {
    header('Location: /');
    exit();
}

$applicant = $result->fetch_assoc();

$result = $db->query('SELECT * FROM JobApplication WHERE ApplicantID = "' . $applicant["ApplicantID"] . '" AND ApplicationStatus = "Saved"');

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
    print_r($applicant);

    while ($saved = $result->fetch_assoc()) {
        print_r($saved);
    }
    echo "</pre>";
    ?>

    <?php require("./components/footer.php") ?>
</body>

</html>