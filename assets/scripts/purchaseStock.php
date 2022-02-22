<?php
    REQUIRE_ONCE "functions.php";
    $finra = $_POST['finra'];
    $userID = getUserIDFromUsername($_POST['username']);
    $value = getMemeValue($finra);
    $amount = $_POST['amount']; //number of stocks bought
    $date = date("Y-m-d h:i:s");

    $memeInfo = getMemeInfo($finra);
    $currentBuyers = $memeInfo['currentBuyers'];

    $response = "";

    //update user transactions
    try {
        //value : price per stock it was bought at
        //amount: number of stocks bought

        $db = db_connect();
        $sql = "INSERT INTO usertransactions (userid, purchaseDate, finra, value, amount)
                VALUES (?, ?, ?, ?, ?)";
        $values = [$userID, $date, $finra, $value, $amount];
        $stmt = $db->prepare($sql);
        $stmt->execute($values);
    }
    catch (Exception $e){
        $response = $e->getMessage();
    }
    finally{
        $db = NULL;
    }

    //update current coin
    try {
        $currentCoin = getCurrentCoins($_POST['username']);
        $newCoin = $currentCoin-($amount*$value);
        $db = db_connect();
        $sql = "UPDATE users SET currentCoin = ? WHERE userid = ?";
        $values = [$newCoin, $userID];
        $stmt = $db->prepare($sql);
        $stmt->execute($values);
        $coins = $newCoin;
    }
    catch (Exception $e){
        $response = $e->getMessage();
    }
    finally{
        $db = NULL;
    }

    //update meme buyers & value
    try {
        $newBuyers = $currentBuyers+1;

        $gt = getTrendArray($finra, "fiveyear");
        $mostRecent = end($gt);
        $gtvalue = $mostRecent['value'];
        if($gtvalue == 0) $gtvalue = 1;

        $newValue = ($gtvalue * $newBuyers)/10;
        
        $db = db_connect();
        $sql = "UPDATE memes SET currentBuyers = ?, currentValue = ? WHERE finra = ?";
        $values = [$newBuyers, $newValue, $finra];
        $stmt = $db->prepare($sql);
        $stmt->execute($values);
        $buyers = $newBuyers;
    }
    catch (Exception $e){
        $response = $e->getMessage();
    }
    finally{
        $db = NULL;
    }

    //update purchases db
    try {
        $newBuyers = $currentBuyers+1;

        $gt = getTrendArray($finra, "fiveyear");
        $mostRecent = end($gt);
        $gtvalue = $mostRecent['value'];
        if($gtvalue == 0) $gtvalue = 1;

        $newValue = ($gtvalue * $newBuyers)/10;


        $db = db_connect();
        $sql = "INSERT INTO purchases (finra, transactionDate, value, currentBuyers, currentValue)
                VALUES (?, ?, ?, ?, ?)";
        $values = [$finra, $date, $amount, $newBuyers, $newValue];
        $stmt = $db->prepare($sql);
        $stmt->execute($values);
    }
    catch (Exception $e){
        $response =$e->getMessage();
    }
    finally{
        $db = NULL;
    }
    $return_arr = array(
        "err" => $response,
        "coins" => number_format($coins,2),
        "buyers" => $buyers
    );
    echo json_encode($return_arr);
?>
