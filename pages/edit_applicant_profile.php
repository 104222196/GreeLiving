<?php

require_once("./functions/applicant_auth.php");
checkApplicantId();

$db = $GLOBALS["db"];

$statement = new mysqli_stmt($db, "SELECT FirstName, LastName, Birthdate, Gender, Phone, Nationality, CountryOfResidence, City, District, StreetAddress, JobTitle, ExperienceLevel, EducationBackground, CareerGoal FROM Applicant WHERE ApplicantID = ?");
$statement->bind_param("s", $_SESSION["applicantId"]);
$success = $statement->execute();

if (!$success) {
    echo "An error happened. Please try again later.";
    exit;
}

$applicant = $statement->get_result()->fetch_assoc();

$fName = $applicant["FirstName"];
$lName = $applicant["LastName"];
$birthdate = $applicant["Birthdate"];
$gender = $applicant["Gender"];
$phone = $applicant["Phone"];
$nationality = $applicant["Nationality"];
$countryOfResidence = $applicant["CountryOfResidence"];
$city = $applicant["City"];
$district = $applicant["District"];
$streetAddress = $applicant["StreetAddress"];
$jobTitle = $applicant["JobTitle"];
$experience = $applicant["ExperienceLevel"];
$education = $applicant["EducationBackground"];
$careerGoal = $applicant["CareerGoal"];

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (isset($_GET["editSuccess"]) && $_GET["editSuccess"] === "true") {
        $successMessage = "Your profile has been updated successfully.";
    }
}

$validGenders = array("Male", "Female", "Non-binary", "Other");
$validExperiences = array("Internship", "Entry level", "Junior", "Mid-level", "Senior");
$validEducationLevels = array("Not graduated", "Intermediate degree", "High school degree", "College degree", "Undergraduate degree", "Postgraduate degree", "Other");

