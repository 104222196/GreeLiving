
<?php

// Check if this is in fact the first time log in.
require_once("./functions/applicant_auth.php");
checkApplicantNotFirstLogIn();

$db = $GLOBALS["db"];

$validGenders = array("Male", "Female", "Non-binary", "Other");
$validExperiences = array("Internship", "Entry level", "Junior", "Mid-level", "Senior");
$validEducationLevels = array("Not graduated", "Intermediate degree", "High school degree", "College degree", "Undergraduate degree", "Postgraduate degree", "Other");

$errors = array();

// Check if this a form submision
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (
        !isset($_POST, $_POST["fName"], $_POST["lName"], $_POST["birthdate"], $_POST["gender"],
        $_POST["phone"], $_POST["nationality"], $_POST["countryOfResidence"], $_POST["city"],
        $_POST["district"], $_POST["streetAddress"], $_POST["jobTitle"], $_POST["experience"],
        $_POST["education"], $_POST["careerGoal"])
    ) {
        header("Location: /applicant/setup");
        exit;
    }

    $authID = $GLOBALS["auth0_applicant"]->getCredentials()->user["sub"];
    $fName = trim($_POST["fName"]);
    $lName = trim($_POST["lName"]);
    $birthdate = trim($_POST["birthdate"]);
    $gender = trim($_POST["gender"]);
    $phone = trim($_POST["phone"]);
    $email = $GLOBALS["auth0_applicant"]->getCredentials()->user["email"];
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
        $errors[] = "Please specify your first name.";
    }
    if (strlen($fName) > 100) {
        $errors[] = "Your first name is too long. Max character limit is 100.";
    }
    if ($lName == "") {
        $errors[] = "Please specify your last name.";
    }
    if (strlen($lName) > 100) {
        $errors[] = "Your last name is too long. Max character limit is 100.";
    }
    if (DateTimeImmutable::createFromFormat("Y-m-d", $birthdate) == false) {
        $errors[] = "Your birthdate is in the wrong format.";
    }
    if (!in_array($gender, $validGenders)) {
        $errors[] = "Invalid gender. Please select one from the dropdown.";
    }
    if (!preg_match("/^\d{10}$/", $phone)) {
        $errors[] = "Please enter a valid phone number of exactly 10 digits.";
    }
    if (strlen($nationality) > 100) {
        $errors[] = "Your nationality is too long. Max character limit is 100.";
    }
    if (strlen($countryOfResidence) > 100) {
        $errors[] = "Your country of residence is too long. Max character limit is 100.";
    }
    if (strlen($city) > 100) {
        $errors[] = "Your city is too long. Max character limit is 100.";
    }
    if (strlen($district) > 100) {
        $errors[] = "Your district is too long. Max character limit is 100.";
    }
    if (strlen($streetAddress) > 100) {
        $errors[] = "Your street address is too long. Max character limit is 100.";
    }
    if (strlen($jobTitle) > 100) {
        $errors[] = "Your job title is too long. Max character limit is 100.";
    }
    if (!in_array($experience, $validExperiences)) {
        $errors[] = "Invalid experience level. Please select one from the dropdown.";
    }
    if (!in_array($education, $validEducationLevels)) {
        $errors[] = "Invalid education level. Please select one from the dropdown.";
    }

    if (count($errors) === 0) {
        $statement = new mysqli_stmt($db, "INSERT INTO Applicant (AuthenticationID, FirstName, LastName, Birthdate, Gender, Email, Phone, Nationality, CountryOfResidence, 
                                           City, District, StreetAddress, JobTitle, ExperienceLevel, EducationBackground, CareerGoal)
                                           VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $statement->bind_param(
            "ssssssssssssssss",
            $authID,
            $fName,
            $lName,
            $birthdate,
            $gender,
            $email,
            $phone,
            $nationality,
            $countryOfResidence,
            $city,
            $district,
            $streetAddress,
            $jobTitle,
            $experience,
            $education,
            $careerGoal
        );
        $success = $statement->execute();

        if (!$success) {
            $errors[] = "An error happened. Please try again.";
        } else {
            header("Location: /applicant/profile");
            exit;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Setup profile - GreeLiving for Job-seekers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <link href="/assets/css/header.css" rel="stylesheet" />
    <link href="/assets/css/footer.css" rel="stylesheet" />
    <link rel="stylesheet" href="/assets/css/applicant_setup.css">

</head>

<body>
    <?php require("./components/header_applicant.php") ?>

    <main style="padding-top:100px">

        <h1>Setup profile</h1>
        <p>Let's get started with your basic information!</p>

        <?php foreach ($errors as $error): ?>
            <p class="text-danger">
                <?= $error ?>
            </p>
        <?php endforeach; ?>

        <form method="post" action="/applicant/setup">
            <label>
                First name: <input type="text" name="fName" value="<?= isset($fName) ? $fName : "" ?>" />
            </label>

            <label>
                Last name: <input type="text" name="lName" value="<?= isset($lName) ? $lName : "" ?>" />
            </label>

            <label>
                Birthdate: <input type="date" name="birthdate" value="<?= isset($birthdate) ? $birthdate : "" ?>" />
            </label>

            <label>
                Gender:
                <select name="gender">
                    <?php foreach ($validGenders as $validGender): ?>
                        <option value="<?= $validGender ?>" <?= (isset($gender) && $validGender === $gender) ? " selected" : "" ?>>
                            <?= $validGender ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Phone: <input type="text" name="phone" value="<?= isset($phone) ? $phone : "" ?>" />
            </label>

            <label>
                Nationality: <input type="text" name="nationality"
                    value="<?= isset($nationality) ? $nationality : "" ?>" />
            </label>

            <label>
                Country of residence: <input type="text" name="countryOfResidence"
                    value="<?= isset($countryOfResidence) ? $countryOfResidence : "" ?>" />
            </label>

            <label>
                City: <input type="text" name="city" value="<?= isset($city) ? $city : "" ?>" />
            </label>

            <label>
                District: <input type="text" name="district" value="<?= isset($district) ? $district : "" ?>" />
            </label>

            <label>
                Street address: <input type="text" name="streetAddress"
                    value="<?= isset($streetAddress) ? $streetAddress : "" ?>" />
            </label>

            <label>
                Job title: <input type="text" name="jobTitle" value="<?= isset($jobTitle) ? $jobTitle : "" ?>" />
            </label>

            <label>
                Experience level:
                <select name="experience">
                    <?php foreach ($validExperiences as $validExperience): ?>
                        <option value="<?= $validExperience ?>" <?= (isset($experience) && $validExperience === $experience) ? " selected" : "" ?>>
                            <?= $validExperience ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Education background:
                <select name="education">
                    <?php foreach ($validEducationLevels as $validEducationLevel): ?>
                        <option value="<?= $validEducationLevel ?>" <?= (isset($education) && $validEducationLevel === $education) ? " selected" : "" ?>>
                            <?= $validEducationLevel ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Career goal:
                <textarea name="careerGoal"><?= isset($careerGoal) ? $careerGoal : "" ?></textarea>
            </label>

            <input type="submit" value="Submit" />
        </form>

    </main>

    <?php require("./components/footer.php") ?>

</body>

</html>