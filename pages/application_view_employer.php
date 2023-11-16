<?php

// Allows employers to change application status.
// url: /employer/view-application/:id

require_once("./functions/employer_auth.php");
checkEmployerId();

$db = $GLOBALS["db"];

// Checks if this employer has the rights to access this application.
$statement = new mysqli_stmt($db, "SELECT CompanyID FROM JobApplication
                                   JOIN Job ON JobApplication.JobID = Job.JobID
                                   WHERE ApplicationID = ?");
$statement->bind_param("s", $applicationId);
$success = $statement->execute();

if (!$success) {
    echo "An error happened. Please try again.";
    exit;
}

$result = $statement->get_result();

if ($result->num_rows == 0) {
    echo "Application not found";
    exit;
}

$application = $result->fetch_assoc();

if ($application["CompanyID"] !== $_SESSION["employerId"]) {
    echo "You do not have permission to view this page.";
    exit;
}

// Defines the list of valid application statuses.
$validStatuses = array("Applying", "Reviewing", "Failed", "Succeeded", "Interviewing");

// Array of errors to show the user.
$errors = array();

// Handles POST request.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST, $_POST["updateStatus"], $_POST["status"])) {
        header("Location: /employer/view-application/" . $applicationId);
        exit;
    }

    $status = trim($_POST["status"]);

    if (!in_array($status, $validStatuses)) {
        array_push($errors, "Please specify a valid application status.");
    }

    if (count($errors) === 0) {
        $statement = new mysqli_stmt($db, "UPDATE JobApplication SET ApplicationStatus = ? WHERE ApplicationID = ?");
        $statement->bind_param("ss", $status, $applicationId);
        $success = $statement->execute();

        if (!$success) {
            array_push($errors, "An error happened while changing the status. Please try again.");
        } else {
            header("Location: /employer/view-application/" . $applicationId);
            exit;
        }
    }
}

$statement = new mysqli_stmt($db, "SELECT JobApplication.*, Job.*, FirstName, LastName, Birthdate, Gender, Email, Phone, Nationality, Applicant.JobTitle, ExperienceLevel, EducationBackground
                                   FROM JobApplication 
                                   JOIN Job ON JobApplication.JobID = Job.JobID
                                   JOIN Applicant ON JobApplication.ApplicantID = Applicant.ApplicantID
                                   WHERE ApplicationID = ?");
$statement->bind_param("s", $applicationId);
$success = $statement->execute();

$result = $statement->get_result();
$application = $result->fetch_assoc();


echo "<pre>";
print_r($application);
echo "</pre>";


?>

<form action="" method="post">
    <label>
        Application status:
        <select name="status">
            <?php
            foreach ($validStatuses as $status) {
                echo '<option value="' . $status . '"';
                if ($application["ApplicationStatus"] === $status) {
                    echo " selected";
                }
                echo '>' . $status . '</option>';
            }
            ?>
        </select>
    </label>
    <input type="submit" name="updateStatus" value="Update status"/>
</form>

<form action="" method="post">
    <label>
        Some text:
        <input type="text" name="text"/>
    </label>
    <input type="submit" name="schedule" value="Schedule"/>
</form>