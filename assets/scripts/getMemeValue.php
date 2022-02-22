<?php
    REQUIRE_ONCE "functions.php";
    $value = getMemeValue($_POST['finra']);
    $return_arr = array("val" => $value);
    echo json_encode($return_arr);
?>
