<?php

$db = $GLOBALS["db"];

$statement = new mysqli_stmt($db, "SELECT * FROM Course WHERE CourseID = ?");
$statement->bind_param("s", $courseId);
$success = $statement->execute();

if (!$success) {
    echo "An error happened. Please try again later.";
    exit;
}

$result = $statement->get_result();

if ($result->num_rows === 0) {
    echo "Course doesn't exist.";
    exit;
}

$course = $result->fetch_assoc();

$errors = array();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST, $_POST["register"])) {
        header("Location: /applicant/courses/" . $courseId);
        exit;
    }

    require_once("./functions/applicant_auth.php");
    checkApplicantId();

    $statement = new mysqli_stmt($db, "INSERT IGNORE INTO CourseApplicant (CourseID, ApplicantID) VALUES (?, ?)");
    $statement->bind_param("ss", $courseId, $_SESSION["applicantId"]);
    $success = $statement->execute();

    if (!$success) {
        $errors[] = "Error while registering for the course.";
    } else {
        header("Location: /applicant/courses/" . $courseId . "?registerSuccess=true");
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (isset($_GET, $_GET["registerSuccess"]) && $_GET["registerSuccess"] === "true") {
        $successMessage = "Successfully registered for the course!";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?=$course["CourseName"]?> course - GreeLiving for Job-seekers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="/assets/css/header.css" rel="stylesheet"/>
    <link href="/assets/css/footer.css" rel="stylesheet"/>
</head>

<body style="padding-top:100px">
    <?php require("./components/header.php") ?>

    <?php if (isset($successMessage)): ?>
        <p class="text-success"><?=$successMessage?></p>
    <?php endif; ?>

    <?php foreach ($errors as $error): ?>
        <p class="text-danger"><?=$error?></p>
    <?php endforeach; ?>

    <h1><?=$course["CourseName"]?></h1>

    <p>
        Lorem ipsum dolor sit amet consectetur adipisicing elit. Reprehenderit expedita, sit autem assumenda explicabo aut, molestiae tempora ipsum ducimus eum exercitationem quasi eligendi nobis corrupti fugiat officiis dolore modi nostrum iure voluptatum nam cupiditate quibusdam. Quasi assumenda obcaecati natus ea, veritatis ratione et excepturi unde ab vitae neque totam officiis molestiae quo fugit alias aliquid harum esse eos nostrum, voluptatum culpa tempore. Quam eius asperiores nihil modi repellat sit omnis sequi hic, sint recusandae corrupti. Vel vitae nostrum consequatur quia deleniti incidunt molestiae voluptates possimus alias ipsum itaque similique qui ut modi labore eum voluptatem reiciendis, neque velit quasi laudantium!
    </p>

    <form method="post" action="/applicant/courses/<?=$course["CourseID"]?>">
        <input type="submit" name="register" value="Register"/>
    </form>

    <?php require("./components/footer.php") ?>
</body>

</html>