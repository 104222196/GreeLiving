<?php

session_start();

if ($_SESSION["language"] == "vn") {
    $_SESSION["language"] = "en";
} else {
    $_SESSION["language"] = "vn";
}

$redirect = "Location: ";

if (isset($_GET["returnUrl"])) {
    $redirect .= $_GET["returnUrl"] != "" ? $_GET["returnUrl"] : "/";
} else {
    $redirect .= "/";
}

header($redirect);

?>