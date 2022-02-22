<?php
    REQUIRE_ONCE "functions.php";
    $username = $_POST['username'];
    $password = $_POST['password'];
    try {
        $db = db_connect();
        $sql = "SELECT * FROM users WHERE user= ? and password=md5( ? )";
        $values = [$username, $password];
        $stmt = $db->prepare($sql);
        $stmt->execute($values);
        // there should only be a single record

        if($stmt->fetch(PDO::FETCH_ASSOC) == NULL){$response = "0";}
        else{$response = "2";}
        session_start();
        $_SESSION['user'] = $username;
    }
    catch (Exception $e) {
        $response = "1";
        return array();
    }
    finally{
      $db = NULL;
    }
    $return_arr = array("err" => $response);
    echo json_encode($return_arr);
?>
