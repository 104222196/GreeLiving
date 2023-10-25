<?php

$mysql_connection = new mysqli("localhost", "root", "GnutTung@04", "greeliving");

    $query = sprintf("SELECT * FROM Applicants WHERE ApplicantID = '%s'", $mysql_connection->real_escape_string($session->user["sub"]));
    $result = $mysql_connection->query($query);

    $mysql_connection->close();
    
    if ($result->fetch_assoc() !== null) {
        header("Location: " . ROUTE_URL_APPLICANT_INDEX);
        exit;
    }

    require("home.php");

?>

<form method="post" action="/applicants/handle-create-profile">
    <label>
        First name: <input type="text" name="fName" />
    </label>
    <label>
        Last name: <input type="text" name="lName" />
    </label>
    <label>
        Age: <input type="text" name="age" />
    </label>
    <label>
        Gender:
        <select name="gender">
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Non-binary">Non-binary</option>
            <option value="Other">Other</option>
        </select>
    </label>
    <label>
        Phone: <input type="text" name="phone" />
    </label>
    <label>
        Country of residence: <input type="text" name="countryOfRes" />
    </label>
    <label>
        City: <input type="text" name="city" />
    </label>
    <label>
        District: <input type="text" name="district" />
    </label>
    <label>
        Street address: <input type="text" name="streetAddress" />
    </label>
    <label>
        Job title: <input type="text" name="jobTitle" />
    </label>
    <select name="experience">
        <option value="Internship">Internship</option>
        <option value="Entry level">Entry level</option>
        <option value="Junior">Junior</option>
        <option value="Mid-level">Mid-level</option>
        <option value="Senior">Senior</option>
    </select>
    <select name="education">
        <option value="Not graduated">Not graduated</option>
        <option value="Intermediate degree">Intermediate degree</option>
        <option value="High school degree">High school degree</option>
        <option value="College degree">College degree</option>
        <option value="Undergraduate degree">Undergraduate degree</option>
        <option value="Postgraduate degree">Postgraduate degree</option>
        <option value="Other">Other</option>
    </select>
    <label>
        Career goal: <input type="text" name="careerGoal" />
    </label>
    <input type="submit" value="Submit" />
</form>