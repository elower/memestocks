<?php
    session_start();
    if(isset($_SESSION['user'])){
        header ("Location: /");
        exit ();
    }
    $response = "";

    REQUIRE_ONCE "assets/scripts/functions.php";
    if(isset($_POST['username']) && isset($_POST['password'])){
        $username = $_POST['username'];
        $password = $_POST['password'];
        try {
            $db = db_connect();
            $sql = "SELECT * FROM users WHERE username= ? and password=md5( ? )";
            $values = [$username, $password];
            $stmt = $db->prepare($sql);
            $stmt->execute($values);
            // there should only be a single record

            if($stmt->fetchColumn() == NULL){$response = "<i class='far fa-frown'></i><p>An account with that username and password does not exist.</p><i class='far fa-frown'></i>";}
            else{
                session_start();
                $_SESSION['user'] = $username;
                header ("Location: portfolio");
                exit ();
            }
        }
        catch (Exception $e) {
            $response = "SQL Error";
        }
        finally{
          $db = NULL;
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>

  <!-- Basic Page Needs
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <meta charset="utf-8">
  <title>Log In | MEMESTOCKS - Invest in the Internet</title>
  <meta name="description" content="">
  <meta name="author" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">


  <!-- CSS
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
    <link rel="stylesheet" href="assets/reset.css">
    <link rel="stylesheet" href="assets/animate.css">
    <link rel="stylesheet" href="assets/style.css">


  <!-- Favicon–––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <link rel="icon" type="image/png" href="assets/img/favicon.ico">
  <!-- Font Awesome -->
   <script src="https://kit.fontawesome.com/a062562745.js" crossorigin="anonymous"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>


</head>
<body>
    <?php printNav(); ?>
    <div id="body">
        <div class="section" id="login-wrap">
            <div id="response"> <?php echo($response); ?></div>
            <h1>Log In</h1>
            <form id="login-form" method="post" action="">
                <input type="text" placeholder="Username" id="username" name="username" required>
                <input type="password" placeholder="Password" id="password" name="password" required>
                <input type="submit" value="Log In">
            </form>
        </div>
    </div>
    <footer>
        <div class="third">
            <img src="assets/img/logo.png">
        </div>
        <div class="third border">
            <p>Privacy Policy</p>
            <p>Terms of Service</p>
            <p><a class="link" href="mailto:contact@memestocks.io">contact@memestocks.io</a></p>
        </div>
        <div class="third border">
            <p>Copyright 2020 Memestocks</p>
            <p id="disclaimer">No real currency is involved in any way with this project.</p>
        </div>
        <p>Web Design & Development by <a href="http://www.ghostdesigns.me" target="_blank" class="link gd">ghostdesigns.me<svg class="underline"><path class="underline--path" d=""><animate class="underline--animation" attributeName="d" dur="0.3s" begin="indefinite" fill="freeze"></animate></path></svg></a></p>
    </footer>
    <script>

    /*Hover Effect*/
    var width = $('.underline').width();

    var steps = 25;
    var height = 6;
    var step_size = width/steps;

    var d_animated = ['M0', '1'];
    var d_normal = ['M0', '1'];

    for (var i=1; i<=steps; i++) {
      d_normal.push(step_size*(-0.5 + i), 1, step_size*i, 1);
      d_animated.push(step_size*(-0.5 + i), height, step_size*i, 1);
    }

    $(document).ready(function() {
      $('.underline--path').attr('d', d_normal.join(' '));

      $('.gd').hover(function() {
        $('.underline--animation').attr({from: d_normal.join(' '), to: d_animated.join(' ')})
      	$('.underline--animation')[0].beginElement();
      }, function() {
        $('.underline--animation').attr({to: d_normal.join(' '), from: d_animated.join(' ')})
      	$('.underline--animation')[0].beginElement();
      });
    });


    $(document).ready(function(){
        $('#navbar').css("background-color", "#12151a");
    });
    </script>
</body>
</html>
