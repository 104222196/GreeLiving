<?php

require_once("./functions/employer_auth.php");
checkEmployerId();

$db = $GLOBALS["db"];
$statement = new mysqli_stmt($db, "SELECT * FROM Company WHERE CompanyID = ?");
$statement->bind_param("s", $_SESSION["employerId"]);
$success = $statement->execute();

if (!$success) {
    echo "An error happened. We cannot show your profile at this time.";
    exit;
}

$result = $statement->get_result();
$employer = $result->fetch_assoc();

$statement = new mysqli_stmt($db, "SELECT * FROM Job WHERE CompanyID = ?");
$statement->bind_param("s", $_SESSION["employerId"]);
$success = $statement->execute();

if (!$success) {
    echo "Cannot retrieve your job postings right now.";
} else {
    $result = $statement->get_result();
}

echo "<pre>";
while ( $row = $result->fetch_assoc() ) {
    print_r($row);
}
echo "</pre>";

$statement = new mysqli_stmt($db, "SELECT * FROM JobApplication JOIN Job ON JobApplication.JobID = Job.JobID WHERE CompanyID = ? ORDER BY TimeOfApplication DESC");
$statement->bind_param("s", $_SESSION["employerId"]);
$success = $statement->execute();

if (!$success) {
    echo "Cannot retrieve your job applications right now.";
} else {
    $result = $statement->get_result();
}

echo "<pre>";
while ( $row = $result->fetch_assoc() ) {
    print_r($row);
}
echo "</pre>";
?>