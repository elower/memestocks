<?php
    REQUIRE_ONCE "assets/scripts/functions.php";
    session_start();

    if(isset($_SESSION['user'])){
        $loggedIn = TRUE;
        if(!isset($_GET['user'])){
            $yourPortfolio = TRUE;
        } else {
            if($_GET['user'] == $_SESSION['user']){
                $yourPortfolio = TRUE;
            } else {
                $yourPortfolio = FALSE;
            }
        }
    } else {
        $loggedIn = FALSE;
        if(!isset($_GET['user'])){
            header('Location: login');
            exit();
        } else {
            $yourPortfolio = FALSE;
        }
    }
    if($yourPortfolio) $userID = getUserIDFromUsername($_SESSION['user']);
    else $userID = getUserIDFromUsername($_GET['user']);

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <script data-ad-client="ca-pub-3977667266956672" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
  <!-- Basic Page Needs
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <meta charset="utf-8">
  <title>Portfolio | MEMESTOCKS - Invest in the Internet</title>
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
            <h1><?php
                if($yourPortfolio) echo "Your Portfolio";
                else print($_GET['user'] . "s Portfolio");
            ?></h1>
            <p>MemeStocks - Invest in the internet.</p>
            <p id="disclaimer">*No real-life currency is involved in memestocks</p>

            <div id="top-performing">
                <p>Top Performing Memes of the Day</p>
                <div id="top-performing-listing">
                    <div class="marquee">
                      <ul class="marquee-content">
                        <?php
                        $topPerforming = getTopPerforming();
                        foreach($topPerforming as $each){
                            $finra = $each['finra'];
                            $value = $each['currentValue'];
                            print("<li><h3>$finra</h3><p>$value MemeCoins</p></li>");
                        }
                        ?>
                      </ul>
                    </div>
                </div>
            </div>
            </div>
        </section>
    </header>
    <svg class="round" width="100%" height="200" viewBox="0 0 500 80" preserveAspectRatio="none">
        <path d="M0,0 L0,40 Q250,80 500,40 L500,0 Z" fill="#12151a" />
    </svg>
    <div id="body">
        <div class="section" id="portfolio">
            <div class="cd-popup" role="alert">
                <div class="cd-popup-container">
                </div>
            </div>
            <script>



            </script>
            <?php
            $db = db_connect();
            $sql = "SELECT * FROM usertransactions WHERE userid = ?";
            $values = [$userID];
            $stmt = $db->prepare($sql);
            $stmt->execute($values);
            $allInvestments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $db = NULL;

            $valueArray = [];
            foreach($allInvestments as $investment){
                $change = ((getMemeValue($investment['FINRA']) - $investment['value'])/$investment['value'])*100;
                $pusharr = array(
                    "finra" => $investment['FINRA'],
                    "change" => number_format($change)
                );
                array_push($valueArray, $pusharr);
            }

            usort($valueArray, function($a, $b){
                return $a['change'] <=> $b['change'];
            });

            $worst = array_slice($valueArray, 0, 5, true);
            $valueArray = array_reverse($valueArray, true);
            $best = array_slice($valueArray, 0, 5, true);

            usort($valueArray, function($a, $b){
                return $a['finra'] <=> $b['finra'];
            });
            $all = $valueArray;

            ?>
            <h1>Best-Performing Investments</h1>
            <div id="stocklist-wrap">
                <div class="list-container" id="list-cont-top">
                    <ul class="scrollable-list" id="list-cont">
            <?php
            $key = 0;
            foreach($best as $meme){
                $memeArray = getMemeInfo($meme['finra']);
                $name = $memeArray['name'];
                $image = $memeArray['image'];
                $finra = $memeArray['FINRA'];
                $categories = $memeArray['categories']; //csv
                $buyers = $memeArray['currentBuyers'];
                $description = $memeArray['description'];
                $categoriesArray = explode(",", $categories);
                $value = getMemeValue($finra);
                print("<a class=\"card\">
                    <li class='top'>
                        <img src='../assets/img/memeimgs/$image' class='meme-img'>
                        <div class='meme-info'>
                            <p class='memelink link' id='$finra'>$finra</p>
                            <p class='memename'>$name</p>
                            <div class='memeval'><p>Current Value: ". number_format($value, 2) . "</p><img class='coin-img' src='/assets/img/coin.png'></div>
                        </div>
                        <div class='valuechange'>
                            <p>Change in Value Since Purchase: ");
                        if($value >= 0) print "<span class='pos'>+"; else print "<span class='neg'>-";
                        print($meme['change'] . "%</span></p>
                        </div>
                        <div class='buy-wrap'>");
                        if($loggedIn){
                            if(userHasStock(getUserIDFromUsername($_SESSION['user']), $finra)){
                                $buyPrice = getUserBuyPrice(getUserIDFromUsername($_SESSION['user']), $finra);
                                $amount = getUserBuyAmount(getUserIDFromUsername($_SESSION['user']), $finra);
                                if($amount == 1) $shares = "share"; else $shares = "shares";
                                print("
                                <div class='bought'>
                                    <p>You bought $amount $shares of this stock for $buyPrice MemeCoins per share.</p>
                                    <p>It is now worth $value MemeCoins per share.</p>
                                </div>
                                <button class='sell' id='sell-stock-top-$finra'>SELL STOCK</button>
                                ");
                            } else {
                                print("
                                    <div class=\"quantity\">
                                      <input type=\"number\" readonly min=\"1\" step=\"1\" value=\"1\" id='amount-top-$finra'>
                                    </div>
                                    <input type='text' id='cost-top-$finra' value='$value'>
                                    <button class='buy' id='buy-stock-top-$finra'>BUY STOCK</button>
                                ");
                            }
                        }
                        print("</div>
                        <div class='chart'>
                            <div id=\"chart_div_top_$key\"></div>
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

                //fill chart
                fillChart("_top_".$key, $finra, "threemonth");
                $key++;
            }
            ?>
                    </ul>
                </div>
                <div id="card-navigation">
                    <i class="fas fa-chevron-left scroll-button left has-hover" id="left-single-top"></i>
                    <i class="fas fa-chevron-right scroll-button right has-hover" id="right-single-top"></i>
                </div>
                <script>
                    var containerTop = $('#list-cont-top'),
                    countTop = $('.top').length,
                    widthTop = 500,
                    curPosTop = 0;
                    if($(window).width() <= 1000){
                        widthTop = $('.card').width();
                    }

                </script>
            </div> <!-- end best performing -->

            <h1>Worst-Performing Investments</h1>
            <div id="stocklist-wrap">
                <div class="list-container" id="list-cont-bottom">
                    <ul class="scrollable-list" id="list-cont">
            <?php
            $key = 0;
            foreach($worst as $meme){
                $memeArray = getMemeInfo($meme['finra']);
                $name = $memeArray['name'];
                $image = $memeArray['image'];
                $finra = $memeArray['FINRA'];
                $categories = $memeArray['categories']; //csv
                $buyers = $memeArray['currentBuyers'];
                $description = $memeArray['description'];
                $categoriesArray = explode(",", $categories);
                $value = getMemeValue($finra);
                print("<a class=\"card\">
                    <li class='bottom'>
                        <img src='../assets/img/memeimgs/$image' class='meme-img'>
                        <div class='meme-info'>
                            <p class='memelink link' id='$finra'>$finra</p>
                            <p class='memename'>$name</p>
                            <div class='memeval'><p>Current Value: ". number_format($value, 2) . "</p><img class='coin-img' src='/assets/img/coin.png'></div>
                        </div>
                        <div class='valuechange'>
                            <p>Change in Value Since Purchase: ");
                        if($value >= 0) print "<span class='pos'>+"; else print "<span class='neg'>-";
                        print($meme['change'] . "%</span></p>
                        </div>
                        <div class='buy-wrap'>");
                        if($loggedIn){
                            if(userHasStock(getUserIDFromUsername($_SESSION['user']), $finra)){
                                $buyPrice = getUserBuyPrice(getUserIDFromUsername($_SESSION['user']), $finra);
                                $amount = getUserBuyAmount(getUserIDFromUsername($_SESSION['user']), $finra);
                                if($amount == 1) $shares = "share"; else $shares = "shares";
                                print("
                                <div class='bought'>
                                    <p>You bought $amount $shares of this stock for $buyPrice MemeCoins per share.</p>
                                    <p>It is now worth $value MemeCoins per share.</p>
                                </div>
                                <button class='sell' id='sell-stock-bottom-$finra'>SELL STOCK</button>
                                ");
                            } else {
                                print("
                                    <div class=\"quantity\">
                                      <input type=\"number\" readonly min=\"1\" step=\"1\" value=\"1\" id='amount-bottom-$finra'>
                                    </div>
                                    <input type='text' id='cost-bottom-$finra' value='$value'>
                                    <button class='buy' id='buy-stock-bottom-$finra'>BUY STOCK</button>
                                ");
                            }
                        }
                        print("</div>
                        <div class='chart'>
                            <div id=\"chart_div_bottom_$key\"></div>
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

                //fill chart
                fillChart("_bottom_".$key, $finra, "threemonth");
                $key++;
            }
            ?>
                    </ul>
                </div>
                <div id="card-navigation">
                    <i class="fas fa-chevron-left scroll-button left has-hover" id="left-single-bottom"></i>
                    <i class="fas fa-chevron-right scroll-button right has-hover" id="right-single-bottom"></i>
                </div>
                <script>
                    var containerBottom = $('#list-cont-bottom'),
                    countBottom = $('.bottom').length,
                    widthBottom = 500,
                    curPosBottom = 0;
                    if($(window).width() <= 1000){
                        widthBottom = $('.card').width();
                    }

                </script>
            </div> <!-- end worst performing -->

            <h1>All Investments</h1>
            <div id="stocklist-wrap">
                <div class="list-container" id="list-cont-all">
                    <ul class="scrollable-list" id="list-cont">
            <?php
            $key = 0;
            foreach($all as $meme){
                $memeArray = getMemeInfo($meme['finra']);
                $name = $memeArray['name'];
                $image = $memeArray['image'];
                $finra = $memeArray['FINRA'];
                $categories = $memeArray['categories']; //csv
                $buyers = $memeArray['currentBuyers'];
                $description = $memeArray['description'];
                $categoriesArray = explode(",", $categories);
                $value = getMemeValue($finra);
                print("<a class=\"card\">
                    <li class='all'>
                        <img src='../assets/img/memeimgs/$image' class='meme-img'>
                        <div class='meme-info'>
                            <p class='memelink link' id='$finra'>$finra</p>
                            <p class='memename'>$name</p>
                            <div class='memeval'><p>Current Value: ". number_format($value, 2) . "</p><img class='coin-img' src='/assets/img/coin.png'></div>
                        </div>
                        <div class='valuechange'>
                            <p>Change in Value Since Purchase: ");
                        if($value >= 0) print "<span class='pos'>+"; else print "<span class='neg'>-";
                        print($meme['change'] . "%</span></p>
                        </div>
                        <div class='buy-wrap'>");
                        if($loggedIn){
                            if(userHasStock(getUserIDFromUsername($_SESSION['user']), $finra)){
                                $buyPrice = getUserBuyPrice(getUserIDFromUsername($_SESSION['user']), $finra);
                                $amount = getUserBuyAmount(getUserIDFromUsername($_SESSION['user']), $finra);
                                if($amount == 1) $shares = "share"; else $shares = "shares";
                                print("
                                <div class='bought'>
                                    <p>You bought $amount $shares of this stock for $buyPrice MemeCoins per share.</p>
                                    <p>It is now worth $value MemeCoins per share.</p>
                                </div>
                                <button class='sell' id='sell-stock-all-$finra'>SELL STOCK</button>
                                ");
                            } else {
                                print("
                                    <div class=\"quantity\">
                                      <input type=\"number\" readonly min=\"1\" step=\"1\" value=\"1\" id='amount-all-$finra'>
                                    </div>
                                    <input type='text' id='cost-all-$finra' value='$value'>
                                    <button class='buy' id='buy-stock-all-$finra'>BUY STOCK</button>
                                ");
                            }
                        }
                        print("</div>
                        <div class='chart'>
                            <div id=\"chart_div_all_$key\"></div>
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

                //fill chart
                fillChart("_all_".$key, $finra, "threemonth");
                $key++;
            }
            ?>
                    </ul>
                </div>
                <div id="card-navigation">
                    <i class="fas fa-chevron-left scroll-button left has-hover" id="left-single-all"></i>
                    <i class="fas fa-chevron-right scroll-button right has-hover" id="right-single-all"></i>
                </div>
                <script>
                    var containerAll = $('#list-cont-all'),
                    countAll = $('.all').length,
                    widthAll = 500,
                    curPosAll = 0;
                    if($(window).width() <= 1000){
                        widthAll = $('.card').width();
                    }

                </script>
            </div> <!-- end all -->
        </div>
    </div>
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
    $('.cd-popup').addClass('is-visible');
    if($(this).attr('id').search("top") !== -1){
        var section = 'top';
    }
    if($(this).attr('id').search("bottom") !== -1){
        var section = 'bottom';
    }
    if($(this).attr('id').search("all") !== -1){
        var section = 'all';
    }
    var finra = $(this).attr('id').replace('buy-stock-'+section+'-', '');

    var count = $('#amount-'+section+'-'+finra).val();
    var total = $('#cost-'+section+'-'+finra).val();

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
    $('.cd-popup').addClass('is-visible');
    if($(this).attr('id').search("top") !== -1){
        var section = 'top';
    }
    if($(this).attr('id').search("bottom") !== -1){
        var section = 'bottom';
    }
    if($(this).attr('id').search("all") !== -1){
        var section = 'all';
    }
    var finra = $(this).attr('id').replace('sell-stock-'+section+'-', '');
    var userid = "<?php echo(getUserIDFromUsername($_SESSION['user'])); ?>";
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

/*Top Performing Marquee*/
const root = document.documentElement;
const marqueeElementsDisplayed = getComputedStyle(root).getPropertyValue("--marquee-elements-displayed");
const marqueeContent = document.querySelector("ul.marquee-content");

root.style.setProperty("--marquee-elements", marqueeContent.children.length);

for(let i=0; i<marqueeElementsDisplayed; i++) {
  marqueeContent.appendChild(marqueeContent.children[i].cloneNode(true));
}

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


/* Stock Scroll Functions */
$('#left-single-top').click(function() {
   if(curPosTop > 0)
       curPosTop -= widthTop;
   containerTop.animate( {
      scrollLeft: curPosTop
   });
});

$('#right-single-top').click(function() {
   if(curPosTop < widthTop * (countTop - 2))
       curPosTop += widthTop;
   containerTop.animate( {
      scrollLeft: curPosTop
   });
});

$('#left-single-bottom').click(function() {
   if(curPosBottom > 0)
       curPosBottom -= widthBottom;
   containerBottom.animate( {
      scrollLeft: curPosBottom
   });
});

$('#right-single-bottom').click(function() {
   if(curPosBottom < widthBottom * (countBottom - 2))
       curPosBottom += widthBottom;
   containerBottom.animate( {
      scrollLeft: curPosBottom
   });
});

$('#left-single-all').click(function() {
   if(curPosAll > 0)
       curPosAll -= widthAll;
   containerAll.animate( {
      scrollLeft: curPosAll
   });
});

$('#right-single-all').click(function() {
   if(curPosAll < widthAll * (countAll - 2))
       curPosAll += widthAll;
   containerAll.animate( {
      scrollLeft: curPosAll
   });
});


jQuery('<div class="quantity-nav"><div class="quantity-button quantity-up"><i class="fas fa-caret-up"></i></div><div class="quantity-button quantity-down"><i class="fas fa-caret-down"></i></div></div>').insertAfter('.quantity input');
jQuery('.quantity').each(function() {
  var spinner = jQuery(this),
    input = spinner.find('input[type="number"]'),
    btnUp = spinner.find('.quantity-up'),
    btnDown = spinner.find('.quantity-down'),
    min = input.attr('min');
    var key = input.attr('id').replace('amount-', '');
    if(key.search("top-") !== -1){
        var section = 'top';
        key = key.replace('top-', '');
    }
    if(key.search("bottom-") !== -1){
        var section = 'bottom';
        key = key.replace('bottom-', '');
    }
    if(key.search("all-") !== -1){
        var section = 'all';
        key = key.replace('all-', '');
    }
    //get max value
    getMultiplier(key).then(function(response){
        var value = response.val;
        var currentCoin = "<?php echo getCurrentCoins($_SESSION['user']); ?>";
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

            $('#cost-'+section+'-'+key).val(newTotalVal.toFixed(2));
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
              $('#cost-'+section+'-'+key).val(newTotalVal.toFixed(2));
              spinner.find("input").val(newVal);
              spinner.find("input").trigger("change");
          });
      });
    });
});

//redirect
$('.memelink').click(function(){
    var finra = $(this).attr('id');
    var url = "meme/"+finra;
    window.location = url;
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
    var username = "<?php echo($_SESSION['user']); ?>";
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
    var username = "<?php echo($_SESSION['user']); ?>";
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
            } else console.log(response.err);
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
