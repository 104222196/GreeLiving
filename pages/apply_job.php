<?php

// Check if the user has logged in.
require_once("./functions/applicant_auth.php");
checkApplicantId();

$db = $GLOBALS["db"];

$statement = new mysqli_stmt($db, "SELECT JobTitle, CompanyName FROM Job JOIN Company ON Job.CompanyID = Company.CompanyID WHERE JobID = ?");
$statement->bind_param("s", $jobId);
$success = $statement->execute();

if (!$success) {
    echo "An error happened. Please try again later.";
    exit;
}

$result = $statement->get_result();

if ($result->num_rows === 0) {
    echo "Job does not exist.";
    exit;
}

$job = $result->fetch_assoc();

$errors = array();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST["statementOfPurpose"], $_POST["expectToGain"], $_POST["questions"], $_FILES, $_FILES["cv"])) {
        header("Location: /applicant/apply/" . $jobId);
        exit();
    }

    $applicantId = $_SESSION["applicantId"];
    $fileName = uniqid() . "-" . trim($_FILES["cv"]["name"]);
    $statementOfPurpose = trim($_POST["statementOfPurpose"]);
    $expectToGain = trim($_POST["expectToGain"]);
    $questions = trim($_POST["questions"]);

    if ($_FILES["cv"]["error"] == 4) {
        $errors[] = "Please upload your CV.";
    }
    if ($_FILES["cv"]["size"] > 5242880) {
        $errors[] = "Please make sure your CV does not exceed 5MB in size.";
    }
    if (!preg_match("/.+\.pdf$/", $fileName)) {
        $errors[] = "Please make sure your CV is in .pdf format.";
    }
    if ($statementOfPurpose == "") {
        $errors[] = "Please fill in your statement of purpose.";
    }
    if ($expectToGain == "") {
        $errors[] = "Please specify what you expect to gain from the company.";
    }

    if (count($errors) == 0) {
        $uploadSuccess = move_uploaded_file($_FILES["cv"]["tmp_name"], "uploads/" . $fileName);

        if ($uploadSuccess) {
            $statement = new mysqli_stmt($db, "INSERT INTO JobApplication (JobID, ApplicantID, CV, StatementOfPurpose, ExpectToGain, Questions, ApplicationStatus)
                                            VALUES (?, ?, ?, ?, ?, ?, 'Applying')");
            $statement->bind_param("ssssss", $jobId, $applicantId, $fileName, $statementOfPurpose, $expectToGain, $questions);
            try {
                $success = $statement->execute();

                if (!$success) {
                    $errors[] = "An error happened. Please try again.";
                } else {
                    header("Location: /applicant/profile");
                    exit;
                }
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        } else {
            $errors[] = "An error happened when uploading your CV. Please try again.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Applying to
        <?= $job["JobTitle"] ?> at
        <?= $job["CompanyName"] ?> - GreeLiving for Job-seekers
    </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <link href="/assets/css/header.css" rel="stylesheet" />
    <link href="/assets/css/footer.css" rel="stylesheet" />
    <link rel="stylesheet" href="/assets/css/apply_job.css">
    
</head>

<body>

    <?php require("./components/header_applicant.php") ?>

    <main style="padding-top:100px">

        <h1>Applying to
            <?= $job["JobTitle"] ?> at
            <?= $job["CompanyName"] ?>
        </h1>

        <?php foreach ($errors as $error): ?>
            <p class="alert alert-danger">
                <?= $error ?>
            </p>
        <?php endforeach; ?>

        <p class="alert alert-primary">Please complete the following form for your job application.</p>

        <form method="post" action="/applicant/apply/<?= $jobId ?>" enctype="multipart/form-data">
            <label>
                CV/Resume: <input type="file" name="cv" accept=".pdf" id="cv" />
            </label>
            <label>
                Statement of purpose (Why do you want to apply for this job?):
                <textarea
                    name="statementOfPurpose"><?= isset($statementOfPurpose) ? $statementOfPurpose : ""; ?></textarea>
            </label>
            <label>
                What support do you expect to gain from the company?
                <textarea name="expectToGain"><?= isset($expectToGain) ? $expectToGain : ""; ?></textarea>
            </label>
            <label>
                Do you have any questions for us?
                <textarea name="questions"><?= isset($questions) ? $questions : ""; ?></textarea>
            </label>
            <input type="submit" name="submit" value="Submit" class="btn btn-primary"/>
        </form>

    </main>

    <?php require("./components/footer.php") ?>

    <script>
        document.getElementById("cv").onchange = function () {
            if (this.files[0].size > 5242880) {
                alert("Your file is too big! A file cannot exceed 5MB." + this.files[0].size);
                this.value = "";
            }
        }
    </script>

</body>

</html>
