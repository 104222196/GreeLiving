<?php

$db = $GLOBALS["db"];
$query = 'SELECT * FROM Job JOIN Specialization ON Job.SpecializationID = Specialization.SpecializationID JOIN Company ON Job.CompanyID = Company.CompanyID WHERE JobID = "' . $jobId . '"';

$result = mysqli_query($db, $query);

if ($result == false || mysqli_num_rows($result) == 0) {
    header("Location: /applicant/jobsearch");
    exit();
}

$job = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Job details - GreeLiving</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</head>
<body>
    <h1><?php echo $job["JobTitle"] ?></h1>
    <p>Employer: <?php echo $job["CompanyName"] ?></p>
    <p>Application deadline: <?php echo $job["ApplicationDeadline"] ?></p>

    <form method="post" action="/applicant/save-job">
        <input type="hidden" name="jobId" value=<?php echo '"' . $jobId . '"'?>/>
        <input type="submit" value="Save job"/>
    </form>

    <a href="/applicant/apply/<?php echo $jobId?>">Apply</a>
    
</body>
</html>