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
    if (!isset($_POST, $_POST["status"])) {
        header("Location: /employer/view-application/" . $applicationId);
        exit;
    }

    $status = trim($_POST["status"]);

    if (!in_array($status, $validStatuses)) {
        $errors[] = "Please specify a valid application status.";
    }

    if (count($errors) === 0) {
        $statement = new mysqli_stmt($db, "UPDATE JobApplication SET ApplicationStatus = ? WHERE ApplicationID = ?");
        $statement->bind_param("ss", $status, $applicationId);
        $success = $statement->execute();

        if (!$success) {
            $errors[] = "An error happened while changing the status. Please try again.";
        }
    }

    if (isset($_POST["interviewFormat"])) {
        $format = trim($_POST["interviewFormat"]);

        if ($format === "in-person" && isset($_POST["inPersonStart"], $_POST["inPersonEnd"])) {
            if (count($_POST["inPersonStart"]) !== count($_POST["inPersonEnd"])) {
                $errors[] = "Please make sure your start and end dates are chosen.";
            }

            $startDates = array();
            foreach ($_POST["inPersonStart"] as $start) {
                $dateObj = DateTimeImmutable::createFromFormat("Y-m-d\\TH:i", trim($start));
                if ($dateObj === false) {
                    $errors[] = "Please format all your interview start times correctly.";
                    break;
                } else {
                    array_push($startDates, $dateObj->format("Y-m-d H:i:s"));
                }
            }

            $endDates = array();
            foreach ($_POST["inPersonEnd"] as $end) {
                $dateObj = DateTimeImmutable::createFromFormat("Y-m-d\\TH:i", trim($end));
                if ($dateObj === false) {
                    $errors[] = "Please format all your interview end times correctly.";
                    break;
                } else {
                    array_push($endDates, $dateObj->format("Y-m-d H:i:s"));
                }
            }

            if (count($errors) === 0) {
                $statement = new mysqli_stmt($db, "DELETE FROM Interview WHERE ApplicationID = ? AND InterviewTypeID = '2'");
                $statement->bind_param("s", $applicationId);
                $success = $statement->execute();

                $statement = new mysqli_stmt($db, "INSERT IGNORE INTO Interview VALUES (?, '1')");
                $statement->bind_param("s", $applicationId);
                $statement->execute();

                $statement = new mysqli_stmt($db, "INSERT IGNORE INTO InPersonInterview (ApplicationID) VALUES (?)");
                $statement->bind_param("s", $applicationId);
                $statement->execute();

                $statement = new mysqli_stmt($db, "SELECT InterviewTimeFrom, InterviewTimeTo FROM InPersonInterviewDate WHERE ApplicationID = ?");
                $statement->bind_param("s", $applicationId);
                $statement->execute();

                $result = $statement->get_result();

                while ($row = $result->fetch_assoc()) {
                    $keep = false;

                    for ($i = 0; $i < count($startDates); $i++) {
                        if ($row["InterviewTimeFrom"] === $startDates[$i] && $row["InterviewTimeTo"] === $endDates[$i]) {
                            $keep = true;
                        }
                    }

                    if ($keep === false) {
                        $statement = new mysqli_stmt($db, "DELETE FROM InPersonInterviewDate WHERE ApplicationID = ? AND InterviewTimeFrom = ? AND InterviewTimeTo = ?");
                        $statement->bind_param("sss", $applicationId, $row["InterviewTimeFrom"], $row["InterviewTimeTo"]);
                        $statement->execute();
                    }
                }

                $statement = new mysqli_stmt($db, "INSERT IGNORE INTO InPersonInterviewDate (ApplicationID, InterviewTimeFrom, InterviewTimeTo) VALUES (?, ?, ?)");
                $statement->bind_param("sss", $applicationId, $from, $to);
                for ($i = 0; $i < count($startDates); $i++) {
                    $from = $startDates[$i];
                    $to = $endDates[$i];
                    $statement->execute();
                }
            }
        } else if ($format === "on-the-go" && isset($_POST["onTheGoStart"], $_POST["onTheGoEnd"], $_POST["onTheGoLink"])) {
            $startObj = DateTimeImmutable::createFromFormat("Y-m-d\\TH:i", trim($_POST["onTheGoStart"]));
            $endObj = DateTimeImmutable::createFromFormat("Y-m-d\\TH:i", trim($_POST["onTheGoEnd"]));
            $link = trim($_POST["onTheGoLink"]);

            if ($startObj === false) {
                $errors[] = "Please format your online interview start time correctly.";
            }
            if ($endObj === false) {
                $errors[] = "Please format your online interview end time correctly.";
            }
            if ($link === "") {
                $errors[] = "Please specify a meeting link for the interview.";
            }
            if (strlen($link) > 255) {
                $errors[] = "Your meeting link is too long. The character limit is 255.";
            }

            if (count($errors) === 0) {
                $startDate = $startObj->format("Y-m-d H:i:s");
                $endDate = $endObj->format("Y-m-d H:i:s");

                $statement = new mysqli_stmt($db, "DELETE FROM Interview WHERE ApplicationID = ? AND InterviewTypeID = '1'");
                $statement->bind_param("s", $applicationId);
                $success = $statement->execute();

                $statement = new mysqli_stmt($db, "INSERT IGNORE INTO Interview VALUES (?, '2')");
                $statement->bind_param("s", $applicationId);
                $statement->execute();

                $statement = new mysqli_stmt($db, "INSERT INTO OnTheGoInterview (ApplicationID, InterviewTimeFrom, InterviewTimeTo, InterviewLink) VALUES (?,?,?,?)
                                                   ON DUPLICATE KEY UPDATE InterviewTimeFrom = ?, InterviewTimeTo = ?, InterviewLink = ?");
                $statement->bind_param("sssssss", $applicationId, $startDate, $endDate, $link, $startDate, $endDate, $link);
                $statement->execute();
            }
        } else {
            header("Location: /employer/view-application/" . $applicationId);
            exit;
        }
    }
}

