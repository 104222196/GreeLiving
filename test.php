<?php

$pattern = "/(^\d{1,5}$)|(^\d{1,5}\.\d{1,2}$)/";

echo preg_match($pattern,"");
echo preg_match($pattern,"12345");
echo preg_match($pattern,"123456");
echo preg_match($pattern,"12345.1");
echo preg_match($pattern,"12345.12");
echo preg_match($pattern,"12345.123");
echo preg_match($pattern,"123456.12");

?>