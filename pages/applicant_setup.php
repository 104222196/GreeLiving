<?php

// Check if this is in fact the first time log in.
require_once("./functions/applicant_auth.php");
checkApplicantNotFirstLogIn();

// Check if this a form submision
if (
    $_POST && isset($_POST["fName"], $_POST["lName"], $_POST["birthdate"], $_POST["gender"],
    $_POST["phone"], $_POST["nationality"], $_POST["countryOfRes"], $_POST["city"],
    $_POST["district"], $_POST["streetAddress"], $_POST["jobTitle"],
    $_POST["experience"], $_POST["education"], $_POST["careerGoal"])
) {
    $db = $GLOBALS["db"];
    $columns = array("AuthenticationID", "FirstName", "LastName", "Birthdate", "Gender", "Email", "Phone", "Nationality", "CountryOfResidence", "City", "District", "StreetAddress", "JobTitle", "ExperienceLevel", "EducationBackground", "CareerGoal");
    $values = array();

    $authID = '"' . $GLOBALS["auth0_applicant"]->getCredentials()->user["sub"] . '"';
    $fName = '"' . $db->real_escape_string($_POST["fName"]) . '"';
    $lName = '"' . $db->real_escape_string($_POST["lName"]) . '"';
    $birthdate = '"' . $db->real_escape_string($_POST["birthdate"]) . '"';
    $gender = '"' . $db->real_escape_string($_POST["gender"]) . '"';
    $email = '"' . $GLOBALS["auth0_applicant"]->getCredentials()->user["email"] . '"';
    $phone = '"' . $db->real_escape_string($_POST["phone"]) . '"';
    $nationality = '"' . $db->real_escape_string($_POST["nationality"]) . '"';
    $countryOfRes = '"' . $db->real_escape_string($_POST["countryOfRes"]) . '"';
    $city = '"' . $db->real_escape_string($_POST["city"]) . '"';
    $district = '"' . $db->real_escape_string($_POST["district"]) . '"';
    $streetAddress = '"' . $db->real_escape_string($_POST["streetAddress"]) . '"';
    $jobTitle = '"' . $db->real_escape_string($_POST["jobTitle"]) . '"';
    $experience = '"' . $db->real_escape_string($_POST["experience"]) . '"';
    $education = '"' . $db->real_escape_string($_POST["education"]) . '"';
    $careerGoal = '"' . $db->real_escape_string($_POST["careerGoal"]) . '"';

    array_push($values, $authID, $fName, $lName, $birthdate, $gender, $email, $phone, $nationality, $countryOfRes, $city, $district, $streetAddress, $jobTitle, $experience, $education, $careerGoal);

    $query = 'INSERT INTO Applicant (' . implode(',', $columns) . ') VALUES (' . implode(',', $values) .')';
    $result = $db->query($query);

    if ($result) {
        header("Location: " . ROUTE_URL_APPLICANT_INDEX);
        exit();
    } else {
        echo "An error happened.";
    }
}

?>

<form method="post" action="/applicant/setup">
    <label>
        First name: <input type="text" name="fName"/>
    </label>
    <label>
        Last name: <input type="text" name="lName" />
    </label>
    <label>
        Birthdate: <input type="text" name="birthdate" />
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
        Nationality: <input type="text" name="nationality" />
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
    <label>
        Experience:
        <select name="experience">
            <option value="Internship">Internship</option>
            <option value="Entry level">Entry level</option>
            <option value="Junior">Junior</option>
            <option value="Mid-level">Mid-level</option>
            <option value="Senior">Senior</option>
        </select>
    </label>
    <label>
        Education:
        <select name="education">
            <option value="Not graduated">Not graduated</option>
            <option value="Intermediate degree">Intermediate degree</option>
            <option value="High school degree">High school degree</option>
            <option value="College degree">College degree</option>
            <option value="Undergraduate degree">Undergraduate degree</option>
            <option value="Postgraduate degree">Postgraduate degree</option>
            <option value="Other">Other</option>
        </select>
    </label>
    <label>
        Career goal: <input type="text" name="careerGoal" />
    </label>
    <input type="submit" value="Submit" />
</form>