$errors = array();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (
        !isset($_POST, $_POST["fName"], $_POST["lName"], $_POST["birthdate"], $_POST["gender"],
        $_POST["phone"], $_POST["nationality"], $_POST["countryOfResidence"], $_POST["city"],
        $_POST["district"], $_POST["streetAddress"], $_POST["jobTitle"], $_POST["experience"],
        $_POST["education"], $_POST["careerGoal"])
    ) {
        header("Location: /applicant/edit-profile");
        exit;
    }

    $fName = trim($_POST["fName"]);
    $lName = trim($_POST["lName"]);
    $birthdate = trim($_POST["birthdate"]);
    $gender = trim($_POST["gender"]);
    $phone = trim($_POST["phone"]);
    $nationality = trim($_POST["nationality"]);
    $countryOfResidence = trim($_POST["countryOfResidence"]);
    $city = trim($_POST["city"]);
    $district = trim($_POST["district"]);
    $streetAddress = trim($_POST["streetAddress"]);
    $jobTitle = trim($_POST["jobTitle"]);
    $experience = trim($_POST["experience"]);
    $education = trim($_POST["education"]);
    $careerGoal = trim($_POST["careerGoal"]);

    if ($fName == "") {
        array_push($errors, "Please specify your first name.");
    }
    if (strlen($fName) > 100) {
        array_push($errors, "Your first name is too long. Max character limit is 100.");
    }
    if ($lName == "") {
        array_push($errors, "Please specify your last name.");
    }
    if (strlen($lName) > 100) {
        array_push($errors, "Your last name is too long. Max character limit is 100.");
    }
    if (DateTimeImmutable::createFromFormat("Y-m-d", $birthdate) == false) {
        array_push($errors, "Your birthdate is in the wrong format.");
    }
    if (!in_array($gender, $validGenders)) {
        array_push($errors, "Invalid gender. Please select one from the dropdown.");
    }
    if (!preg_match("/^\d{10}$/", $phone)) {
        array_push($errors, "Please enter a valid phone number of exactly 10 digits.");
    }
    if (strlen($nationality) > 100) {
        array_push($errors, "Your nationality is too long. Max character limit is 100.");
    }
    if (strlen($countryOfResidence) > 100) {
        array_push($errors, "Your country of residence is too long. Max character limit is 100.");
    }
    if (strlen($city) > 100) {
        array_push($errors, "Your city is too long. Max character limit is 100.");
    }
    if (strlen($district) > 100) {
        array_push($errors, "Your district is too long. Max character limit is 100.");
    }
    if (strlen($streetAddress) > 100) {
        array_push($errors, "Your street address is too long. Max character limit is 100.");
    }
    if (strlen($jobTitle) > 100) {
        array_push($errors, "Your job title is too long. Max character limit is 100.");
    }
    if (!in_array($experience, $validExperiences)) {
        array_push($errors, "Invalid experience level. Please select one from the dropdown.");
    }
    if (!in_array($education, $validEducationLevels)) {
        array_push($errors, "Invalid education level. Please select one from the dropdown.");
    }

    if (count($errors) === 0) {
        $statement = new mysqli_stmt($db, "UPDATE Applicant 
                                           SET FirstName = ?, LastName = ?, Birthdate = ?, Gender = ?, Phone = ?, Nationality = ?, CountryOfResidence = ?, 
                                           City = ?, District = ?, StreetAddress = ?, JobTitle = ?, ExperienceLevel = ?, EducationBackground = ?, CareerGoal = ?
                                           WHERE ApplicantID = ?");
        $statement->bind_param(
            "sssssssssssssss",
            $fName,
            $lName,
            $birthdate,
            $gender,
            $phone,
            $nationality,
            $countryOfResidence,
            $city,
            $district,
            $streetAddress,
            $jobTitle,
            $experience,
            $education,
            $careerGoal,
            $_SESSION["applicantId"]
        );
        $success = $statement->execute();

        if (!$success) {
            array_push($errors, "An error happened. Please try again.");
        } else {
            header("Location: /applicant/edit-profile?editSuccess=true");
            exit;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Edit profile - GreeLiving for Job-seekers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <link href="/assets/css/header.css" rel="stylesheet" />
    <link href="/assets/css/footer.css" rel="stylesheet" />
    <link rel="stylesheet" href="/assets/css/edit_applicant_profile.css">

</head>

<body>
    <?php require("./components/header_applicant.php") ?>

    <main style="padding-top:100px">

        <h1>Edit profile</h1>

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

        <form method="post" action="/applicant/edit-profile">
            <label>
                First name: <input type="text" name="fName" value="<?= $fName ?>" />
            </label>

            <label>
                Last name: <input type="text" name="lName" value="<?= $lName ?>" />
            </label>

            <label>
                Birthdate: <input type="date" name="birthdate" value="<?= $birthdate ?>" />
            </label>

            <label>
                Gender:
                <select name="gender">
                    <?php foreach ($validGenders as $validGender): ?>
                        <option value="<?= $validGender ?>" <?= ($validGender === $gender) ? " selected" : "" ?>>
                            <?= $validGender ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Phone: <input type="text" name="phone" value="<?= $phone ?>" />
            </label>

            <label>
                Nationality: <input type="text" name="nationality" value="<?= $nationality ?>" />
            </label>

            <label>
                Country of residence: <input type="text" name="countryOfResidence" value="<?= $countryOfResidence ?>" />
            </label>

            <label>
                City: <input type="text" name="city" value="<?= $city ?>" />
            </label>

            <label>
                District: <input type="text" name="district" value="<?= $district ?>" />
            </label>

            <label>
                Street address: <input type="text" name="streetAddress" value="<?= $streetAddress ?>" />
            </label>

            <label>
                Job title: <input type="text" name="jobTitle" value="<?= $jobTitle ?>" />
            </label>

            <label>
                Experience level:
                <select name="experience">
                    <?php foreach ($validExperiences as $validExperience): ?>
                        <option value="<?= $validExperience ?>" <?= ($validExperience === $experience) ? " selected" : "" ?>>
                            <?= $validExperience ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Education background:
                <select name="education">
                    <?php foreach ($validEducationLevels as $validEducationLevel): ?>
                        <option value="<?= $validEducationLevel ?>" <?= ($validEducationLevel === $education) ? " selected" : "" ?>>
                            <?= $validEducationLevel ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Career goal:
                <textarea name="careerGoal"><?= $applicant["CareerGoal"] ?></textarea>
            </label>

            <input type="submit" value="Edit" />
        </form>
    </main>

    <?php require("./components/footer.php") ?>

</body>

</html>