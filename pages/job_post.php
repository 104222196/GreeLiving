<?php

require_once("./functions/employer_auth.php");
checkEmployerId();

$db = $GLOBALS["db"];

$validExperiences = array("Internship", "Entry level", "Junior", "Mid-level", "Senior");
$validFormats = array("On-site", "Remote", "Hybrid");
$errors = array();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (
        !isset($_POST, $_POST["jobTitle"], $_POST["specialization"], $_POST["deadline"], $_POST["salary"],
        $_POST["workLocation"], $_POST["experience"], $_POST["format"], $_POST["scope"], $_POST["benefits"])
    ) {
        header("Location: /employer/post-job");
        exit;
    }

    $companyId = $_SESSION["employerId"];
    $jobTitle = trim($_POST["jobTitle"]);
    $specialization = trim($_POST["specialization"]);
    $deadline = trim($_POST["deadline"]);
    $salary = trim($_POST["salary"]);
    $workLocation = trim($_POST["workLocation"]);
    $experience = trim($_POST["experience"]);
    $format = trim($_POST["format"]);
    $scope = trim($_POST["scope"]);
    $benefits = trim($_POST["benefits"]);

    if ($jobTitle === "") {
        $errors[] = "Please specify the job title.";
    }
    if (!preg_match("/^\d+$/", $specialization)) {
        $errors[] = "Please select a valid specialization.";
    }
    if ($deadline === "") {
        $errors[] = "Please choose a valid deadline.";
    }
    $deadlineDt = DateTimeImmutable::createFromFormat("Y-m-d\\TH:i", $deadline);
    if ($deadlineDt === false) {
        $errors[] = "Please make sure your deadline is in the correct format.";
    }
    if (!preg_match("/(^\d{1,8}$)|(^\d{1,8}\.\d{1,2}$)/", $salary)) {
        $errors[] = "Please enter a valid salary value. It can have up to eight digits before the decimal point and up to two digits after the decimal point.";
    }
    if ($workLocation === "") {
        $errors[] = "Please specify a working location.";
    }
    if (!in_array($experience, $validExperiences)) {
        $errors[] = "Please specify a vaid experience requirement.";
    }
    if (!in_array($format, $validFormats)) {
        $errors[] = "Please specify a valid working format.";
    }
    if ($scope === "") {
        $errors[] = "Please specify the scope of work.";
    }
    if ($benefits === "") {
        $errors[] = "Please specify the work benefits.";
    }

    if (count($errors) === 0) {
        $deadline = $deadlineDt->format("Y-m-d H:i:s");

        $statement = new mysqli_stmt($db, "INSERT INTO Job (CompanyID, JobTitle, ApplicationDeadline, Salary, WorkingLocation, SpecializationID, ExperienceRequirement, WorkingFormat, ScopeOfWork, Benefits) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $statement->bind_param("ssssssssss", $companyId, $jobTitle, $deadline, $salary, $workLocation, $specialization, $experience, $format, $scope, $benefits);
        $success = $statement->execute();

        if (!$success) {
            $errors[] = "An error happened. Please check your input and try again.";
        } else {
            header("Location: /employer/profile");
            exit;
        }
    }
}

$statement = new mysqli_stmt($db, "SELECT * FROM Specialization");
$statement->execute();
$specializations = $statement->get_result()->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Create job posting - GreeLiving for Employers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <link href="/assets/css/header.css" rel="stylesheet" />
    <link href="/assets/css/footer.css" rel="stylesheet" />
    <link rel="stylesheet" href="/assets/css/job_post.css">
</head>

<body>

    <?php require("./components/header_employer.php") ?>

    <main style="padding-top:100px">

        <h1>Create job posting</h1>

        <?php foreach ($errors as $error): ?>
            <p class="alert alert-danger">
                <?= $error ?>
            </p>
        <?php endforeach; ?>

        <form method="post" action="">
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="jobTitle">Job title:</label>
                        <input 
                            type="text"
                            id="jobTitle"
                            name="jobTitle"
                            class="form-control"
                            value="<?= isset($jobTitle) ? $jobTitle : "" ?>"
                        />                        
                    </div>

                    <div class="form-group">
                        <label for="deadline">
                            Application deadline:</label> <input id="deadline" type="datetime-local" name="deadline" class="form-control"
                                value="<?= isset($deadline) ? $deadline : "" ?>" />
                        

                    </div>
                </div>

                <div class="col">
                    <div class="form-group">
                        <label for="specialization">
                            Specialization:</label>
                            <select id="specialization" name="specialization" class="form-control">
                                <?php foreach ($specializations as $specializationOption): ?>
                                    <option value="<?= $specializationOption["SpecializationID"] ?>"
                                        <?= (isset($specialization) && $specializationOption["SpecializationID"] == $specialization) ? " selected" : "" ?>>
                                        <?= $specializationOption["SpecializationName"] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="salary">
                            Salary:</label>
                        <input type="text" id="salary" name="salary" value="<?= isset($salary) ? $salary : "" ?>" class="form-control"/>
                        
                    </div>
                </div>

                <div class="col">

                    <div class="form-group">
                        <label for="workLocation">
                            Working location:</label>
                        <input type="text" id="workLocation" name="workLocation"
                                value="<?= isset($workLocation) ? $workLocation : "" ?>" class="form-control" />
                        
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">

                    <div class="form-group">
                        <label for="experience">
                            Experience requirement:</label>
                            <select name="experience" id="experience" class="form-control">
                                <?php foreach ($validExperiences as $validExperience): ?>
                                    <option value="<?= $validExperience ?>" <?= (isset($experience) && $validExperience == $experience) ? " selected" : "" ?>>
                                        <?= $validExperience ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        
                    </div>
                </div>

                <div class="col">

                    <div class="form-group">
                        <label for="format">
                            Working format:</label>
                            <select name="format" id="format" class="form-control">
                                <?php foreach ($validFormats as $validFormat): ?>
                                    <option value="<?= $validFormat ?>" <?= (isset($format) && $validFormat == $format) ? " selected" : "" ?>>
                                        <?= $validFormat ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="scope">
                            Scope of work:</label>
                            <textarea name="scope" id="scope" class="form-control"><?= isset($scope) ? $scope : "" ?></textarea>
                    </div>

                </div>

                <div class="col">

                    <div class="form-group">
                        <label for="benefits">
                            Benefits:</label>
                            <textarea name="benefits" id="benefits" class="form-control"><?= isset($benefits) ? $benefits : "" ?></textarea>
                        
                    </div>
                </div>
            </div>

            <input type="submit" class="btn btn-primary" value="Post job"/>
        </form>

    </main>

    <?php require("./components/footer.php") ?>

</body>

</html>