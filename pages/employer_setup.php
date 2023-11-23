<?php

require_once("./functions/employer_auth.php");
checkEmployerNotFirstLogIn();

$validSizes = array("1-20 employees", "21-50 employees", "51-100 employees", "100+ employees");

$errors = array();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST, $_POST["companyName"], $_POST["size"], $_POST["phone"], $_POST["introduction"])) {
        header("Location: /employer/setup");
        exit;
    }

    $authID = getEmployerAuthId();
    $companyName = trim($_POST["companyName"]);
    $size = trim($_POST["size"]);
    $phone = trim($_POST["phone"]);
    $email = $GLOBALS["auth0_employer"]->getCredentials()->user["email"];
    $introduction = trim($_POST["introduction"]);

    if ($companyName === "") {
        $errors[] = "Please enter a name for your company.";
    }
    if (!in_array($size, $validSizes, true)) {
        $errors[] = "Invalid company size.";
    }
    if (!preg_match("/^\d{10}$/", $phone)) {
        $errors[] = "Please enter a phone number of 10 digits.";
    }
    if ($introduction === "") {
        $errors[] = "Please enter an introduction for your company.";
    }

    if (count($errors) === 0) {
        $db = $GLOBALS["db"];
        $statement = new mysqli_stmt($db, "INSERT INTO Company (AuthenticationID, CompanyName, CompanySize, Phone, Email, Introduction) VALUES (?,?,?,?,?,?)");
        $statement->bind_param("ssssss", $authID, $companyName, $size, $phone, $email, $introduction);
        $success = $statement->execute();

        if (!$success) {
            $errors[] = "An error happened. Please try again.";
        } else {
            header("Location: /employer/profile");
            exit;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Setup profile - GreeLiving for Employers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <link href="/assets/css/header.css" rel="stylesheet" />
    <link href="/assets/css/footer.css" rel="stylesheet" />
    <link rel="stylesheet" href="/assets/css/employer_setup.css">

</head>

<body>
    <?php require("./components/header_employer.php") ?>

    <main style="padding-top:100px">

        <h1>Setup profile</h1>
        <p>Let's get started with your basic information!</p>

        <?php foreach ($errors as $error): ?>
            <p class="text-danger">
                <?= $error ?>
            </p>
        <?php endforeach; ?>

        <form method="post" action="">
            <label>
                Company name: <input type="text" name="companyName" value="<?=isset($companyName) ? $companyName : ""?>"/>
            </label>

            <label>
                Company size:
                <select name="size">
                    <?php foreach ($validSizes as $validSize): ?>
                        <option value="<?=$validSize?>" <?=(isset($size) && $size === $validSize) ? " selected" : "" ?>>
                            <?= $validSize ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Phone number: <input type="text" name="phone" value="<?=isset($phone) ? $phone : ""?>"/>
            </label>

            <label>
                Company introduction:
                <textarea name="introduction"><?=isset($introduction) ? $introduction : ""?></textarea>
            </label>

            <input type="submit" value="Submit" />
        </form>

    </main>

    <?php require("./components/footer.php") ?>
</body>

</html>