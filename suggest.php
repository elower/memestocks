<?php
    session_start();
    REQUIRE_ONCE "assets/scripts/functions.php";
    if(isset($_POST['memename'])){
        $memename = $_POST['memename'];
        $username = $_POST['username'];
        if(isset($_POST['autoinvest']) && $_POST['autoinvest'] == "invest")
            $autoInvest = 1;
        else
            $autoInvest = 0;
        try {
            $db = db_connect();
            $sql = "INSERT INTO memesuggestions (username, memeName, autoInvest)
            VALUES (?, ?, ?);";
            $values = [$memename, $username, $autoInvest];
            $stmt = $db->prepare($sql);
            $stmt->execute($values);
            $response = "<i class='far fa-smile-beam'></i><p>Thanks for your input. We really appreciate your help!</p><i class='far fa-smile-beam'></i>";
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
  <title>Suggest a Meme | MEMESTOCKS - Invest in the Internet</title>
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
        <div class="section" id="suggest-wrap">
            <div id="response"> <?php echo($response); ?></div>
            <h1>Suggest a Meme</h1>
            <form id="login-form" method="post" action="">
                <input type="text" placeholder="Meme Name" name="memename" required>
                <input type="text" placeholder="Your Username" name="username" <?php if(isset($_SESSION['user'])) echo("value='" . $_SESSION['user'] . "'"); ?>>
                <label class="check-container"><p id="tooltip" data-tooltip="Automatically add this meme to your portfolio when it gets approved - with no cost to you.">Automatic Investment <i class="far fa-question-circle"></i></p>

                  <input type="checkbox" name="autoinvest" value="invest">
                  <span class="checkmark"></span>
                </label>
                <input type="submit" value="Post Suggestion">
            </form>
            <h3>Just fill out these fields and we'll do the rest.</h3>
            <p>You don't have to enter in a Username. If you don't have a MemeStocks account, just leave the Username field blank. But if you do have an account, listed below are all the cool rewards you could get for contributing under your username!</p>
        </div>

        <div class="section" id="rewards">
            <h1>Contribution Rewards</h1>
            <div class="box lightpink">
                <h3>Extra MemeCoins</h3>
                <p>If your contribution is approved, you'll get some MemeCoins added to your account.
                    To be specific, you'll get 2x the starting calculated price
                    of the stock for the meme you contribute!
                </p><i class="fas fa-coins"></i>
            </div>
            <div class="box red">
                <h3>Automatic Investment</h3>
                <p>If you check the "Automatic Investment" box in the
                    Meme Suggestion form, in addition to the extra MemeCoins you
                    receive, you will also automatically have 1 share of that meme
                    added to your portfolio - with no cost to you!
                </p><i class="fas fa-chart-line"></i>
            </div>
            <div class="box pink">
                <h3>Recognition</h3>
                <p>When your suggested meme is live on the site, we will have a
                    link to your MemeStocks portfolio at the bottom of the meme's
                    landing page. If you provided any additional "Alternate Socials"
                    when filling out the Suggestion form, we'll throw those in there, as well!
                </p><i class="fas fa-award"></i>
            </div>
            <div class="box gray">
                <h3>Our Thanks</h3>
                <p>Every time you make a new meme suggestion, you really help us out.
                    We really appreciate you taking the time to fill out our form!
                    Thank you, from the bottom of our hearts!</p><i class="far fa-kiss-wink-heart"></i>
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


    $(document).ready(function(){
        $('#navbar').css("background-color", "#12151a");
    });
    </script>
</body>
</html>
