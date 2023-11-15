<?php

$pt = "/^(cat|dog|rat)$/";

echo preg_match($pt, "cat");
echo preg_match($pt, "dog");
echo preg_match($pt, "rat");
echo preg_match($pt, "catto");
echo preg_match($pt, "dogged");
echo preg_match($pt, "ratty");
echo preg_match($pt, "acat");
echo preg_match($pt, "adog");
echo preg_match($pt, "arat");

echo preg_match("/^\d*$/", "");

echo "Hello " . "123" * "5";

?>