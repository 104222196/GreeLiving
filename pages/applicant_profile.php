<?php

$session = $auth0_applicants->getCredentials();

if ($session === null) {
    header("Location: " . ROUTE_URL_APPLICANT_LOGIN);
    exit;
}

echo '<pre>';
print_r($session->user);
echo '</pre>';

echo '<p>You have logged in as an applicant</p>';

echo '<p>You can now <a href="/applicant/logout">log out</a>.</p>';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Applicant profile - GreeLiving</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="/assets/css/header.css" rel="stylesheet"/>
    <link href="/assets/css/footer.css" rel="stylesheet"/>
</head>

<body>
    <?php require("./components/header.php") ?>
    <?php require("./components/footer.php") ?>
</body>

</html>