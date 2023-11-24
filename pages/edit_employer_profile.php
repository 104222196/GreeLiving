<?php

require_once("./functions/employer_auth.php");
checkEmployerId();

$db = $GLOBALS["db"];

// Gets the current information.
$statement = new mysqli_stmt($db, "SELECT CompanyName, CompanySize, Phone, Introduction FROM Company WHERE CompanyID = ?");
$statement->bind_param("s", $_SESSION["employerId"]);
$success = $statement->execute();

if (!$success) {
    echo "An error happened. Please try again.";
    exit;
}

$employer = $statement->get_result()->fetch_assoc();

$companyName = $employer["CompanyName"];
$companySize = $employer["CompanySize"];
$phone = $employer["Phone"];
$introduction = $employer["Introduction"];

// Handles POST request.
$validSizes = array("1-20 employees", "21-50 employees", "51-100 employees", "100+ employees");
$errors = array();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST, $_POST["companyName"], $_POST["size"], $_POST["phone"], $_POST["introduction"])) {
        header("Location: /employer/edit-profile");
        exit;
    }

    $companyName = trim($_POST["companyName"]);
    $companySize = trim($_POST["size"]);
    $phone = trim($_POST["phone"]);
    $introduction = trim($_POST["introduction"]);

    if ($companyName === "") {
        $errors[] = "Please specify a company name.";
    }
    if (!in_array($companySize, $validSizes)) {
        $errors[] = "Company size invalid.";
    }
    if (!preg_match("/^\d{10}$/", $phone)) {
        $errors[] = "Please enter a phone number of exactly 10 digits.";
    }
    if ($introduction === "") {
        $errors[] = "Please specify an introduction to your company.";
    }

    if (count($errors) === 0) {
        $statement = new mysqli_stmt($db, "UPDATE Company
                                           SET CompanyName = ?, CompanySize = ?, Phone = ?, Introduction = ?
                                           WHERE CompanyID = ?");
        $statement->bind_param("sssss", $companyName, $companySize, $phone, $introduction, $_SESSION["employerId"]);
        $success = $statement->execute();

        if (!$success) {
            $errors[] = "An error happened. Please try again.";
        } else {
            $successMessage = "Your profile has been successfully updated.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Edit profile - GreeLiving for Employers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <link href="/assets/css/header.css" rel="stylesheet" />
    <link href="/assets/css/footer.css" rel="stylesheet" />
    <link rel="stylesheet" href="/assets/css/edit_employer_profile.css">

</head>

<body>
    <?php require("./components/header_employer.php") ?>

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

        <form method="post" action="">
            <label>
                Company name: <input type="text" name="companyName" value="<?= $companyName ?>" />
            </label>

            <label>
                Company size:
                <select name="size">
                    <?php foreach ($validSizes as $validSize): ?>
                        <option value="<?= $validSize ?>" <?= $validSize === $companySize ? " selected" : "" ?>>
                            <?= $validSize ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Phone: <input type="text" name="phone" value="<?= $phone ?>" />
            </label>

            <label>
                Company's introduction:
                <textarea name="introduction"><?= $introduction ?></textarea>
            </label>

            <input type="submit" value="Edit" />
        </form>

    </main>

    <?php require("./components/footer.php") ?>

</body>

</html>