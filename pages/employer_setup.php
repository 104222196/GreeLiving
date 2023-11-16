<?php

require_once("./functions/employer_auth.php");
checkEmployerNotFirstLogIn();

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

    $validSizes = array("1-20 employees", "21-50 employees", "51-100 employees", "100+ employees");

    if ($companyName === "") {
        array_push($errors, "Please enter a name for your company.");
    }
    if (!in_array($size, $validSizes, true)) {
        array_push($errors, "Invalid company size.");
    }
    if (!preg_match("/^\d{10}$/", $phone)) {
        array_push($errors, "Please enter a phone number of 10 digits.");
    }
    if ($introduction === "") {
        array_push($errors, "Please enter an introduction for your company.");
    }

    if (count($errors) === 0) {
        $db = $GLOBALS["db"];
        $statement = new mysqli_stmt($db, "INSERT INTO Company (AuthenticationID, CompanyName, CompanySize, Phone, Email, Introduction) VALUES (?,?,?,?,?,?)");
        $statement->bind_param("ssssss", $authID, $companyName, $size, $phone, $email, $introduction);
        $success = $statement->execute();

        if (!$success) {
            array_push($errors, "An error happened. Please try again.");
        } else {
            header("Location: /employer/profile");
            exit;
        }
    }
}

?>

<?php

foreach ($errors as $error) {
    echo "<p>" . $error ."</p>";
}
?>

<form method="post" action="">
    <label>
        Company name: <input type="text" name="companyName"/>
    </label>
    <label>
        Company size:
        <select name="size">
            <option value="1-20 employees" default>1-20 employees</option>
            <option value="21-50 employees">21-50 employees</option>
            <option value="51-100 employees">51-100 employees</option>
            <option value="100+ employees">100+ employees</option>
        </select>
    </label>
    <label>
        Phone number: <input type="text" name="phone"/>
    </label>
    <label>
        Company introduction:
        <textarea name="introduction"></textarea>
    </label>
    <input type="submit" value="Submit"/>
</form>