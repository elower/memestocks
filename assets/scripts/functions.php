<?php
REQUIRE_ONCE "dbconnect.php";
date_default_timezone_set('EST');

function getTrendArray($finra, $dates){
    /*
    dates are:

    week
    month
    threemonth
    year
    fiveyear
    */
    try{
        $db = db_connect();
        $sql = "SELECT data
                FROM memeData";
           $stmt = $db->prepare($sql);
           $stmt->execute();
           $result = $stmt->fetchColumn();
           $result = json_decode($result);
           $result = $result->{"$finra"}->{"$dates"};
           $result = json_decode(json_encode($result), true);
           return $result;
    } catch (Exception $e) {
        return [];
    }
    finally{
      $db = NULL;
    }
}
function getUserID($email){
  $db = db_connect();
  $sql = "SELECT userid FROM users WHERE email= ?";
  $values = [$email];
  $stmt = $db->prepare($sql);
  $stmt -> execute($values);
  $result = $stmt->fetchColumn();
  $db = NULL;
  return $result;
}
function memeExists($finra){
  $db = db_connect();
  $sql = "SELECT * FROM memes WHERE finra= ?";
  $values = [$finra];
  $stmt = $db->prepare($sql);
  $stmt -> execute($values);
  $result = $stmt->fetchColumn();
  $db = NULL;
  return $result;
}
function getUserIDFromUsername($user){
  $db = db_connect();
  $sql = "SELECT userid FROM users WHERE username= ?";
  $values = [$user];
  $stmt = $db->prepare($sql);
  $stmt -> execute($values);
  $result = $stmt->fetchColumn();
  $db = NULL;
  return $result;
}

function sendEmail($to, $from, $subject, $content){
    $headers = 'From:' . $from . "\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    mail($to, $subject, $content, $headers);
}

function update($userID, $category, $value){
  try{
    $db = db_connect();
    $sql = "UPDATE users
            SET $category = ?
            WHERE userid = ?";
    $stmt = $db -> prepare($sql);
    $stmt -> execute(array($value, $userID));
    return TRUE;
  }
  catch (Exception $e){
    return FALSE;
  }
  finally{
    $db = NULL;
  }
}

function getCurrentCoins($username){
    $db = db_connect();
    $sql = "SELECT currentCoin FROM users WHERE username= ?";
    $values = [$username];
    $stmt = $db->prepare($sql);
    $stmt -> execute($values);
    $result = $stmt->fetchColumn();
    $db = NULL;
    return $result;
}

function getCatList(){
    $db = db_connect();
    $sql = "SELECT * FROM memes";
    $stmt = $db->prepare($sql);
    $stmt -> execute();
    $result = "";
    $resultArr = [];
    foreach ($stmt as $rec){
        $categoriesArray = explode(",", $rec['categories']);
        foreach($categoriesArray as $category){
            if(!in_array($category, $resultArr))
                array_push($resultArr, $category);
        }
    }
    sort($resultArr);
    $result = implode(',', $resultArr);
    $db = NULL;
    return $result;
}

function getMemeValue($finra){
    $gt = getTrendArray($finra, "fiveyear");
    $mostRecent = end($gt);
    $gtvalue = $mostRecent['value'];
    if($gtvalue == 0) $gtvalue = 1;
    $memeInfo = getMemeInfo($finra);
    $currentBuyers = $memeInfo['currentBuyers'];
    if($currentBuyers == 0) $currentBuyers = 1;
    $memeValue = ($gtvalue * $currentBuyers)/10;
    return $memeValue;
}
function getAllMemes(){
    $db = db_connect();
    $sql = "SELECT finra FROM memes";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $db = NULL;
    return $result;
}
function getMemeInfo($finra){
    $db = db_connect();
    $sql = "SELECT * FROM memes WHERE finra = ?";
    $values = [$finra];
    $stmt = $db->prepare($sql);
    $stmt->execute($values);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $db = NULL;
    return $result;
}
function getBuyersByDate($finra, $date){
    try{
        $date = date("Y-m-d h:i:s", strtotime($date));
        $db = db_connect();
        $sql = "SELECT currentBuyers FROM purchases WHERE finra = ? AND transactionDate <= ?
                ORDER BY transactionDate DESC";
        $values = [$finra, $date];
        $stmt = $db->prepare($sql);
        $stmt->execute($values);
        $result = $stmt->fetchColumn();
        if($result == null || $result == 0) $result = 1;
        return $result;
    } catch(Exception $e){
        return 1;
    } finally{
        $db = NULL;
        return 1;
    }
}

