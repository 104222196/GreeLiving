<?php

$mysql_connection = new mysqli("localhost", "root", "GnutTung@04", "greeliving");

    $query = sprintf("SELECT * FROM Applicants WHERE ApplicantID = '%s'", $mysql_connection->real_escape_string($session->user["sub"]));
    $result = $mysql_connection->query($query);

       
    if ($result->fetch_assoc() !== null) {
        header("Location: " . ROUTE_URL_APPLICANT_INDEX);
        exit;
    }
    
    $applicantID = $mysql_connection->real_escape_string($session->user["sub"]);
    $firstName = $mysql_connection->real_escape_string($_POST["fName"]);
    $lastName = $mysql_connection->real_escape_string($_POST["lName"]);
    $age = $mysql_connection->real_escape_string($_POST["age"]);
    $gender = $mysql_connection->real_escape_string($_POST["gender"]);
    $phone = $mysql_connection->real_escape_string($_POST["phone"]);
    $email = $mysql_connection->real_escape_string($session->user["email"]);
    $nationality = $mysql_connection->real_escape_string($_POST["nationality"]);
    $countryOfRes = $mysql_connection->real_escape_string($_POST["countryOfRes"]);
    $city = $mysql_connection->real_escape_string($_POST["city"]);
    $district = $mysql_connection->real_escape_string($_POST["district"]);
    $streetAddress = $mysql_connection->real_escape_string($_POST["streetAddress"]);
    $jobTitle = $mysql_connection->real_escape_string($_POST["jobTitle"]);
    $experience = $mysql_connection->real_escape_string($_POST["experience"]);
    $education = $mysql_connection->real_escape_string($_POST["education"]);
    $careerGoal = $mysql_connection->real_escape_string($_POST["careerGoal"]);

    $query = sprintf("INSERT INTO Applicants VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')", $applicantID, $firstName, $lastName, $age, $gender, $phone, $email, $nationality, $countryOfRes, $city, $district, $streetAddress, $jobTitle, $experience, $education, $careerGoal);

    $result = $mysql_connection->query($query);

    if ($mysql_connection->errno === 0) {
        header("Location: " . ROUTE_URL_APPLICANT_INDEX);
        exit;
    }

    echo "An error occured";

?>