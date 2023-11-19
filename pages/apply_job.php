<?php

// Check if the user has logged in.
require_once("./functions/applicant_auth.php");
checkApplicantId();

$errors = array();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST["statementOfPurpose"], $_POST["expectToGain"], $_POST["questions"], $_FILES, $_FILES["cv"])) {
        header("Location: /applicant/apply/" . $jobId);
        exit();
    }
    
    $applicantId = $_SESSION["applicantId"];
    $fileName = uniqid() . "-" . trim($_FILES["cv"]["name"]);
    $statementOfPurpose = trim($_POST["statementOfPurpose"]);
    $expectToGain = trim($_POST["expectToGain"]);
    $questions = trim($_POST["questions"]);

    if ($_FILES["cv"]["error"] == 4) {
        array_push($errors, "Please upload your CV.");
    }
    if ($_FILES["cv"]["size"] > 5242880) {
        array_push($errors, "Please make sure your CV does not exceed 5MB in size.");
    }
    if (!preg_match("/.+\.pdf$/", $fileName)) {
        array_push($errors, "Please make sure your CV is in .pdf format.");
    }
    if ($statementOfPurpose == "") {
        array_push($errors, "Please fill in your statement of purpose.");
    }
    if ($expectToGain == "") {
        array_push($errors, "Please specify what you expect to gain from the company.");
    }

    if (count($errors) == 0) {
        $uploadSuccess = move_uploaded_file($_FILES["cv"]["tmp_name"], "uploads/" . $fileName);

        if ($uploadSuccess) {
            $db = $GLOBALS["db"];
            $statement = new mysqli_stmt($db, "INSERT INTO JobApplication (JobID, ApplicantID, CV, StatementOfPurpose, ExpectToGain, Questions, ApplicationStatus)
                                            VALUES (?, ?, ?, ?, ?, ?, 'Applying')");
            $statement->bind_param("ssssss", $jobId, $applicantId, $fileName, $statementOfPurpose, $expectToGain, $questions);
            try {
                $success = $statement->execute();

                if (!$success) {
                    array_push($errors, "An error happened. Please try again.");
                } else {
                    header("Location: /applicant/profile");
                    exit;
                }
            } catch (Exception $e) {
                array_push($errors, $e->getMessage());
            }            
        } else {
            array_push($errors, "An error happened when uploading your CV. Please try again.");
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Applying - GreeLiving</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</head>
<body>

<?php
    foreach ($errors as $error) {
        echo "<p>" . $error . "</p>";
    }
?>
    
<form method="post" action="/applicant/apply/<?php echo $jobId ?>" enctype="multipart/form-data">
    <label>
        CV/Resume: <input type="file" name="cv" accept=".pdf" id="cv"/>
    </label>
    <label>
        Statement of purpose:
        <textarea name="statementOfPurpose"><?php echo isset($statementOfPurpose) ? $statementOfPurpose : ""; ?></textarea>
    </label>
    <label>
        What do you expect to gain from the company?
        <textarea name="expectToGain"><?php echo isset($expectToGain) ?$expectToGain : ""; ?></textarea>
    </label>
    <label>
        Do you have any questions?
        <textarea name="questions"><?php echo isset($questions) ? $questions : ""; ?></textarea>
    </label>
    <input type="submit" name="submit" value="Submit"/>
</form>

<script>
    document.getElementById("cv").onchange = function() {
        if (this.files[0].size > 5242880){
            alert("Your file is too big! A file cannot exceed 5MB." + this.files[0].size);
            this.value = "";
        }
    }
</script>
    
</body>
</html>