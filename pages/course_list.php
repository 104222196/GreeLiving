<?php

$db = $GLOBALS["db"];

$statement = new mysqli_stmt($db, "SELECT * FROM Course");
$success = $statement->execute();

if (!$success) {
    echo "An error happened. Please try again later.";
    exit;
}

$courses = $statement->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Training courses - GreeLiving for Job-seekers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="/assets/css/header.css" rel="stylesheet"/>
    <link href="/assets/css/footer.css" rel="stylesheet"/>
</head>

<body>
    <?php require("./components/header_applicant.php") ?>

    <main style="padding-top:100px">
        <h1>Training courses</h1>
        
        <?php while ($row = $courses->fetch_assoc()): ?>
            <div>
                <h2><?= $row["CourseName"]?></h2>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Suscipit saepe excepturi temporibus modi eos expedita amet sit est ipsum vitae.</p>
                <a href="/applicant/courses/<?=$row["CourseID"]?>">More details</a>
            </div>
        <?php endwhile; ?>
    </main>

    <?php require("./components/footer.php") ?>
</body>

</html>