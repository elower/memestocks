<?php
    REQUIRE_ONCE "functions.php";
    $value = getTotalReturn($_POST['user'], $_POST['finra']);
    $return_arr = array("val" => $value);
    echo json_encode($return_arr);
?>
