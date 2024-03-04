<?php

$resultsPerPage = '50';

$db = $GLOBALS["db"];
$query = "SELECT JobID, JobTitle, DatePosted, ApplicationDeadline, Salary, WorkingLocation, WorkingFormat, ExperienceRequirement, SpecializationName, CompanyName, CompanySize FROM Job 
          JOIN Specialization ON Job.SpecializationID = Specialization.SpecializationID 
          JOIN Company ON Job.CompanyID = Company.CompanyID";

$filters = array();

// Get filter options
if (isset($_GET["query"])) {
    $queryString = trim($_GET["query"]);
    if ($queryString !== "") {
        $queryString = $db->real_escape_string($queryString);
        $filters[] = "(JobTitle LIKE '%" . $queryString . "%' OR CompanyName LIKE '%" . $queryString . "%')";
    }
}
if (isset($_GET["salary"])) {
    $salary = trim($_GET["salary"]);
    if (preg_match("/^\d+$/", $salary)) {
        $filters[] = "Salary >= '" . $salary . "'";
    }
}
if (isset($_GET["experience"])) {
    $experience = trim($_GET["experience"]);
    if (preg_match("/^(Internship|Entry level|Junior|Mid-level|Senior)$/", $experience)) {
        $filters[] = "ExperienceRequirement = '" . $experience . "'";
    }
}
if (isset($_GET["format"])) {
    $format = trim($_GET["format"]);
    if (preg_match("/^(Remote|Hybrid|On-site)$/", $format)) {
        $filters[] = "WorkingFormat = '" . $format . "'";
    }
}
if (isset($_GET["companySize"])) {
    $companySize = trim($_GET["companySize"]);
    if (preg_match("/^(1-20|21-50|51-100|100\+) employees$/", $companySize)) {
        $filters[] = "CompanySize = '" . $companySize . "'";
    }
}
if (isset($_GET["specialization"])) {
    $specialization = trim($_GET["specialization"]);
    if ($specialization !== "") {
        $filters[] = "Job.SpecializationID = '" . $db->real_escape_string($specialization) . "'";
    }
}

// Construct the final query
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

// Run the query and get results
$result = mysqli_query($db, $query);

if ($result === false) {
    echo "An error happened. Please try again.";
    exit;
}

$jobs = $result->fetch_all(MYSQLI_ASSOC);

// Filter select values
$experiences = array("Internship", "Entry level", "Junior", "Mid-level", "Senior");
$companySizes = array("1-20 employees", "21-50 employees", "51-100 employees", "100+ employees");
$workingFormats = array("On-site", "Remote", "Hybrid");
$specializations = mysqli_query($db, "SELECT SpecializationID, SpecializationName FROM Specialization")->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Job search - GreeLiving</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <link href="/assets/css/header.css" rel="stylesheet" />
    <link href="/assets/css/footer.css" rel="stylesheet" />
    <link rel="stylesheet" href="/assets/css/job_search.css">
</head>

<body>
    <?php require("./components/header_applicant.php") ?>

    <main style="padding-top:100px; max-width: 1440px; margin: 0 auto">
        <h1>Job search</h1>

        <h2>Filter jobs</h2>

        <form method="get" action="/applicant/jobsearch">
            <label>
                Search:
                <input type="text" name="query" placeholder="Enter job or company name" value="<?=isset($queryString) ? $queryString : "" ?>" />
            </label>

            <label>
                Expected salary (minimum $<span id="salaryValue"><?=isset($salary) ? $salary : "1"?></span>):
                <input type="range" name="salary" min="1" max="9999" id="salarySlider" value="<?=isset($salary) ? $salary : "1"?>" />
            </label>

            <label>
                Experience:
                <select name="experience" value="someval">
                    <option value="" default>All levels</option>
                    <?php foreach ($experiences as $experienceOption): ?>
                        <option 
                            value="<?=$experienceOption?>"
                            <?=isset($experience) && $experience === $experienceOption ? ' selected' : ""?>
                        >
                            <?=$experienceOption?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Company size:
                <select name="companySize">
                    <option value="" default>All sizes</option>
                    <?php foreach ($companySizes as $sizeOption): ?>
                        <option 
                            value="<?=$sizeOption?>"
                            <?=isset($companySize) && $companySize === $sizeOption ? ' selected' : ""?>
                        >
                            <?=$sizeOption?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Working format:
                <select name="format">
                    <option value="" default>All</option>
                    <?php foreach ($workingFormats as $formatOption): ?>
                        <option 
                            value="<?=$formatOption?>"
                            <?=isset($format) && $format === $formatOption ? ' selected' : ""?>
                        >
                            <?=$formatOption?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Specialization:
                <select name="specialization">
                    <option value="" default>All</option>
                    <?php foreach ($specializations as $specializationOption): ?>
                        <option 
                            value="<?=$specializationOption["SpecializationID"]?>"
                            <?=isset($specialization) && $specialization === $specializationOption["SpecializationID"] ? ' selected' : ""?>
                        >
                            <?=$specializationOption["SpecializationName"]?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <input type="submit" value="Search" />
        </form>

        <h2>Jobs</h2>

        <?php if (count($jobs) === 0): ?>
            <p>No jobs match your search criteria.</p>
        <?php else: ?>
            <div class="job-container">
            <?php foreach ($jobs as $job): ?>
                <div class="job-content">
                    <h3><?=$job["JobTitle"]?></h3>
                    <p>at <?=$job["CompanyName"]?></p>
                    <p>
                        Posted: <?=DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $job["DatePosted"])->format("d/m/Y H:i:s")?>
                    </p>
                    <p class="text-danger">
                        Application deadline: <?=DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $job["ApplicationDeadline"])->format("d/m/Y H:i:s")?>
                    </p>
                    <p>Salary: $<?=$job["Salary"]?></p>
                    <p>Working location: <?=$job["WorkingLocation"]?></p>
                    <p>Specialization: <?=$job["SpecializationName"]?></p>
                    <p>Experience requirement: <?=$job["ExperienceRequirement"]?></p>
                    <p>Working format: <?=$job["WorkingFormat"]?></p>
                    <a href="/applicant/job/<?=$job["JobID"]?>">More details</a>
                </div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </main>


    <?php require("./components/footer.php") ?>

    <script>
        document.getElementById("salarySlider").addEventListener("change", function (event) {
            document.getElementById("salaryValue").innerText = event.target.value;
        });
    </script>
</body>

</html>
