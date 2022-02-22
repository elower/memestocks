<?php
    REQUIRE_ONCE "assets/scripts/functions.php";
    session_start();

    if(!isset($_GET['finra']) || empty($_GET['finra'])){
        header('Location: /');
        exit();
    }
    if(isset($_SESSION['user']) && !empty($_SESSION['user'])){
        $loggedIn = TRUE;
        $username = $_SESSION['user'];
    }
    else
        $loggedIn = FALSE;
    $finra = $_GET['finra'];
    $memeInfo = getMemeInfo($finra);
    $name = $memeInfo['name'];
    $image = $memeInfo['image'];
    $categories = $memeInfo['categories']; //csv
    $buyers = $memeInfo['currentBuyers'];
    $description = $memeInfo['description'];
    $categoriesArray = explode(",", $categories);
    $value = getMemeValue($finra);
?>
<!DOCTYPE html>
<html lang="en">
<head>

    <script data-ad-client="ca-pub-3977667266956672" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
  <!-- Basic Page Needs
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <meta charset="utf-8">
  <title><?php echo($name); ?> | MEMESTOCKS - Invest in the Internet</title>
  <meta name="description" content="">
  <meta name="author" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">


  <!-- CSS
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
    <link rel="stylesheet" href="/assets/reset.css">
    <link rel="stylesheet" href="/assets/animate.css">
    <link rel="stylesheet" href="/assets/style.css">


  <!-- Favicon–––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <link rel="icon" type="image/png" href="/assets/img/favicon.ico">

  <!-- Font Awesome -->
   <script src="https://kit.fontawesome.com/a062562745.js" crossorigin="anonymous"></script>

   <!-- Google Charts -->
   <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
    <header>
        <?php printNav(); ?>

        <section>
            <?php
            if(!memeExists($finra)){
                echo("<h1>Meme does not exist.</h1>");
                echo("<p>MemeStocks - Invest in the internet.</p>");
                echo("</section></header>");
            } else {
            ?>
            <h1><?php echo($name); ?></h1>
            <p>MemeStocks - Invest in the internet.</p>
            <div id="top-performing">
                <p>Top Performing Memes of the Day</p>
                <div id="top-performing-listing">
                    <div class="marquee">
                      <ul class="marquee-content">
                        <?php
                        $topPerforming = getTopPerforming();
                        foreach($topPerforming as $each){
                            $topfinra = $each['finra'];
                            $topvalue = $each['currentValue'];
                            print("<li><h3>$topfinra</h3><p>$topvalue MemeCoins</p></li>");
                        }
                        ?>
                      </ul>
                    </div>
                </div>
            </div>
        </section>
    </header>
    <svg class="round" width="100%" height="200" viewBox="0 0 500 80" preserveAspectRatio="none">
        <path d="M0,0 L0,40 Q250,80 500,40 L500,0 Z" fill="#12151a" />
    </svg>
    <div id="body" class="single">
        <div class="section" id="full">
            <div class="cd-popup" role="alert">
                <div class="cd-popup-container">
                </div>
            </div>
            <h1><?php echo($name); ?></h1>
            <p class="info">Day Trading is not permitted on MemeStocks.</p><p class="info" id="tooltip" data-tooltip="Day Trading is buying a stock and selling it in the same day."><i class="far fa-question-circle"></i></p>
            <nav class="menu">
            <a class="menu__item menu__item--darkpurple" data-background="c92142">
                <i class="fas fa-calendar-week"></i>
            </a>
            <div id="menu-nav-arrows">
                <span id="arrow-full"><i class="fas fa-sort-up"></i></span>
            </div>
            <div id="menu-nav-options"></div>
            </nav>
            <div id="full-wrap">
                <?php
                print("<a class=\"card\">
                    <li>
                        <img src='/assets/img/memeimgs/$image' class='meme-img'>
                        <div class='meme-info'>
                            <p class='memelink link' id='$finra'>$finra</p>
                            <p class='memename'>$name</p>
                            <div class='memeval'><p>Current Value: ". number_format($value, 2) . "</p><img class='coin-img' src='/assets/img/coin.png'></div>
                        </div>
                        <div class='memedesc'>
                            <p>$description</p>
                        </div>
                        <div class='buy-wrap'>");
                        if($loggedIn){
                            if(userHasStock(getUserIDFromUsername($username), $finra)){
                                $buyPrice = getUserBuyPrice(getUserIDFromUsername($username), $finra);
                                $amount = getUserBuyAmount(getUserIDFromUsername($username), $finra);
                                if($amount == 1) $shares = "share"; else $shares = "shares";
                                print("
                                <div class='bought'>
                                    <p>You bought $amount $shares of this stock for $buyPrice MemeCoins per share.</p>
                                    <p>It is now worth $value MemeCoins per share.</p>
                                </div>
                                <button class='sell' id='sell-stock-$finra'>SELL STOCK</button>
                                ");
                            } else {
                                print("
                                    <div class=\"quantity\">
                                      <input type=\"number\" readonly min=\"1\" step=\"1\" value=\"1\" id='amount-$finra'>
                                    </div>
                                    <input type='text' id='cost-$finra' value='$value'>
                                    <button class='buy' id='buy-stock-$finra'>BUY STOCK</button>
                                ");
                            }
                        }
                        print("</div>
                        <div class='chart'>
                            <div id=\"chart_div\"></div>
                        </div>
                        <div class='extra-info'>
                            <section class='categories'>
                                <h3>Categories</h3>");
                        foreach($categoriesArray as $item){
                            print("<p>$item</p>");
                        }
                        print("
                            </section>
                            <section class='buyers'>
                                <h3>Buyers</h3>
                                <p id='current-buyers'><i class='fas fa-user'></i>$buyers</p>
                            </section>
                        </div>
                    </li>
                </a>");
                ?>
        </div><!-- full wrap-->
      </div><!-- full -->
    </div>
    <?php }?>
    <footer>
        <div class="third">
            <img src="/assets/img/logo.png">
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
</body>

<script>
var dateActive = "";
var dateString = "";
/*Top Performing Marquee*/
const root = document.documentElement;
const marqueeElementsDisplayed = getComputedStyle(root).getPropertyValue("--marquee-elements-displayed");
const marqueeContent = document.querySelector("ul.marquee-content");

root.style.setProperty("--marquee-elements", marqueeContent.children.length);

for(let i=0; i<marqueeElementsDisplayed; i++) {
  marqueeContent.appendChild(marqueeContent.children[i].cloneNode(true));
}

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
        $('#menu-nav-options').hide();
        $('#arrow-full > i').hide();
      $('.underline--path').attr('d', d_normal.join(' '));

      $('.gd').hover(function() {
        $('.underline--animation').attr({from: d_normal.join(' '), to: d_animated.join(' ')})
      	$('.underline--animation')[0].beginElement();
      }, function() {
        $('.underline--animation').attr({to: d_normal.join(' '), from: d_animated.join(' ')})
      	$('.underline--animation')[0].beginElement();
      });

      fillStocks(null, null);
    });
    $(window).scroll(function() {
        var hT = $('#top-performing').offset().top,
           hH = $('#top-performing').outerHeight(),
           wH = $(window).height(),
           wS = $(this).scrollTop();
        if (wS > (hT+hH-wH)){
            $('#navbar').css("background-color", "#12151a");
        } else {
            $('#navbar').css("background-color", "transparent");
        }
        if(wS == 0){$('#navbar').css("background-color", "transparent");}
    });


    const menuItems = document.querySelectorAll('.menu__item');
    let menuItemActive = document.querySelector('.menu__item--active');
    if(menuItemActive == null){menuItemActive = document.querySelector('.menu__item--darkpurple');}

    for (var i = 0; i < menuItems.length; i++) {
      menuItems[i].addEventListener('click', buttonClick);
    }

    function buttonClick() {
      if (!this.classList.contains('menu__item--active')) {
        menuItemActive.classList.remove('menu__item--active');
        this.classList.add('menu__item--active');

        menuItemActive.classList.add('menu__item--animate');
        this.classList.add('menu__item--animate');

        menuItemActive = this;
        $('#arrow').fadeIn();
        $('#menu-nav-options').fadeIn();
        $('#arrow-full > i').fadeIn();

        document.getElementById('menu-nav-options').innerHTML = "<h3>Display Chart Data From:</h3>"+
        "<a class='date-item' onclick=\"fillStocks('date', 'week')\">Past Week</a>"+
        "<a class='date-item' onclick=\"fillStocks('date', 'month')\">Past Month</a>"+
        "<a class='date-item' onclick=\"fillStocks('date', 'threemonth')\">Past 3 Months</a>"+
        "<a class='date-item' onclick=\"fillStocks('date', 'year')\">Past Year</a>"+
        "<a class='date-item' onclick=\"fillStocks('date', 'fiveyear')\">Past Five Years</a>";
        var dateItems = document.querySelectorAll('.date-item');

        if(dateActive === ""){
            dateActive = dateItems[2];
        } else {
            for (var i = 0; i < dateItems.length; i++) {
                if(dateItems[i].innerHTML == dateActive.innerHTML)
                    dateActive = dateItems[i];
            }
        }
        dateActive.classList.add('active');

        for (var i = 0; i < dateItems.length; i++) {
                dateItems[i].addEventListener('click', function(){//add date function
                if(this.innerHTML != dateActive.innerHTML) {
                    dateActive.classList.remove('active');
                    this.classList.add('active');
                    dateActive = this;
                }
            });
        }
    } else {
            menuItemActive.classList.remove('menu__item--active');
            $('#menu-nav-options').fadeOut();
            $('#arrow-full > i').fadeOut();
        }
    }

    function fillStocks(addOption, value){
        var finra = "<?php echo($finra); ?>";

        if(value != null)
            dateString = value.replace(' ', '%20');
        $('#chart_div').load('/assets/scripts/singleChart.php?finra='+finra+'&date='+dateString);
    }



    var container = $('.list-container'),
    count = $('ul a li').length,
    width = 500,
    curPos = 0;
    if($(window).width() <= 1000){
        width = $('.card').width();
    }
    container.animate( {
       scrollLeft: curPos
    });


    jQuery('<div class="quantity-nav"><div class="quantity-button quantity-up"><i class="fas fa-caret-up"></i></div><div class="quantity-button quantity-down"><i class="fas fa-caret-down"></i></div></div>').insertAfter('.quantity input');
    jQuery('.quantity').each(function() {
      var spinner = jQuery(this),
        input = spinner.find('input[type="number"]'),
        btnUp = spinner.find('.quantity-up'),
        btnDown = spinner.find('.quantity-down'),
        min = input.attr('min');
        var key = input.attr('id').replace('amount-', '');
        //get max value
        getMultiplier(key).then(function(response){
            var value = response.val;
            var currentCoin = "<?php echo getCurrentCoins($username); ?>";
            var max = currentCoin/value;
            input.attr('max', max);
            var max = input.attr('max');

            btnUp.click(function() {

            var multiplier = 0;
            getMultiplier(key).then(function(response){
                multiplier = response.val;
                var oldValue = parseFloat(input.val());
                if (oldValue >= max) {
                  var newVal = oldValue;
                } else {
                  var newVal = oldValue + 1;
                }

                var newTotalVal = newVal*multiplier;
                $('#cost-'+key).val(newTotalVal.toFixed(2));
                spinner.find("input").val(newVal);
                spinner.find("input").trigger("change");
            });
          });

          btnDown.click(function() {
              var multiplier = 0;
              getMultiplier(key).then(function(response){
                  multiplier = response.val;
                  var oldValue = parseFloat(input.val());
                  if (oldValue <= min) {
                    var newVal = oldValue;
                  } else {
                    var newVal = oldValue - 1;
                  }
                  var newTotalVal = newVal*multiplier;
                  $('#cost-'+key).val(newTotalVal.toFixed(2));
                  spinner.find("input").val(newVal);
                  spinner.find("input").trigger("change");
              });
          });
        });
    });

    function getMultiplier(finra){
        return $.ajax({
            url: '/assets/scripts/getMemeValue.php',
            type: 'post',
            dataType: 'JSON',
            data: { "finra": finra}
        });
    }
    function getTotalReturn(finra, userid){
        return $.ajax({
            url: '/assets/scripts/getTotalReturn.php',
            type: 'post',
            dataType: 'JSON',
            data: { "finra": finra, "user": userid}
        });
    }
    function getPurchaseDate(finra, userid){
        return $.ajax({
            url: '/assets/scripts/getPurchaseDate.php',
            type: 'post',
            dataType: 'JSON',
            data: { "finra": finra, "user": userid}
        });
    }

    $('.buy').click(function(){
        var finra = $(this).attr('id').replace('buy-stock-', '');
        var count = $('#amount-'+finra).val();
        var total = $('#cost-'+finra).val();
        if(count == 1) var shares = "share";
        else var shares = "shares";
        $('.cd-popup-container').html("<p>You are about to purchase "+count+" "+shares+" of "+finra+" for a total of "+total+" MemeCoins. Continue?</p>"+
        "<ul class='cd-buttons'>"+
            "<li onclick=\"acceptPurchase('"+finra+"', '"+count+"')\"><a>Yes</a></li>"+
            "<li onclick=\"closePopup(false)\"><a>No</a></li>"+
        "</ul>"+
        "<a class='cd-popup-close img-replace'>Close</a>");
    });

    $('.sell').click(function(){
        var finra = $(this).attr('id').replace('sell-stock-', '');
        var userid = "<?php echo(getUserIDFromUsername($username)); ?>";
        getPurchaseDate(finra, userid).then(function(response){
            var purchaseDate = response.val;
            var currentDate = new Date();
            var month = currentDate.getMonth() + 1;
            var day = currentDate.getDate();
            var year = currentDate.getFullYear();

            currentDate = year + "-" + month + "-" + day;
            if(purchaseDate == currentDate){
                $('.cd-popup-container').html("<p>Day Trading (selling stock the same day you purchased it) is not allowed on MemeStocks. Please wait until tomorrow to sell your stock!</p>"+
                "<ul class='cd-buttons'>"+
                    "<li class='close' onclick=\"closePopup(false)\"><a>Okay</a></li>"+
                "</ul>"+
                "<a class='cd-popup-close img-replace'>Close</a>");
            } else {
                getTotalReturn(finra, userid).then(function(response){
                    total = response.val;
                    $('.cd-popup-container').html("<p>You are about to sell your shares of stock in  "+finra+" for a total of "+total+" MemeCoins. Continue?</p>"+
                    "<ul class='cd-buttons'>"+
                        "<li onclick=\"acceptSale('"+finra+"', '"+total+"')\"><a>Yes</a></li>"+
                        "<li onclick=\"closePopup(false)\"><a>No</a></li>"+
                    "</ul>"+
                    "<a class='cd-popup-close img-replace'>Close</a>");
                });
            }

        });

    });

    //open popup
    $('.buy').on('click', function(event){
        event.preventDefault();
        $('.cd-popup').addClass('is-visible');
    });
    //open popup
    $('.sell').on('click', function(event){
        event.preventDefault();
        $('.cd-popup').addClass('is-visible');
    });

    //close popup
    $('.cd-popup').on('click', function(event){
        if( $(event.target).is('.cd-popup-close') || $(event.target).is('.cd-popup') ) {
            event.preventDefault();
            $(this).removeClass('is-visible');
        }
    });
    function closePopup(refresh){
        $('.cd-popup').removeClass('is-visible');
        if(refresh == true)
            location.reload();
    }
    function acceptPurchase(finra, amount){
        var username = "<?php echo($username); ?>";
        $.ajax({
            url: '/assets/scripts/purchaseStock.php',
            type: 'post',
            dataType: 'JSON',
            data: { "finra": finra, "username": username, "amount": amount},
            success: function(response) {
                if(response.err == ""){
                    $('#current-coin').html(response.coins);
                    container.animate( {
                       scrollLeft: 0
                    });
                    fillStocks(null, null);

                    $('.cd-popup-container').html("<p>Sucess! Congrats on your new stock!</p>"+
                    "<ul class='cd-buttons'>"+
                        "<li class='close' onclick=\"closePopup(true)\"><a>Close</a></li>"+
                    "</ul>"+
                    "<a class='cd-popup-close img-replace'>Close</a>");
                } else{
                    console.log(response.err);
                }
            }
        });
    }
    function acceptSale(finra, amount){
        var username = "<?php echo($username); ?>";
        $.ajax({
            url: '/assets/scripts/sellStock.php',
            type: 'post',
            dataType: 'JSON',
            data: { "finra": finra, "username": username, "amount": amount},
            success: function(response) {
                if(response.err == ""){
                    $('#current-coin').html(response.coins);
                    container.animate( {
                       scrollLeft: 0
                    });
                    fillStocks(null, null);
                    $('.cd-popup-container').html("<p>Sucess! Congrats on your sale!</p>"+
                    "<ul class='cd-buttons'>"+
                        "<li class='close' onclick=\"closePopup(true)\"><a>Close</a></li>"+
                    "</ul>"+
                    "<a class='cd-popup-close img-replace'>Close</a>");
                }
            }
        });
    }
    //close popup when clicking the esc keyboard button
    $(document).keyup(function(event){
        if(event.which=='27'){
            $('.cd-popup').removeClass('is-visible');
        }
    });
</script>
</html>
