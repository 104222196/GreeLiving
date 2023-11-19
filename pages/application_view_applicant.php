<?php

require_once("./functions/applicant_auth.php");
checkApplicantId();

$db = $GLOBALS["db"];

// Checks if this applicant has the rights to access this application.
$statement = new mysqli_stmt($db, "SELECT ApplicantID FROM JobApplication
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

if ($application["ApplicantID"] !== $_SESSION["applicantId"]) {
    echo "You do not have permission to view this page.";
    exit;
}

// Handles post requests
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset( $_POST, $_POST["bookedDate"])) {
        header("Location: /applicant/view-application/" . $applicationId);
        exit;
    }

    $dates = explode("|", $_POST["bookedDate"]);

    if (count($dates) !== 2) {
        header("Location: /applicant/view-application/" . $applicationId);
        exit;
    }

    $statement = new mysqli_stmt($db, "UPDATE InPersonInterviewDate SET Booked = '0' WHERE ApplicationID = ?");
    $statement->bind_param("s", $applicationId);
    $statement->execute();

    $statement = new mysqli_stmt($db, "UPDATE InPersonInterviewDate SET Booked = '1' WHERE ApplicationID = ? AND InterviewTimeFrom = ? AND InterviewTimeTo = ?");
    $statement->bind_param("sss", $applicationId, $dates[0], $dates[1]);
    $statement->execute();
}

// Gets the application info
$statement = new mysqli_stmt($db, "SELECT JobApplication.*, Job.* FROM JobApplication 
                                   JOIN Job ON JobApplication.JobID = Job.JobID
                                   WHERE ApplicationID = ?");
$statement->bind_param("s", $applicationId);
$success = $statement->execute();

$result = $statement->get_result();
$application = $result->fetch_assoc();

// Gets the interview information, if the status is "Interviewing"
if ($application["ApplicationStatus"] === "Interviewing") {
    $statement = new mysqli_stmt($db, "SELECT ApplicationID, InterviewTypeID FROM Interview WHERE ApplicationID = ?");
    $statement->bind_param("s", $applicationId);
    $statement->execute();
    $result = $statement->get_result();
    if ($result->num_rows > 0) {
        $interview = $result->fetch_assoc();
    
        if ($interview["InterviewTypeID"] == "1") {
            $statement = new mysqli_stmt($db, "SELECT InterviewTimeFrom, InterviewTimeTo, Booked FROM InPersonInterviewDate WHERE ApplicationID = ?");
            $statement->bind_param("s", $applicationId);
            $statement->execute();
    
            $inPersonInterviewDates = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
        } else if ( $interview["InterviewTypeID"] == "2") {
            $statement = new mysqli_stmt($db, "SELECT InterviewTimeFrom, InterviewTimeTo, InterviewLink FROM OnTheGoInterview WHERE ApplicationID = ?");
            $statement->bind_param("s", $applicationId);
            $statement->execute();
    
            $onTheGoInterview = $statement->get_result()->fetch_assoc();
        }
    }
}

echo "<pre>";
print_r($application);
if (isset($inPersonInterviewDates)) {
    print_r($inPersonInterviewDates);
}
if (isset($onTheGoInterview)) {
    print_r($onTheGoInterview);
}
echo "</pre>";

?>

<?php
if (isset($inPersonInterviewDates)) {
    echo '<form method="post" action="">';
    foreach ($inPersonInterviewDates as $inPersonInterviewDate) {
        echo '<div>';
        echo '<label>';
        echo '<input type="radio" name="bookedDate" value="' . $inPersonInterviewDate['InterviewTimeFrom'] . '|' . $inPersonInterviewDate["InterviewTimeTo"] . '"';
        echo $inPersonInterviewDate["Booked"] ? "checked" : "";
        echo '/>';
        echo DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $inPersonInterviewDate['InterviewTimeFrom'])->format("l, d-M-o h:i:s A");
        echo ' to ';
        echo DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $inPersonInterviewDate['InterviewTimeTo'])->format("l, d-M-o h:i:s A");
        echo '</label>';
        echo '</div>';
    }
    echo '<input type="submit" value="Book"/>';
    echo '</form>';
}

?>