<?php
    REQUIRE_ONCE "functions.php";
    $username = $_POST['username'];
    $email = $_POST['email'];
    try {
        $db = db_connect();
        $sql = "SELECT COUNT(*) FROM users WHERE username = ? OR email = ?";
        $values = [$username, $email];
        $stmt = $db->prepare($sql);
        $stmt->execute($values);
        $result = $stmt->fetchColumn();
        if($result == 0 || $result == NULL)
            $response = TRUE;
        else
            $response = FALSE;
    }
    catch (Exception $e){
        $response = FALSE;
    }
    finally{
        $db = NULL;
    }
    $return_arr = array("valid" => $response);
    echo json_encode($return_arr);
?>
