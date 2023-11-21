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
    <title>
        <?= $course["CourseName"] ?> course - GreeLiving for Job-seekers
    </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="/assets/css/header.css" rel="stylesheet" />
    <link href="/assets/css/footer.css" rel="stylesheet" />
</head>

<body>
    <?php require("./components/header_applicant.php") ?>

    <main style="padding-top:100px">

        <?php if (isset($successMessage)): ?>
            <p class="text-success">
                <?= $successMessage ?>
            </p>
        <?php endif; ?>

        <?php foreach ($errors as $error): ?>
            <p class="text-danger">
                <?= $error ?>
            </p>
        <?php endforeach; ?>

        <h1>
            <?= $course["CourseName"] ?>
        </h1>

        <section>
            <h2>Introduction</h2>
            <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Saepe est explicabo voluptates veritatis corrupti ratione aperiam laudantium quasi! Eum rem, fugit asperiores accusamus quas delectus vero nisi aspernatur velit quidem officiis illo similique, aut magnam iste enim, voluptas quibusdam recusandae error deserunt dolorum consequuntur dolorem dicta. Labore voluptate sapiente numquam.</p>
        </section>

        <section>
            <h2>Course information</h2>
            <p>Length: 12 weeks</p>
            <p>Provider: GreeLiving</p>
            <p>Price: $999</p>
        </section>

        <section>
            <h2>Course outline</h2>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Corporis rem illum itaque iste accusamus nisi iusto reiciendis nihil quasi aspernatur placeat dicta, eligendi, labore ipsa sequi. Earum, esse similique. Vel fugit reiciendis, voluptatem quas atque similique assumenda, ut doloremque nam adipisci quibusdam corporis et aliquam voluptas voluptatum architecto unde repellat.</p>
        </section>

        <section>
            <h2>Course benefits</h2>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Totam nemo id qui. Minima ullam praesentium, eveniet laboriosam eligendi pariatur voluptatem fugit ut ab recusandae earum itaque sequi molestiae obcaecati dignissimos cum dicta fugiat aliquam eos officiis esse odio. Quas ratione inventore debitis voluptatibus incidunt, voluptatem nihil quibusdam magni praesentium cupiditate!</p>
        </section>

        <form method="post" action="/applicant/courses/<?= $course["CourseID"] ?>">
            <fieldset>
                <legend>Personal information</legend>
                <label>
                    Name: <input type="text" name="name"/>
                </label>
                <label>
                    Age: <input type="text" name="age"/>
                </label>
                <label>
                    Gender: <input type="text" name="gender"/>
                </label>
                <label>
                    Address: <input type="text" name="address"/>
                </label>
            </fieldset>

            <fieldset>
                <legend>Bank account information</legend>
                <label>
                    Bank branch: <input type="text" name="branch"/>
                </label>
                <label>
                    Card number: <input type="text" name="number"/>
                </label>
                <label>
                    Account name: <input type="text" name="accName"/>
                </label>
            </fieldset>

            <input type="submit" name="register" value="Buy the course now!" />
        </form>

    </main>


    <?php require("./components/footer.php") ?>
</body>

</html>