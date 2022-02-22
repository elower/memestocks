<?php
    REQUIRE_ONCE "functions.php";
    print("<html>");
    print("<pre>");
    // $test = (getTrendArray('AHB', 'threemonth'));
    // foreach($test as $record){
    //     print_r($record->{'date'});
    // }
    // $gt = getTrendArray('AHB', 'month');
    //
    // foreach($gt as $key => $record){
    //     print("[".$record['date'].", ");
    //     print($record['value']);
    //     print("]");
    //     if($key != count($gt)-1){
    //         print(", ");
    //     }
    // }
    // print_r($gt);
    // foreach($gt as $key => $record){
    //     print("[".$record->{'date'}.", ");
    //     print($record->{'value'});
    //     print("]");
    //     if($key != count($gt)-1){
    //         print(", ");
    //     }
    // }

    // week
    // month
    // threemonth
    // year
    // fiveyear


    //js should be YYYY-MM-DDTHH:mm:ss
    //new Date(Year, Month, Day, Hours, Minutes, Seconds, Milliseconds)

    /*Dates Received
        week: Jul 21, 2020 at 9:00 AM
        month: Jun 28, 2020
        threemonth: May 10, 2020
        year: Aug 18 – 24, 2019
        fiveyear: Sep 27 – Oct 3, 2015
    */

    try{
        $db = db_connect();
        $sql = "SELECT data
                FROM memeData";
           $stmt = $db->prepare($sql);
           $stmt->execute();
           $result = $stmt->fetchColumn();
           $result = json_decode($result);
           $result = json_decode(json_encode($result), true);
           print_r($result);

    } catch (Exception $e) {

    }
    finally{
      $db = NULL;
    }

    $gt = getTrendArray("AHB", "fiveyear");

    foreach($gt as $record){
        $dateParts = explode(" – ",$record['date']);
        $formerDate = $dateParts[0];
        $latterDate = $dateParts[1];
        $dateY = explode(", ", $dateParts[1]);
        $dateY = $dateY[1];
        $newDate = date("Y-m-d", strtotime("$formerDate $dateY"));
        ?>
        <script>
            var d = new Date("<?php echo($newDate); ?>");
            console.log(d);
        </script>
        <?php
    }
    ?>


    </html>
    <?
    print("</pre>");
?>
