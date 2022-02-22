<?php
    session_start();
    if(isset($_SESSION['user'])){
        header ("Location: /");
        exit ();
    }

    REQUIRE_ONCE "assets/scripts/functions.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>

  <!-- Basic Page Needs
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <meta charset="utf-8">
  <title>Sign Up | MEMESTOCKS - Invest in the Internet</title>
  <meta name="description" content="">
  <meta name="author" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">


  <!-- CSS
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
    <link rel="stylesheet" href="assets/reset.css">
    <link rel="stylesheet" href="assets/animate.css">
    <link rel="stylesheet" href="assets/style.css">

    <!-- Font Awesome -->
     <script src="https://kit.fontawesome.com/a062562745.js" crossorigin="anonymous"></script>
  <!-- Favicon–––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <link rel="icon" type="image/png" href="assets/img/favicon.ico">


  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

</head>
<body>
    <?php printNav(); ?>
    <div id="body">
        <div class="section" id="signup-wrap">
            <div id="response"></div>
            <h1>Create Your Account</h1>
            <form id="signup-form" method="post">
                <input type="text" placeholder="Username" id="username" required>
                <input type="email" placeholder="Email" id="email" required>
                <input type="password" placeholder="Password" id="password" required>
                <input type="password" placeholder="Confirm Password" id="password2" required>
                <div id="tos">
                    <label class="check-container"><p>Agree to TOS</p>
                      <input type="checkbox" id="agree-tos" required>
                      <span class="checkmark"></span>
                    </label>

                    <p>View TOS</p>
                </div>
                <input type="submit" value="Sign Up">
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
            } else if(/^[a-zA-Z0-9-_.]*$/.test(username) == false){
                responsetxt = "Your username must contain only letters (A-Z a-z), numbers (0-9), dashes (-), underscores (_), and periods (.)";
                valid = false;
            } else if(password.length < 4){
                responsetxt = "Your password must be longer than 4 characters.";
                valid = false;
            } else {
                //check for repeat username/email here
                checkUserEmail(username, email).then(function(response){
                    if(response.valid == false){
                        responsetxt = "An account with that username or email already exists.";
                        valid = false;
                        document.getElementById('response').innerHTML = "<i class='far fa-frown'></i><p>"+responsetxt+"</p><i class='far fa-frown'></i>";
                        location.href = "#response";
                    } else valid = true;
                    if(valid == true){
                        $.ajax({
                            url: '/assets/scripts/signup.php',
                            type: 'post',
                            dataType: 'JSON',
                            data: { "username": username, "email": email, "password": password},
                            success: function(response) {
                                if(response.err != true){
                                    document.getElementById('response').innerHTML = "<i class='far fa-frown'><p>"+response.err+"</p><i class='far fa-frown'>";
                                }
                                else{
                                    document.getElementById('response').innerHTML = "<i class='far fa-smile-beam'></i><p>You have successfully created your account. Check your email for a verification link!</p><i class='far fa-smile-beam'></i>";
                                    document.getElementById('signup-form').reset();
                                }
                            }
                        });
                    }
                });
            }

            document.getElementById('response').innerHTML = "<i class='far fa-frown'></i><p>"+responsetxt+"</p><i class='far fa-frown'></i>";
            location.href = "#response";
        });

        function checkUserEmail(username, email){
            return $.ajax({
                url: '/assets/scripts/checkUserEmail.php',
                type: 'post',
                dataType: 'JSON',
                data: { "username": username, "email": email}
            });
        }
    </script>
</body>
</html>
