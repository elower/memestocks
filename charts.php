<?php
    REQUIRE_ONCE "assets/scripts/functions.php";
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Basic Page Needs
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <meta charset="utf-8">
  <title>Stock Charts | MEMESTOCKS - Invest in the Internet</title>
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

   <!-- Google Charts -->
   <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

   <!-- Smooth Scroll ––––––––––––––––––––––––––––––––––––––––––––––––––-->
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
	$(function() {
	  $('a[href*=\\#]:not([href=\\#])').click(function() {
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
    <header class="short">
        <?php printNav(); ?>
    </header>
    <div id="body">
        <a id="top"></a>
        <div id="charts-page">
            <svg class="round" width="100%" height="200" viewBox="0 0 500 80" preserveAspectRatio="none">
                <path d="M0,0 L0,40 Q250,80 500,40 L500,0 Z" fill="#12151a" />
            </svg>
            <div class="cd-popup" role="alert">
                <div class="cd-popup-container">
                </div>
            </div>
            <div class="section">
                <h1>Stock Charts</h1>
                <p class="info">Day Trading is not permitted on MemeStocks.</p><p class="info" id="tooltip" data-tooltip="Day Trading is buying a stock and selling it in the same day."><i class="far fa-question-circle"></i></p>
                <nav class="menu">
                <a class="menu__item menu__item--pink" data-background="e4a924">
                    <i class="fas fa-list"></i>
                </a>
                <a class="menu__item menu__item--red" data-background="c92142">
                    <i class="fas fa-calendar-week"></i>
                </a>
                <a class="menu__item menu__item--black" data-background="37b983">
                    <i class="fas fa-sort"></i>
                </a>
                <a class="menu__item menu__item--blue" data-background="9f32b8">
                    <i class="fas fa-sync-alt"></i>
                </a>

                <div id="menu-nav-arrows">
                    <span><i class="fas fa-sort-up" id="arrow-one"></i></span>
                    <span><i class="fas fa-sort-up" id="arrow-two"></i></span>
                    <span><i class="fas fa-sort-up" id="arrow-three"></i></span>
                    <span><i class="fas fa-sort-up" id="arrow-four"></i></span>
                </div>
                <div id="menu-nav-options"></div>
              </nav>

              <div id="stocklist-wrap">
                  <div class="list-container">
                      <div class="spinner-box">
                      <div class="configure-border-1">
                        <div class="configure-core"></div>
                      </div>
                      <div class="configure-border-2">
                        <div class="configure-core"></div>
                      </div>
                    </div>
                      <ul class="scrollable-list" id="list-cont">
                      </ul>
                  </div>
                  <div id="card-navigation">
                      <i class="fas fa-chevron-left scroll-button left has-hover" id="left-single"></i>
                      <i class="fas fa-chevron-right scroll-button right has-hover" id="right-single"></i>
                  </div>
              </div>
            </div>

            <svg class="rev-triangle" width="100%" height="200" viewBox="0 0 500 80" preserveAspectRatio="none">
                <path d="M0,0 L250,50 L500,0 Z" fill="#12151a" />
            </svg>
        </div>

        <div id="top-charts">
            <div class="section">
                <h1>Top Investors</h1>
                <table class="top-investors">
                    <tr>
                        <th class='top-left'>Username</th>
                        <th class="top-right right">Current Cash</th>
                    </tr>
                    <?php
                    try {
                        $db = db_connect();
                        $sql = "SELECT * FROM users ORDER BY currentCoin DESC LIMIT 10";
                        $stmt = $db->prepare($sql);
                        $stmt->execute();
                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        $key = 0;
                        foreach($result as $record){
                            if($key % 2 == 0)
                                $class = "dark";
                            else
                                $class = "light";
                            if($key == count($result)-1){
                                $bottom = "class='bottom-left'";
                                $bottomright = "bottom-right";
                            }
                            $name = $record['username'];
                            $cash = $record['currentCoin'];

                            print("<tr class='$class'>
                                <td $bottom><a href='portfolio/$name'>$name</a></td></a>
                                <td class='$bottomright right'><div>$cash<img src='assets/img/coin.png'></div></td>
                            </tr>
                            ");
                            $key++;
                        }
                    }
                    catch (Exception $e) {
                        $response = "Error Fetching Top Investors";
                    }
                    finally{
                      $db = NULL;
                    }
                    ?>
                </table>
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
</body>

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

  $('#navbar').css("background-color", "#12151a");
});




    var categories = {};
    var sort = {};
    var date = {};
    var sortString = '';
    var dateString = '';
    var catString = '';
    var allCategories = "<?php print(getCatList()); ?>";
    var allCatArray = allCategories.split(',');
    var catItems = "";
    var catActive = "";
    var dateActive = "";
    var sortActive = "";
    var clickable = false;
    $(document).ready(function(){
        $('#menu-nav-options').hide();
        fillStocks(null, null);
    });

    /* Stock Scroll Functions */
    $('#left-single').click(function() {
       if(curPos > 0)
           curPos -= width;
       container.animate( {
          scrollLeft: curPos
       });
    });

    $('#right-single').click(function() {
       if(curPos < width * (count - 2))
           curPos += width;
       container.animate( {
          scrollLeft: curPos
       });
    });

    const menuItems = document.querySelectorAll('.menu__item');
    let menuItemActive = document.querySelector('.menu__item--active');
    if(menuItemActive == null){menuItemActive = document.querySelector('.menu__item--pink');}

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

        if(menuItemActive.classList.contains('menu__item--pink')){
            $('#arrow-one').fadeIn();
            $('#arrow-two').hide();
            $('#arrow-three').hide();
            $('#arrow-four').hide();
            $('#menu-nav-options').css("border-color","#f6a3b2");
            $('#menu-nav-arrows > span > i').css("color","#f6a3b2");
            var innerHTML = "<h3>Categories</h3>";
            for(i = 0; i < allCatArray.length; i++){//add active class to those in active array
                innerHTML += "<a class='cat-item";
                for(j = 0; j < catActive.length; j++){
                    if(catActive[j].innerHTML == allCatArray[i]){
                        innerHTML += " active";
                    }
                }
                innerHTML += "'>"+allCatArray[i]+"</a>";
            }
            document.getElementById('menu-nav-options').innerHTML = innerHTML;

            var catItems = document.querySelectorAll('.cat-item');
            for (var i = 0; i < catItems.length; i++) {
                catItems[i].addEventListener('click', function(){//add category function
                if(clickable == true){
                    if(!this.classList.contains('active')) {
                        this.classList.add('active');
                    } else{
                        this.classList.remove('active');
                    }
                    //list of active categories
                    catActive = document.querySelectorAll('.active');
                    categories = {};
                    for(var i = 0; i < catActive.length; i++){
                        categories["category"+i] = catActive[i].innerHTML;
                    }
                    catString = JSON.stringify(categories);
                    catString = catString.replace(" ", "%20");
                    fillStocks('categories', catString);
                    clickable = false;
                }
              });
            }
        }
        if(menuItemActive.classList.contains('menu__item--red')){
            $('#arrow-one').hide();
            $('#arrow-two').fadeIn();
            $('#arrow-three').hide();
            $('#arrow-four').hide();
            $('#menu-nav-options').css("border-color","#6c3137");
            $('#menu-nav-arrows > span > i').css("color","#6c3137");
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
                    if(this.innerHTML != dateActive.innerHTML && clickable == true) {
                        dateActive.classList.remove('active');
                        this.classList.add('active');
                        dateActive = this;
                        clickable = false;
                    }
                });
            }
        }
        if(menuItemActive.classList.contains('menu__item--black')){
            $('#arrow-one').hide();
            $('#arrow-two').hide();
            $('#arrow-three').fadeIn();
            $('#arrow-four').hide();
            $('#menu-nav-options').css("border-color","#274156");
            $('#menu-nav-arrows > span > i').css("color","#274156");
            document.getElementById('menu-nav-options').innerHTML = "<h3>Sort By</h3>"+
            "<a class = 'sort-item' onclick=\"fillStocks('sort', 'costAsc')\">Value Ascending</a>"+
            "<a class = 'sort-item' onclick=\"fillStocks('sort', 'costDesc')\">Value Descending</a>"+
            "<a class = 'sort-item' onclick=\"fillStocks('sort', 'buyAsc')\">Buyers Ascending</a>"+
            "<a class = 'sort-item' onclick=\"fillStocks('sort', 'buyDesc')\">Buyers Descending</a>"+
            "<a class = 'sort-item' onclick=\"fillStocks('sort', 'nameAsc')\">Name Ascending</a>"+
            "<a class = 'sort-item' onclick=\"fillStocks('sort', 'nameDesc')\">Name Descending</a>";
            var sortItems = document.querySelectorAll('.sort-item');

            if(sortActive === ""){
                sortActive = sortItems[2];
            } else {
                for (var i = 0; i < sortItems.length; i++) {
                    if(sortItems[i].innerHTML == sortActive.innerHTML)
                        sortActive = sortItems[i];
                }
            }
            sortActive.classList.add('active');

            for (var i = 0; i < sortItems.length; i++) {
                    sortItems[i].addEventListener('click', function(){//add date function
                    if(this.innerHTML != sortActive.innerHTML && clickable == true) {
                        sortActive.classList.remove('active');
                        this.classList.add('active');
                        sortActive = this;
                        clickable = false;
                    }
                });
            }
        }
        if(menuItemActive.classList.contains('menu__item--blue')){
            $('#arrow-one').hide();
            $('#arrow-two').hide();
            $('#arrow-three').hide();
            $('#arrow-four').fadeIn();
            $('#menu-nav-options').css("border-color","#B5FFE1");
            $('#menu-nav-arrows > span > i').css("color","#B5FFE1");
            document.getElementById('menu-nav-options').innerHTML = "<h3>Refresh</h3>"+
            "<a onclick=\"fillStocks(null, null)\">Refresh Stock Data</a>";
        }
        $('#menu-nav-options').fadeIn();
    } else {
            menuItemActive.classList.remove('menu__item--active');
            if(menuItemActive.classList.contains('menu__item--pink')){
                $('#arrow-one').fadeOut();
            }
            if(menuItemActive.classList.contains('menu__item--red')){
                $('#arrow-two').fadeOut();
            }
            if(menuItemActive.classList.contains('menu__item--black')){
                $('#arrow-three').fadeOut();
            }
            if(menuItemActive.classList.contains('menu__item--blue')){
                $('#arrow-four').fadeOut();
            }

            $('#menu-nav-options').fadeOut();
        }
    }

    function fillStocks(addOption, value){
        $('#list-cont').css("visibility", "hidden");
        $('.spinner-box').show();
        if(addOption == null && value == null){ //on pageload
            $('#list-cont').load('assets/scripts/listAllMemes.php?sort='+sortString+'&categories='+catString+'&date='+dateString);
        } else if(clickable == true){
            if(addOption == 'sort'){
                sort.type = value;
                sortString = JSON.stringify(sort);
            }
            if(addOption == 'date'){
                dateString = value.replace(' ', '%20');
            }
            $('#list-cont').load('assets/scripts/listAllMemes.php?sort='+sortString+'&categories='+catString+'&date='+dateString);
        }
    }
    var observer = new MutationObserver(function (mutationRecords) {
        $('#list-cont').css("visibility", "visible");
        $('.spinner-box').hide();
        clickable = true;
    });
    observer.observe(document.getElementById('list-cont'), { childList: true });
</script>
</html>
