<?php

require_once("./functions/employer_auth.php");
checkEmployerId();

$db = $GLOBALS["db"];

$statement = new mysqli_stmt($db, "");

$validExperiences = array("Internship", "Entry level", "Junior", "Mid-level", "Senior");
$validFormats = array("On-site", "Remote", "Hybrid");
$errors = array();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST, $_POST["jobTitle"], $_POST["specialization"], $_POST["deadline"], $_POST["salary"],
               $_POST["workLocation"], $_POST["experience"], $_POST["format"], $_POST["scope"], $_POST["benefits"])
    ) {
        header("Location: /employer/edit-job/" . $jobId);
        exit;
    }

    $jobTitle = trim($_POST["jobTitle"]);
    $specialization = trim($_POST["specialization"]);
    $deadline = trim($_POST["deadline"]);
    $salary = trim($_POST["salary"]);
    $workLocation = trim($_POST["workLocation"]);
    $experience = trim($_POST["experience"]);
    $format = trim($_POST["format"]);
    $scope = trim($_POST["scope"]);
    $benefits = trim($_POST["benefits"]);

    if ($jobTitle === "") {
        array_push( $errors,"Please specify the job title.");
    }
    if (!preg_match("/^\d+$/", $specialization)) {
        array_push($errors, "Please select a valid specialization.");
    }
    if ($deadline === "") {
        array_push($errors,"Please choose a valid deadline.");
    }
    $deadlineDt = DateTimeImmutable::createFromFormat("Y-m-d\\TH:i", $deadline);
    if ($deadlineDt === false) {
        array_push($errors,"Please make sure your deadline is in the correct format.");
    }
    if (!preg_match("/(^\d{1,5}$)|(^\d{1,5}\.\d{1,2}$)/", $salary)) {
        array_push($errors, "Please enter a valid salary value. It can have up to five digits before the decimal point and up to two digits after the decimal point.");
    }
    if ($workLocation === "") {
        array_push($errors,"Please specify a working location.");
    }
    if (!in_array($experience, $validExperiences)) {
        array_push($errors,"Please specify a vaid experience requirement.");
    }
    if (!in_array($format, $validFormats)) {
        array_push($errors,"Please specify a valid working format.");
    }
    if ($scope === "") {
        array_push($errors,"Please specify the scope of work.");
    }
    if ($benefits === "") {
        array_push($errors,"Please specify the work benefits.");
    }
    
    if (count($errors) === 0) {
        $deadline = $deadlineDt->format("Y-m-d H:i:s");

        $statement = new mysqli_stmt($db, "UPDATE Job SET JobTitle, ApplicationDeadline, Salary, WorkingLocation, SpecializationID, ExperienceRequirement, WorkingFormat, ScopeOfWork, Benefits");
        $statement->bind_param("ssssssssss", $companyId, $jobTitle, $deadline, $salary, $workLocation, $specialization, $experience, $format, $scope, $benefits);
        $success = $statement->execute();

        if (!$success) {
            array_push($errors,"An error happened. Please check your input and try again.");
        } else {
            header("Location: /employer/profile");
            exit;
        }
    }
}

$statement = new mysqli_stmt($db, "SELECT * FROM Specialization");
$statement->execute();
$result = $statement->get_result();

?>

<?php

foreach ($errors as $error) {
    echo '<p style="color: red">' . $error . "</p>";
}
?>

<form method="post" action="">
    <label>
        Job title: <input type="text" name="jobTitle" value="<?php echo isset($jobTitle) ? $jobTitle : "" ?>"/>
    </label>
    <label>
        Specialization:
        <select name="specialization">
            <?php
                while ($row = $result->fetch_assoc()) {
                    echo '<option value="' . $row["SpecializationID"] . '">' . $row['SpecializationName'] . '</option>';
                }
            ?>
        </select>
    <label>
        Application deadline: <input type="datetime-local" name="deadline" value="<?php echo isset($deadline) ? $deadline : "" ?>"/>
    </label>
    <label>
        Salary: <input type="text" name="salary" value="<?php echo isset($salary) ? $salary : "" ?>"/>
    </label>
    <label>
        Working location: <input type="text" name="workLocation" value="<?php echo isset($workLocation) ? $workLocation : "" ?>"/>
    </label>
    <label>
        Experience requirement:
        <select name="experience">
            <option value="Internship" default>Internship</option>
            <option value="Entry level">Entry level</option>
            <option value="Junior">Junior</option>
            <option value="Mid-level">Mid-level</option>
            <option value="Senior">Senior</option>
        </select>
    </label>
    <label>
        Working format:
        <select name="format">
            <option value="On-site" default>On-site</option>
            <option value="Remote">Remote</option>
            <option value="Hybrid">Hybrid</option>
        </select>
    </label>
    <label>
        Scope of work:
        <textarea name="scope"><?php echo isset($scope) ? $scope : "" ?></textarea>
    </label>
    <label>
        Benefits:
        <textarea name="benefits"><?php echo isset($benefits) ? $benefits : "" ?></textarea>
    </label>
    <input type="submit" value="Post job"/>
</form>
