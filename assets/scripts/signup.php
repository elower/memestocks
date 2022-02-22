<?php
    REQUIRE_ONCE "functions.php";
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    try {
        $hash = md5( rand(0,1000) ); //verification code
        $db = db_connect();
        $sql = "INSERT INTO users (username, email, password, verificationCode)
                VALUES (?, ?, md5( '$password' ), ?)";
        $values = [$username, $email, $hash];
        $stmt = $db->prepare($sql);
        $stmt->execute($values);

        $response = TRUE;

        /*Create Verification Email*/
        $content = "
        <head>
          <title>Verify Your MemeStocks Account</title>
          <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
          <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\"/>
          <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />
          <style type=\"text/css\">
          @import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Lato:wght@300;400;700&display=swap');
          </style>
        </head>
        <div style=\"width: 100%; height: 100%; background-color: #f1f2f5; padding-top: 45px;\">
            <div style=\"width: 350px; background-color: white; margin: auto; height: 600px; text-align: center; box-sizing: border-box; padding: 30px;\">
                <img src=\"http://memestocks.io/assets/img/logo.png\" style=\"width: 100px; height: 100px;\">
                <p style=\"font-family: 'Bebas Neue', cursive; font-size: 24px; line-height: 32px; color: #12151a\">$username, thanks for signing up for MemeStocks.</p>
                <p style=\"font-family: 'Lato', sans-serif; font-size: 18px; line-height: 24px; color: #979693\">Please confirm your email address by clicking the link below, or pasting the url into your browser.</p>
                <a href=\"http://memestocks.io/activate?email=$email&hash=$hash\" style=\"display: block; text-decoration: none; width: 100%; border-radius: 4px; font-family: 'Lato', sans-serif; font-size: 32px; line-height: 56px; color: white; background-color: #aedbf5; margin: 20px 0;\">Verify Here</a>
                <a style=\"font-family: 'Lato', sans-serif; font-size: 12px; line-height: 16px; color: #979693\">http://memestocks.io/activate?email=$email&hash=$hash</a>
            </div>
        </div>
        ";
        sendEmail($email, "noreply@memestocks.io", "Verify Your MemeStocks Account", $content);
    }
    catch (Exception $e){
        $response = $e->getMessage();
    }
    finally{
        $db = NULL;
    }
    $return_arr = array("err" => $response);
    echo json_encode($return_arr);
?>
