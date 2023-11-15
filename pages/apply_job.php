<?php

require("./functions/check_applicant_login.php");

checkFirstTimeLogIn();

echo $jobId;

if ($_POST) {
    if (!file_exists("uploads") && !is_dir("uploads")) {
        mkdir("uploads");
    }
    move_uploaded_file($_FILES["cv"]["tmp_name"], "uploads/" . $_FILES["cv"]["name"]);
}

?>

<form method="post" action="/applicant/apply/<?php echo $jobId?>" enctype="multipart/form-data">
    <label>
        CV/Resume: <input type="file" name="cv" accept=".pdf"/>
    </label>
    <label>
        Statement of purpose:
        <textarea name="statementOfPurpose"></textarea>
    </label>
    <label>
        What do you expect to gain from the company?
        <textarea name="expectToGain"></textarea>
    </label>
    <label>
        Do you have any questions?
        <textarea name="questions"></textarea>
    </label>
    <input type="submit" value="Apply now"/>
</form>