function fillChart($key, $finra, $dates){
    if($key == "") $height = "300";
    else $height = "100";
    if(getTrendArray($finra, $dates) == []){
        print("<script>
            document.getElementById('chart_div$key').innerHTML = '<h2>Not enough chart data to display. Try a different date range.</h2>';
        </script>");
    } else {
        print("
        <script>
            google.charts.load('current', {packages: ['corechart', 'line']});
            google.charts.setOnLoadCallback(drawBasic);

            function drawBasic() {

                  var data = new google.visualization.DataTable();
                  ");
                  print("
                  data.addColumn('datetime', 'date');
                  data.addColumn('number', '$finra');
                  data.addRows([
        ");
        $gt = getTrendArray($finra, $dates);
        foreach($gt as $count => $record){
            $thisDate = $record['date'];
            /*Dates Received
                week: Jul 21, 2020 at 9:00 AM
                month: Jun 28, 2020
                threemonth: May 10, 2020
                year: Aug 18 – 24, 2019
                fiveyear: Sep 27 – Oct 3, 2015
            */
            if($dates == "month" || $dates == "threemonth"){
                $newDate = date("Y-m-d", strtotime($thisDate));
            } else if($dates == "week"){

                $newDate = str_replace(" at", "", $thisDate);
                $newDate = date("Y-m-d h:i:s", strtotime($newDate));

                $year = date("Y", strtotime($newDate));
                $month = date("n", strtotime($newDate));
                $day = date("j", strtotime($newDate));
                $hours = date("G", strtotime($newDate));

                $newDate = "$year, $month, $day, $hours";

                //get mins and maxes for view window
                if($count == 0){
                    $min = $newDate;
                }
                if($count == count($gt)-1){
                    $max = $newDate;
                }
            } else if($dates == "year"){
                $dateParts = explode(" – ",$record['date']);
                $dateMD = $dateParts[0];
                $dateY = explode(", ", $dateParts[1]);
                $dateY = $dateY[1];
                $newDate = "$dateMD $dateY";
                $newDate = date("Y-m-d", strtotime($newDate));
            } else { //fiveyear
                $dateParts = explode(" – ",$record['date']);
                $formerDate = $dateParts[0];
                $latterDate = $dateParts[1];
                $dateY = explode(", ", $dateParts[1]);
                $dateY = $dateY[1];
                $newDate = date("Y-m-d", strtotime("$formerDate $dateY"));
            }
            if($dates == "week")
                print("[new Date($newDate), ");
            else
                print("[new Date('$newDate'), ");
            print($record['value'] * getBuyersByDate($finra, $newDate));
            print("]");
            if($count != count($gt)-1){
                print(", ");
            }
        }

        if($dates == "week"){ //set variables for view window
            $viewWindow = "
            viewWindow: {
                min: new Date($min),
                max: new Date($max)
            },";
        } else $viewWindow = "";
        print("]);
                  var options = {
                    hAxis: {
                      $viewWindow
                      title: 'Time',
                      textStyle:{color: '#6c3137'},
                      titleTextStyle:{color: '#6c3137'},
                      baselineColor: '#f6a3b2'
                    },
                    vAxis: {
                      title: 'Value',
                      textStyle:{color: '#6c3137'},
                      titleTextStyle:{color: '#6c3137', fontSize: 14},
                      baselineColor: '#f6a3b2'
                    },
                    legend: {
                        position: 'none'
                    },
                    height: '$height',
                    series: {
                        0: { color: '#fde2e3' }
                    },
                    backgroundColor: '#65897f'
                };

                  data.sort([{column: 0}]);
                  var chart = new google.visualization.LineChart(document.getElementById('chart_div$key'));
                  chart.draw(data, options);
            }
        </script>");
    }
}

function userHasStock($userID, $finra){
    $db = db_connect();
    $sql = "SELECT COUNT(*) FROM usertransactions WHERE finra = ? AND userid = ?";
    $values = [$finra, $userID];
    $stmt = $db->prepare($sql);
    $stmt->execute($values);
    $result = $stmt->fetch();
    $db = NULL;

    return $result[0];
}

function getUserBuyPrice($userID, $finra){
    $db = db_connect();
    $sql = "SELECT value FROM usertransactions WHERE finra = ? AND userid = ?";
    $values = [$finra, $userID];
    $stmt = $db->prepare($sql);
    $stmt->execute($values);
    $result = $stmt->fetchColumn();

    $db = NULL;
    return $result;
}
function getUserBuyAmount($userID, $finra){
    $db = db_connect();
    $sql = "SELECT amount FROM usertransactions WHERE finra = ? AND userid = ?";
    $values = [$finra, $userID];
    $stmt = $db->prepare($sql);
    $stmt->execute($values);
    $result = $stmt->fetchColumn();

    $db = NULL;
    return $result;
}
function getPurchaseDate($userID, $finra){
    $db = db_connect();
    $sql = "SELECT purchaseDate FROM usertransactions WHERE finra = ? AND userid = ?";
    $values = [$finra, $userID];
    $stmt = $db->prepare($sql);
    $stmt->execute($values);
    $result = $stmt->fetchColumn();

    $returnDate = date("Y-n-j", strtotime($result));
    $db = NULL;
    return $returnDate;
}
function getTotalReturn($userID, $finra){
    $db = db_connect();
    $sql = "SELECT amount FROM usertransactions WHERE finra = ? AND userid = ?";
    $values = [$finra, $userID];
    $stmt = $db->prepare($sql);
    $stmt->execute($values);
    $result = $stmt->fetchColumn();

    $returnAmt = $result*getMemeValue($finra);

    $db = NULL;
    return $returnAmt;
}
function getTopPerforming(){
    $db = db_connect();
    $sql = "SELECT finra, currentValue FROM memes ORDER BY currentValue DESC LIMIT 5";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $db = NULL;

    return $result;
}

function printNav(){
    print("<nav id=\"navbar\">");
    if(isset($_SESSION['user']) && !empty($_SESSION['user'])){
        print("
        <a href=\"/\" class='logo'><img src=\"assets/img/logo.png\"></a>
        <div id=\"navbar-userinfo\">
            <a href=\"/portfolio\">Your Portfolio</a>
            <div class=\"coins\"><a id='current-coin'>" . getCurrentCoins($_SESSION['user']) . "</a><img class=\"coin-img\" src=\"/assets/img/coin.png\"></div>
        </div>

        <a href=\"/charts#top\">Top Investors</a>
        <a href=\"/charts\">Stock Charts</a>
        <a href=\"/logout\" id=\"login\">Log Out</a>");
    } else {
        print("
        <a href=\"/\" class='logo'><img src=\"/assets/img/logo.png\"></a>
        <a href=\"/#about\">What is this?</a>
        <a href=\"/#top\">Top Investors</a>
        <a href=\"/#charts\">Stock Charts</a>
        <a id=\"login\" href=\"/login\">Log In</a>
        <a id=\"signup\" href=\"/signup\">Sign Up</a>
        ");
    }
    print("</nav>");


    print("
    <div id=\"hamburger-wrap\">
        <div class=\"hamburger\">
          <span class=\"line\"></span>
          <span class=\"line\"></span>
          <span class=\"line\"></span>
        </div>
    </div>
    <div class=\"hamburger-menu\">");
        if(isset($_SESSION['user'])){
          print("
            <a class='user'>". $_SESSION['user'] . "</a>
            <div class=\"ham-coins\"><a id='current-coin'>" . getCurrentCoins($_SESSION['user']) . "</a><img class=\"coin-img\" src=\"/assets/img/coin.png\"></div>
            <a href=\"/portfolio\">Your Portfolio</a>
            <a href=\"/charts#top\">Top Investors</a>
            <a href=\"/charts\">Stock Charts</a>
            <a href=\"/logout\">Log Out</a>
            ");
        } else {
          print("
          <a href=\"/#about\">What is this?</a>
          <a href=\"/#top\">Top Investors</a>
          <a href=\"/#charts\">Stock Charts</a>
          <a href=\"/login\">Log In</a>
          <a href=\"/signup\">Sign Up</a>
          ");
        }
    print("</div>

    <script>
    $('document').ready(function(){
      $(\".hamburger\").click(function(){
          $(this).toggleClass(\"is-active\");
          $('.hamburger-menu').toggleClass(\"is-active\");
      });
    })
    </script>
    ");
}
?>
