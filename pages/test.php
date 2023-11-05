<?php

session_start();

if (!isset($_SESSION["language"])) {
    $_SESSION["language"] = "en";
}

$contentVn = array(
    "title" => "Chào mừng bạn đến website!",
    "paragraph" => "Sth sth tiếng việt"
);

$contentEn = array(
    "title" => "Welcome to the website!",
    "paragraph" => "Sth sth english"
);


if (isset($_SESSION["language"])) {
    if ($_SESSION["language"] == "vn") {
        $content = $contentVn;
    } else {
        $content = $contentEn;
    }
} else {
    $content = $contentEn;
}

echo $_SESSION["language"];
echo "<h1>" . $content["title"] . "</h1>";
echo "<p>" . $content["paragraph"] . "</p>";

if (isset($_SESSION["language"])) {
    if ($_SESSION["language"] == "vn") {
        echo '<a href="/changeLanguage?returnUrl=' . $_SERVER["PATH_INFO"] . '?' . $_SERVER["QUERY_STRING"] . '">To English</a>';
    } else {
        echo '<a href="/changeLanguage?returnUrl=' . $_SERVER["PATH_INFO"] . '?' . $_SERVER["QUERY_STRING"] . '">To Vietnamese</a>';
    }
} else {
    echo '<a href="/changeLanguage?returnUrl=' . $_SERVER["PATH_INFO"] . '?' . $_SERVER["QUERY_STRING"] . '">To Vietnamese</a>';
}

?>