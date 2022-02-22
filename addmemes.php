<?php
    session_start();
    if(isset($_SESSION['user'])){
        header ("Location: /");
        exit ();
    }

    REQUIRE_ONCE "assets/scripts/functions.php";
    if(isset($_POST['memename'])){
        $memename = $_POST['memename'];
        $finra = $_POST['finra'];
        $description = $_POST['description'];
        $categories = $_POST['categories'];
        $image = $_POST['image'];
        try {
            $db = db_connect();
            $sql = "INSERT INTO memes
            VALUES (?, ?, ?, ?, 0, 0, ?, 0);";
            $values = [$finra, $memename, $description, $image, $categories];
            $stmt = $db->prepare($sql);
            $stmt->execute($values);
            $response = "Success.";
        }
        catch (Exception $e) {
            $response = "SQL Error <br> $finra <br> $memename <br> $description <br> $image";
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
  <title>Add a Meme | MEMESTOCKS - Invest in the Internet</title>
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

</head>
<body>
    <nav id="navbar">
        <a href="/"><img src="assets/img/logo.png"></a>
        <a href="/#about">What is this?</a>
        <a href="/#top">Top Investors</a>
        <a href="/#charts">Stock Charts</a>
        <a id="login" href="login">Log In</a>
        <a id="signup" href="signup">Sign Up</a>
    </nav>
    <div id="body">
        <div class="section" id="login-wrap">
            <div id="response"> <?php echo($response); ?></div>
            <h1>Add Meme</h1>
            <form id="login-form" method="post" action="">
                <input type="text" placeholder="Meme Name" name="memename" required>
                <input type="text" placeholder="FINRA" name="finra" required>
                <textarea name="description" placeholder="Description" required></textarea>
                <input type="text" placeholder="CSV Categories" name="categories" required>
                <input type="text" placeholder="Image" name="image" required>
                <input type="submit" value="Add Meme">
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
            <p><a href="mailto:contact@memestocks.io">contact@memestocks.io</a></p>
        </div>
        <div class="third border">
            <p>Copyright 2020 Memestocks</p>
            <p>No real currency is involved in any way with this project.</p>
        </div>
        <p>Web Design & Development by <a href="http://www.ghostdesigns.me" target="_blank">ghostdesigns.me</a></p>
    </footer>
</body>
</html>
