<?php

$db = $GLOBALS["db"];

$statement = new mysqli_stmt($db, "SELECT CourseID, CourseName FROM Course WHERE CourseID = ?");
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
    <title>
        <?= $course["CourseName"] ?> course - GreeLiving for Job-seekers
    </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="/assets/css/header.css" rel="stylesheet" />
    <link href="/assets/css/footer.css" rel="stylesheet" />
    <link href="/assets/css/course_details.css" rel="stylesheet" />
</head>

<body>
    <?php require("./components/header_applicant.php") ?>

    <main style="padding-top:100px; padding-bottom: 100px">

        <?php if (isset($successMessage)): ?>
            <p class="alert alert-success mx-2">
                <?= $successMessage ?>
            </p>
        <?php endif; ?>

        <?php foreach ($errors as $error): ?>
            <p class="alert alert-danger mx-2">
                <?= $error ?>
            </p>
        <?php endforeach; ?>

        <div class="container mt-5">

            <h1 class="text-center">
                <?= $course["CourseName"] ?>
            </h1>

            <div class="row">

                <section class="col-sm-3">
                    <h2 class="text-center">Introduction</h2>
                    <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Saepe est explicabo voluptates
                        veritatis corrupti ratione aperiam laudantium quasi! Eum rem, fugit asperiores accusamus quas
                        delectus vero nisi aspernatur velit quidem officiis illo similique, aut magnam iste enim,
                        voluptas quibusdam recusandae error deserunt dolorum consequuntur dolorem dicta. Labore
                        voluptate sapiente numquam.</p>
                </section>

                <section class="col-sm-3">
                    <h2 class="text-center">Course information</h2>
                    <ul class="list-group">
                        <li class="list-group-item">Length: 12 weeks</li>
                        <li class="list-group-item">Provider: GreeLiving</li>
                        <li class="list-group-item">Price: $999</li>
                    </ul>
                </section>

                <section class="col-sm-3">
                    <h2 class="text-center">Course outline</h2>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Corporis rem illum itaque iste accusamus
                        nisi iusto reiciendis nihil quasi aspernatur placeat dicta, eligendi, labore ipsa sequi. Earum,
                        esse similique. Vel fugit reiciendis, voluptatem quas atque similique assumenda, ut doloremque
                        nam adipisci quibusdam corporis et aliquam voluptas voluptatum architecto unde repellat.</p>
                </section>

                <section class="col-sm-3">
                    <h2 class="text-center">Course benefits</h2>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Totam nemo id qui. Minima ullam
                        praesentium, eveniet laboriosam eligendi pariatur voluptatem fugit ut ab recusandae earum itaque
                        sequi molestiae obcaecati dignissimos cum dicta fugiat aliquam eos officiis esse odio. Quas
                        ratione inventore debitis voluptatibus incidunt, voluptatem nihil quibusdam magni praesentium
                        cupiditate!</p>
                </section>

            </div>

        </div>
        </div>
        <div class="container mt-5">
            <form method="post" action="/applicant/courses/<?= $course["CourseID"] ?>" class="needs-validation"
                novalidate>
                <div class="form-container">
                    <fieldset>
                        <legend>Personal information</legend>
                        <div class="form-group">
                            <label for="name">Name:</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="age">Age:</label>
                            <input type="text" class="form-control" id="age" name="age" required>
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender:</label>
                            <input type="text" class="form-control" id="gender" name="gender" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Address:</label>
                            <input type="text" class="form-control" id="address" name="address" required>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Bank account information</legend>
                        <div class="form-group">
                            <label for="branch">Bank branch:</label>
                            <input type="text" class="form-control" id="branch" name="branch" required>
                        </div>
                        <div class="form-group">
                            <label for="number">Card number:</label>
                            <input type="text" class="form-control" id="number" name="number" required>
                        </div>
                        <div class="form-group">
                            <label for="accName">Account name:</label>
                            <input type="text" class="form-control" id="accName" name="accName" required>
                        </div>
                    </fieldset>
                </div>


                <button type="submit" name="register" class="btn btn-primary d-block mx-auto mt-3">Buy the course
                    now!</button>
            </form>
        </div>
    </main>


    <?php require("./components/footer.php") ?>
</body>

</html>