<?php
    REQUIRE_ONCE "assets/scripts/functions.php";
    session_start();
    if(isset($_SESSION['user'])){
        header ("Location: /");
        exit ();
    }

  if(isset($_GET['email']) && isset($_GET['hash'])){
    $email = $_GET['email'];
    $hash = $_GET['hash'];


    $db = db_connect();
    $sql = "SELECT email, verificationCode, verified FROM users WHERE email= ? AND verificationCode= ? AND verified='0'";
    $values = [$email, $hash];
    $stmt = $db->prepare($sql);
    $stmt -> execute($values);
    $result = $stmt->fetch();
    $db = NULL;
    $result = $result[0];

    $result = TRUE;
    if($result == TRUE){
      $userID = getUserID($email);
      if(update($userID, "verified", 1) == TRUE)
        $innerMSG = "Your account was successfully activated! <br><a href = \"login\">Log in here!</a>";
    }
    else{
        $innerMSG = "Invalid activation link.";
    }
  }
  else{
    $innerMSG = "Invalid activation link.";
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>

  <!-- Basic Page Needs
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <meta charset="utf-8">
  <title>Activate Your Account | MEMESTOCKS - Invest in the Internet</title>
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


     <!-- Smooth Scroll ––––––––––––––––––––––––––––––––––––––––––––––––––-->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script>
   $(function() {
     $('a[href*=#]:not([href=#])').click(function() {
       if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {

         var target = $(this.hash);
         target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
         if (target.length) {
           $('html,body').animate({
             scrollTop: target.offset().top
           }, 1000);
           return false;
         }
       }
     });
   });
   </script>
 <!-- SMOOTH SCROLL -->

</head>
<body>
    <?php printNav(); ?>
    <div id="body">
        <div class="section">
            <div id="response">
                <h3><?php echo($innerMSG); ?></h3>
            </div>
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


        $('#signup-form').submit(function(event) {
            var username = document.getElementById('username').value;
            var email = document.getElementById('email').value;
            var password = document.getElementById('password').value;
            var confirmPassword = document.getElementById('password2').value;
            var responsetxt = "";
            var valid = false;
            event.preventDefault();
            if(username == '' || email == '' || password == '' || confirmPassword == ''){
                responsetxt = "You must enter all fields.";
                valid = false;
            } else if(password != confirmPassword){
                responsetxt = "Your password does not match the Confirm Password field.";
                valid = false;
            } else if(username.length > 12){
                responsetxt = "Your username must be less than 12 characters long.";
                valid = false;
            } else if(password.length < 4){
                responsetxt = "Your password must be longer than 4 characters.";
                valid = false;
            } else {
                //check for repeat username/email here
                valid = true;
            }

            if(valid == true){
                $.ajax({
                    url: '/assets/scripts/signup.php',
                    type: 'post',
                    dataType: 'JSON',
                    data: { "username": username, "email": email, "password": password},
                    success: function(response) {
                        if(response.err != true){
                            document.getElementById('response').innerHTML = "<p>"+response.err+"</p>";
                            location.href = "#response";
                        }
                        else{
                            document.getElementById('response').innerHTML = "<p>You have successfully created your account. Check your email for a verification link!</p>";
                            document.getElementById('signup-form').reset();
                            location.href = "#response";
                        }
                    }
                });
            }

            document.getElementById('response').innerHTML = "<p>"+responsetxt+"</p>";
            location.href = "#response";
        });
    </script>
</body>
</html>
