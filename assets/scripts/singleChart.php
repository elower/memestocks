<?php
REQUIRE_ONCE "functions.php";
if(isset($_GET['date']) && !empty($_GET['date']))
    $dates = $_GET['date'];
else
    $dates = "threemonth";

fillChart("", $_GET['finra'], $dates);
?>
