<?php

require_once("./functions/employer_auth.php");
checkEmployerId();

$db = $GLOBALS["db"];

// Gets the employer's information.
$statement = new mysqli_stmt($db, "SELECT * FROM Company WHERE CompanyID = ?");
$statement->bind_param("s", $_SESSION["employerId"]);
$success = $statement->execute();

if (!$success) {
    echo "An error happened. We cannot show your profile at this time.";
    exit;
}

$result = $statement->get_result();
$employer = $result->fetch_assoc();

// Get the list of job postings.
$statement = new mysqli_stmt($db, "SELECT * FROM Job WHERE CompanyID = ?");
$statement->bind_param("s", $_SESSION["employerId"]);
$success = $statement->execute();

if (!$success) {
    echo "Cannot retrieve your job postings right now.";
} else {
    $jobs = $statement->get_result();
}

// Gets the list of applications to the company.
$statement = new mysqli_stmt($db, "SELECT * FROM JobApplication JOIN Job ON JobApplication.JobID = Job.JobID WHERE CompanyID = ? ORDER BY TimeOfApplication DESC");
$statement->bind_param("s", $_SESSION["employerId"]);
$success = $statement->execute();

if (!$success) {
    echo "Cannot retrieve your job applications right now.";
} else {
    $applications = $statement->get_result();
}

// Gets the list of upcoming interviews.
$interviews = array();

$statement = new mysqli_stmt($db, "SELECT InPersonInterview.ApplicationID, InPersonInterview.InterviewTypeID, Applicant.FirstName, Applicant.LastName, InterviewTimeFrom, InterviewTimeTo 
                                   FROM InPersonInterview
                                   JOIN InPersonInterviewDate ON InPersonInterview.ApplicationID = InPersonInterviewDate.ApplicationID
                                   JOIN JobApplication ON InPersonInterview.ApplicationID = JobApplication.ApplicationID
                                   JOIN Job ON JobApplication.JobID = Job.JobID
                                   JOIN Applicant ON JobApplication.ApplicantID = Applicant.ApplicantID
                                   WHERE Job.CompanyID = ? AND Booked != '0' AND InterviewTimeTo > NOW();");
$statement->bind_param("s", $_SESSION["employerId"]);
$statement->execute();
$result = $statement->get_result();

while ( $row = $result->fetch_assoc() ) {
    $interviews[] = $row;
}

$statement = new mysqli_stmt($db, "SELECT OnTheGoInterview.ApplicationID, OnTheGoInterview.InterviewTypeID, Applicant.FirstName, Applicant.LastName, InterviewTimeFrom, InterviewTimeTo, InterviewLink 
                                   FROM OnTheGoInterview
                                   JOIN JobApplication ON OnTheGoInterview.ApplicationID = JobApplication.ApplicationID
                                   JOIN Job ON JobApplication.JobID = Job.JobID
                                   JOIN Applicant ON JobApplication.ApplicantID = Applicant.ApplicantID
                                   WHERE Job.CompanyID = ? AND InterviewTimeTo > NOW();");
$statement->bind_param("s", $_SESSION["employerId"]);
$statement->execute();
$result = $statement->get_result();

while ( $row = $result->fetch_assoc() ) {
    $interviews[] = $row;
}

function compareInterviewDates($a, $b) {
    if ($a["InterviewTimeFrom"] < $b["InterviewTimeTo"]) {
        return -1;
    } else {
        return 1;
    }
}

usort( $interviews, "compareInterviewDates");

echo "<pre>";
print_r($interviews);
echo "</pre>";
?>