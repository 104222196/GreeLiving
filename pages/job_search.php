<?php

$resultsPerPage = '50';

$db = $GLOBALS["db"];
$query = "SELECT * FROM Job JOIN Specialization ON Job.SpecializationID = Specialization.SpecializationID JOIN Company ON Job.CompanyID = Company.CompanyID";

$filters = array();

if (isset($_GET["query"])) {
    $queryString = trim($_GET["query"]);
    if ($queryString !== "") {
        array_push($filters,"(JobTitle LIKE '%" . $queryString . "%' OR CompanyName LIKE '%" . $queryString . "%')");
    }
}

if (isset($_GET["salary"])) {
    $salary = trim($_GET["salary"]);
    if (preg_match("/^\d+$/", $salary)) {
        array_push($filters, "Salary >= '" . $salary . "'");
    }
}

if (isset($_GET["experience"])) {
    $experience = trim($_GET["experience"]);
    if (preg_match("/^(Internship|Entry level|Junior|Mid-level|Senior)$/", $experience)) {
        array_push($filters, "ExperienceRequirement = '" . $experience . "'");
    }
}

if (isset($_GET["format"])) {
    $format = trim($_GET["format"]);
    if (preg_match("/^(Remote|Hybrid|On-site)$/", $format)) {
        array_push($filters, "WorkingFormat = '" . $format . "'");
    }
}

if (isset($_GET["companySize"])) {
    $companySize = trim($_GET["companySize"]);
    if (preg_match("/^(1-20|21-50|51-100|100\+) employees$/", $companySize)) {
        array_push($filters, "CompanySize = '" . $companySize . "'");
    }
}

if (isset($_GET["specialization"])) {
    $specialization = trim($_GET["specialization"]);
    if ($specialization !== "") {
        array_push($filters, "SpecializationName = '" . $specialization . "'");
    }
}

if (count($filters) > 0) {
    $query .= " WHERE " . implode(" AND ", $filters);
}

$query .= " ORDER BY DatePosted DESC LIMIT " . $resultsPerPage;

if (isset($_GET["page"])) {
    $page = trim($_GET["page"]);
    if (preg_match("/^\d+$/", $page)) {
        $query .= " OFFSET " . ($page - 1) * $resultsPerPage;
    }
}

echo $query;

$result = mysqli_query($db, $query);
$specializations = mysqli_query($db, "SELECT SpecializationName FROM Specialization ORDER BY SpecializationName");

function jobCard($job) {
    echo '<a href="/applicant/job/' . $job["JobID"] . '">';
    echo "<h2>" . $job["JobTitle"] . "</h2>";
    echo "<p>at " . $job["CompanyName"] . "</p>";
    echo "<p>posted on " . $job["DatePosted"] . "</p>";
    echo "<p>deadline " . $job["ApplicationDeadline"] . "</p>"; 
    echo "</a>";
}

$experienceOptions = array("Internship", "Entry level", "Junior", "Mid-level", "Senior");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Job search - GreeLiving</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</head>
<body>
    <h1>Job search</h1>

    <form method="get" action="/applicant/jobsearch">
        <label>
            Search:
            <input type="text" name="query" value=<?php echo isset($queryString) ? '"'.$queryString.'"' : '""' ?>/>
        </label>
        <label>
            Expected salary (minimum):
            <input type="range" name="salary" min="1" max="9999" value="1" id="salarySlider"/>
            <span id="salaryValue">1</span>
        </label>
        <label>
            Experience:
            <select name="experience" value="someval">
                <option value="" default>All levels</option>
                <?php
                    foreach ($experienceOptions as $option) {
                        echo '<option value="' . $option . '"';
                        echo isset($experience) && $experience === $option ? ' selected' : "";
                        echo '>' . $option . '</option>';
                    }
                ?>
            </select>
        </label>
        <label>
            Company size:
            <select name="companySize">
                <option value="" default>All sizes</option>
                <option value="1-20 employees">1-20 employees</option>
                <option value="21-50 employees">21-50 employees</option>
                <option value="51-100 employees">51-100 employees</option>
                <option value="100+ employees">100+ employees</option>
            </select>
        </label>
        <label>
            Working format:
            <select name="format">
                <option value="" default>All</option>
                <option value="Remote">Remote</option>
                <option value="Hybrid">Hybrid</option>
                <option value="On-site">On-site</option>
            </select>
        </label>
        <label>
            Specialization:
            <select name="specialization">
                <option value="" default>All</option>
                <?php
                
                if ($specializations) {
                    while ($row = mysqli_fetch_assoc($specializations)) {
                        echo '<option value="' . $row["SpecializationName"] . '">' . $row["SpecializationName"] . "</option>";
                    }
                }

                ?>
            </select>
        </label>
        <input type="submit" value="Search"/>
    </form>

    <?php

    if ($result == false) {
        echo "". mysqli_error($db);
    } else {
        while ($row = mysqli_fetch_assoc($result)) {
            jobCard($row);
        }
    }

    ?>

    <script>
        document.getElementById("salarySlider").addEventListener("change", function(event) {
            document.getElementById("salaryValue").innerText = event.target.value;
        });
    </script>
</body>
</html>