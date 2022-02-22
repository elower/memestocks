<?php
    session_start();
    if(isset($_SESSION['user']) && !empty($_SESSION['user'])){
        $loggedIn = TRUE;
        $username = $_SESSION['user'];
    } else {
        $username = "";
        $loggedIn = FALSE;
    }

REQUIRE_ONCE "functions.php";
$where = "";
$sort = "";
//REQUIRE_ONCE "gtrends/initializeGTrends.php";
if(isset($_GET['sort']) && !empty($_GET['sort'])){ //sort is set
    $sortArray = json_decode(stripslashes($_GET['sort']),true);
    $sortType = $sortArray['type'];
    switch($sortType){
        case "costAsc":
            $sort = " ORDER BY currentValue";
            break;
        case "costDesc":
            $sort = " ORDER BY currentValue DESC";
            break;
        case "buyAsc":
            $sort = " ORDER BY currentBuyers";
            break;
        case "buyDesc":
            $sort = " ORDER BY currentBuyers DESC";
            break;
        case "nameAsc":
            $sort = " ORDER BY name";
            break;
        case "nameDesc":
            $sort = " ORDER BY name DESC";
            break;
        default:
            $sort = " ORDER BY name";
    }
} else {$sort = " ORDER BY name";}
if(isset($_GET['date']) && !empty($_GET['date'])){
    $dates = $_GET['date'];
}
else{
    $dates = "threemonth";
}
if(isset($_GET['categories']) && !empty($_GET['categories'])){ //sort is set
    $catArray = json_decode(stripslashes($_GET['categories']),true);
    $key = 0;
    foreach($catArray as $category){
        if($key == 0){$where .= " WHERE ";}
        $where .= "categories LIKE '%$category%'";
        if($key != (count($catArray)-1)){ // add OR if not first item
            $where .= " OR ";
        }
        $key++;
    }
}
try {
    $db = db_connect();
    $sql = "SELECT * FROM memes" . $where . $sort;
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $key = 0;
    foreach($result as $record){
        $name = $record['name'];
        $image = $record['image'];
        $finra = $record['FINRA'];
        $categories = $record['categories']; //csv
        $buyers = $record['currentBuyers'];
        $description = $record['description'];
        $categoriesArray = explode(",", $categories);
        $value = getMemeValue($finra);
        // href=\"meme?finra=$finra\"
        print("<a class=\"card\">
            <li>
                <img src='../assets/img/memeimgs/$image' class='meme-img'>
                <div class='meme-info'>
                    <p class='memelink link' id='$finra'>$finra</p>
                    <p class='memename'>$name</p>
                    <div class='memeval'><p>Current Value: ". number_format($value, 2) . "</p><img class='coin-img' src='assets/img/coin.png'></div>
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
                    <div id=\"chart_div_$key\"></div>
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
        fillChart("_".$key, $finra, $dates);
        $key++;
    }
    print("
    <script>
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

    </script>");
    ?>
    <script>
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
            "<li onclick=\"closePopup()\"><a>No</a></li>"+
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
                    "<li class='close' onclick=\"closePopup()\"><a>Okay</a></li>"+
                "</ul>"+
                "<a class='cd-popup-close img-replace'>Close</a>");
            } else {
                getTotalReturn(finra, userid).then(function(response){
                    total = response.val;
                    $('.cd-popup-container').html("<p>You are about to sell your shares of stock in  "+finra+" for a total of "+total+" MemeCoins. Continue?</p>"+
                    "<ul class='cd-buttons'>"+
                        "<li onclick=\"acceptSale('"+finra+"', '"+total+"')\"><a>Yes</a></li>"+
                        "<li onclick=\"closePopup()\"><a>No</a></li>"+
                    "</ul>"+
                    "<a class='cd-popup-close img-replace'>Close</a>");
                });
            }

        });

    });
    </script>
    <?php
}
catch (Exception $e) {
    $response = "SQL Error";
}
finally{
  $db = NULL;
}
?>
<script>
    //redirect
    $('.memelink').click(function(){
        var finra = $(this).attr('id');
        var url = "meme/"+finra;
        window.location = url;
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
    function closePopup(){
        $('.cd-popup').removeClass('is-visible');
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
                        "<li class='close' onclick=\"closePopup()\"><a>Close</a></li>"+
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
                        "<li class='close' onclick=\"closePopup()\"><a>Close</a></li>"+
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
