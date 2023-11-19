<?php

require_once("./functions/employer_auth.php");
checkEmployerLogIn();

$db = $GLOBALS["db"];

$statement = new mysqli_stmt($db, "SELECT * FROM Applicant WHERE ApplicantID = ?");
$statement->bind_param("s", $applicantId);
$statement->execute();
$result = $statement->get_result();

if($result->num_rows === 0) {
    echo "Applicant does not exist.";
    exit;
}

$applicant = $result->fetch_assoc();

// Get the applicant's registered courses.
$statement = new mysqli_stmt($db, "SELECT CourseName, CourseStatus FROM CourseApplicant
                                   JOIN Course ON CourseApplicant.CourseID = Course.CourseID
                                   WHERE ApplicantID = ?");
$statement->bind_param("s", $applicant["ApplicantID"]);
$statement->execute();
$courses = $statement->get_result()->fetch_all(MYSQLI_ASSOC);

echo "<pre>";
print_r($applicant);
print_r($courses);
echo "</pre>";

?>