// Gets the application from the database.
$statement = new mysqli_stmt($db, "SELECT JobApplication.*, Job.JobTitle, FirstName, LastName, Email, Phone
                                   FROM JobApplication 
                                   JOIN Job ON JobApplication.JobID = Job.JobID
                                   JOIN Applicant ON JobApplication.ApplicantID = Applicant.ApplicantID
                                   WHERE ApplicationID = ?");
$statement->bind_param("s", $applicationId);
$success = $statement->execute();

$result = $statement->get_result();
$application = $result->fetch_assoc();

// Gets the interview information of the application. There might be none.
$statement = new mysqli_stmt($db, "SELECT ApplicationID, InterviewTypeID FROM Interview WHERE ApplicationID = ?");
$statement->bind_param("s", $applicationId);
$statement->execute();
$result = $statement->get_result();
if ($result->num_rows > 0) {
    $interview = $result->fetch_assoc();

    if ($interview["InterviewTypeID"] == "1") {
        $statement = new mysqli_stmt($db, "SELECT InterviewTimeFrom, InterviewTimeTo FROM InPersonInterviewDate WHERE ApplicationID = ?");
        $statement->bind_param("s", $applicationId);
        $statement->execute();

        $inPersonInterviewDates = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
    } else if ($interview["InterviewTypeID"] == "2") {
        $statement = new mysqli_stmt($db, "SELECT InterviewTimeFrom, InterviewTimeTo, InterviewLink FROM OnTheGoInterview WHERE ApplicationID = ?");
        $statement->bind_param("s", $applicationId);
        $statement->execute();

        $onTheGoInterview = $statement->get_result()->fetch_assoc();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>View application - GreeLiving for Employers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <link href="/assets/css/header.css" rel="stylesheet" />
    <link href="/assets/css/footer.css" rel="stylesheet" />
    <link rel="stylesheet" href="/assets/css/application_view_employer.css">
</head>

<body>

    <?php require("./components/header_employer.php") ?>

    <main style="padding-top:100px; padding-bottom:100px" class="container-lg">

        <h1>
            View application to position
            <?= $application["JobTitle"] ?>
        </h1>

        <hr>

        <h2>Details</h2>
        <h3>Applicant details</h3>
        <p>Name:
            <?= $application["FirstName"] . " " . $application["LastName"] ?>
        </p>
        <p>Email:
            <?= $application["Email"] ?>
        </p>
        <p>Phone:
            <?= $application["Phone"] ?>
        </p>
        <p>Profile: <a href="/employer/view-applicant/<?= $application["ApplicantID"] ?>">To applicant's
                profile</a></p>

        <h3>Attached documents</h3>
        <p>CV: <a href="/uploads/<?= $application["CV"] ?>">See attached CV</a></p>
        <p>Statement of purpose:
            <?= $application["StatementOfPurpose"] ?>
        </p>
        <p>Expect to gain:
            <?= $application["ExpectToGain"] ?>
        </p>
        <p>Questions:
            <?= $application["Questions"] ?>
        </p>

        <hr>

        <h2>Edit application</h2>

        <?php foreach ($errors as $error): ?>
            <p class="text-danger">
                <?= $error ?>
            </p>
        <?php endforeach; ?>

        <form action="" method="post">

            <p class="alert alert-warning">
                Note: Your applicant will not see the interview details 
                unless you set the status to "Interviewing"
            </p>
            <label class="form-label">
                Application status:
                <select name="status" id="status" class="form-select">
                    <?php foreach ($validStatuses as $status): ?>
                        <option value="<?= $status ?>" <?= ($application["ApplicationStatus"] === $status) ? " selected" : "" ?>>
                                <?= $status ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <fieldset id="format">
                <legend>Interview format</legend>
                <label class="form-label d-block">
                    <input type="radio" name="interviewFormat" value="in-person" id="inPersonSelector" class="form-check-input"
                    <?= (isset($interview) && $interview["InterviewTypeID"] == "1") ? "checked" : "" ?> />
                    In-person 
                </label>
                <label class="form-label d-block">
                    <input type="radio" name="interviewFormat" value="on-the-go" id="onlineSelector" class="form-check-input"
                    <?= (isset($interview) && $interview["InterviewTypeID"] == "2") ? "checked" : "" ?> />
                    Online (on-the-go)
                </label>
            </fieldset>

            <fieldset id="inPerson">
                <legend>In-person interview</legend>
                <?php if (isset($inPersonInterviewDates)): ?>
                    <?php foreach ($inPersonInterviewDates as $inPersonInterviewDate): ?>
                        <div>
                            <label class="form-label">
                                Interview start:
                                <input type="datetime-local" name="inPersonStart[]" class="form-control"
                                    value="<?= DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $inPersonInterviewDate["InterviewTimeFrom"])->format("Y-m-d\\TH:i") ?>" />
                            </label>
                            <label class="form-label">
                                Interview end:
                                <input type="datetime-local" name="inPersonEnd[]" class="form-control"
                                    value="<?= DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $inPersonInterviewDate["InterviewTimeTo"])->format("Y-m-d\\TH:i") ?>" />
                            </label>
                            <button class="removeDate btn btn-outline-danger">Remove</button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <button id="addInPersonDate" class="btn btn-small btn-outline-primary">Add another date</button>
            </fieldset>

            <fieldset id="online" disabled>
                <legend>Online interview</legend>
                <label class="form-label d-block">
                    Interview start:
                    <input type="datetime-local" name="onTheGoStart" class="form-control"
                        value="<?= isset($onTheGoInterview) ? DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $onTheGoInterview["InterviewTimeFrom"])->format("Y-m-d\\TH:i") : "" ?>" />
                </label>
                <label class="form-label d-block">
                    Interview end:
                    <input type="datetime-local" name="onTheGoEnd" class="form-control"
                        value="<?= isset($onTheGoInterview) ? DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $onTheGoInterview["InterviewTimeTo"])->format("Y-m-d\\TH:i") : "" ?>" />
                </label>
                <label class="form-label d-block">
                    Link to meeting:
                    <input type="text" name="onTheGoLink" class="form-control"
                        value="<?= isset($onTheGoInterview) ? $onTheGoInterview["InterviewLink"] : "" ?>" />
                </label>
            </fieldset>

            <input type="submit" class="btn btn-small btn-outline-primary" value="Update application"/>

        </form>

    </main>

    <?php require("./components/footer.php") ?>

    <script src="/assets/js/application_view_employer.js"></script>

</body>

</html>