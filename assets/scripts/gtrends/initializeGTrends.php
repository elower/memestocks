<?php
//      Google Trends has a pretty strict rate limit. In order to deal with this,
//      I have this file set to run as a cron job to write to daily.txt once per day
//      with the updated trends data in a large serialized array. Once the
//      array is initialized, I am able to extrapolate the data as many times
//      as needed across the site.


REQUIRE 'vendor/autoload.php';
REQUIRE_ONCE "/var/www/memestocks.io/public_html/assets/scripts/functions.php";

use Google\GTrends;
$options = [
    'hl' => 'en-US',
    'tz' => -60,
    'geo' => 'US',
];
/** @var Google\GTrends $gt */
$gt = new GTrends($options);

$dailyArray = [];

//initialize search times
$week = "now 7-d";
$month = "today 1-m";
$threemonth = "today 3-m";

$today = date('Y-m-d');

$oneyr = date("Y-m-d",strtotime("-1 year"));
$oneyear = "$oneyr $today";

$fiveyr = date("Y-m-d",strtotime("-5 years"));
$fiveyear = "$fiveyr $today";

$allMemes = getAllMemes();

foreach($allMemes as $meme){
    $finra = $meme['finra'];
    $memeInfo = getMemeInfo($finra);
    $searchTerm = str_replace(",", "", $memeInfo['name']);
    $searchTerm = str_replace("?", "", $searchTerm);
    $searchTerm = str_replace(".", "", $searchTerm);
    if($memeInfo['addmeme'] == 1) $searchTerm.= " meme";

    $iotWeek = $gt->interestOverTime($searchTerm, 0, $week, '');
    $iotMonth = $gt->interestOverTime($searchTerm, 0, $month, '');
    $iotThreeMonth = $gt->interestOverTime($searchTerm, 0, $threemonth, '');
    $iotOneYear = $gt->interestOverTime($searchTerm, 0, $oneyear, '');
    $iotFiveYear = $gt->interestOverTime($searchTerm, 0, $fiveyear, '');

    $weekArray = [];
    foreach($iotWeek as $record){
        if($record['hasData'][0] == 1){
            $pushArray = array(
                "date" => $record['formattedTime'],
                "value" =>$record['formattedValue'][0]
            );
            array_push($weekArray, $pushArray);
        }
    }

    $monthArray = [];
    foreach($iotMonth as $record){
        if($record['hasData'][0] == 1){
            $pushArray = array(
                "date" => $record['formattedTime'],
                "value" =>$record['formattedValue'][0]
            );
            array_push($monthArray, $pushArray);
        }
    }

    $threeMonthArray = [];
    foreach($iotThreeMonth as $record){
        if($record['hasData'][0] == 1){
            $pushArray = array(
                "date" => $record['formattedTime'],
                "value" =>$record['formattedValue'][0]
            );
            array_push($threeMonthArray, $pushArray);
        }
    }

    $oneYearArray = [];
    foreach($iotOneYear as $record){
        if($record['hasData'][0] == 1){
            $pushArray = array(
                "date" => $record['formattedTime'],
                "value" =>$record['formattedValue'][0]
            );
            array_push($oneYearArray, $pushArray);
        }
    }

    $fiveYearArray = [];
    foreach($iotFiveYear as $record){
        if($record['hasData'][0] == 1){
            $pushArray = array(
                "date" => $record['formattedTime'],
                "value" =>$record['formattedValue'][0]
            );
            array_push($fiveYearArray, $pushArray);
        }
    }

    /* arrays now look like this:
        array(
            0 => array(
                "date" => Jul 12, 2019 at 12:15 AM,
                "value" => 84
            ),
            1 => array(
                "date" => Jul 13, 2019 at 12:15 AM,
                "value" => 92
            )
        )

    etc.
    */

    //now add them each to the encompassing array
    $dailyArray["$finra"] = array(
            "week" => $weekArray,
            "month" => $monthArray,
            "threemonth" => $threeMonthArray,
            "year" => $oneYearArray,
            "fiveyear" => $fiveYearArray
    );

    sleep(30); //needed in order to avoid DDOS to Google Trends
}
    /* encompassing array now looks like this:
        $dailyArray = (
            "searchterm" => array(
                "week" => array(
                    array(
                        "date" => "7days ago",
                        "value" => "84"
                    ),
                    array(
                        "date" => "today",
                        "value" => "92"
                    )
                ),
                "month" => array(
                    array(
                        "date" => "one month ago",
                        "value" => "22"
                    ),
                    array(
                        "date" => "today",
                        "value" => "92"
                    )
                ),
                "threemonth" => array(
                    array(
                        "date" => "three months ago",
                        "value" => "62"
                    ),
                    array(
                        "date" => "today",
                        "value" => "92"
                    )
                )
            ),
            "searchterm2" => array(
                "week" => array(
                    array(
                        "date" => "7days ago",
                        "value" => "84"
                    ),
                    array(
                        "date" => "today",
                        "value" => "92"
                    )
                ),
                "month" => array(
                    array(
                        "date" => "one month ago",
                        "value" => "22"
                    ),
                    array(
                        "date" => "today",
                        "value" => "92"
                    )
                ),
                "threemonth" => array(
                    array(
                        "date" => "three months ago",
                        "value" => "62"
                    ),
                    array(
                        "date" => "today",
                        "value" => "92"
                    )
                )
            )
        );

    etc.
    */
//}


// $dailyArray = array(
//     "$searchTerm" => array(
//         "week" => array(
//             array(
//                 "date" => "7days ago",
//                 "value" => "84"
//             ),
//             array(
//                 "date" => "today",
//                 "value" => "92"
//             )
//         ),
//         "month" => array(
//             array(
//                 "date" => "one month ago",
//                 "value" => "22"
//             ),
//             array(
//                 "date" => "today",
//                 "value" => "92"
//             )
//         ),
//         "threemonth" => array(
//             array(
//                 "date" => "three months ago",
//                 "value" => "62"
//             ),
//             array(
//                 "date" => "today",
//                 "value" => "92"
//             )
//         )
//     ),
//     "search term 2" => array(
//         "week" => array(
//             array(
//                 "date" => "7days ago",
//                 "value" => "84"
//             ),
//             array(
//                 "date" => "today",
//                 "value" => "92"
//             )
//         ),
//         "month" => array(
//             array(
//                 "date" => "one month ago",
//                 "value" => "22"
//             ),
//             array(
//                 "date" => "today",
//                 "value" => "92"
//             )
//         ),
//         "threemonth" => array(
//             array(
//                 "date" => "three months ago",
//                 "value" => "62"
//             ),
//             array(
//                 "date" => "today",
//                 "value" => "125"
//             )
//         )
//     )
// );

//check that all values are set -_- bc apparently I have to do that now
foreach($dailyArray as $key => $each){
    if(empty($each['week'])){
        $log  = date("F j, Y, g:i a"). " : Array not full. Stopped at: " . $key .
        "\nEXITING SCRIPT WITH ERROR\n" .
        "------------------------------------\n\n";
        //Save string to log, use FILE_APPEND to append.
        file_put_contents("/var/www/memestocks.io/public_html/assets/scripts/gtrends/gtrenderrors.log", $log, FILE_APPEND);
        exit;
    }
}

//Write json encoded array to temp file
$dailyEncoded = json_encode($dailyArray);
$data = tmpfile();
fwrite($data, $dailyEncoded);
fseek($data, 0);

try {
    $db = db_connect();
    $sql = "UPDATE memeData
            SET data = :data";
    $stmt = $db->prepare($sql);

    $stmt->bindParam(':data', $data, PDO::PARAM_LOB);
    $stmt->execute();
    $log  = date("F j, Y, g:i a"). " : Successfully pulled data." .
    "\nENDING SCRIPT WITH SUCCESS\n" .
    "------------------------------------\n\n";
    file_put_contents("/var/www/memestocks.io/public_html/assets/scripts/gtrends/gtrenderrors.log", $log, FILE_APPEND);
} catch (Exception $e) {
    $log  = date("F j, Y, g:i a"). " : Error inserting array to DB." . $e->getMessage() .
    "\nENDING SCRIPT WITH ERROR\n" .
    "------------------------------------\n\n";
    file_put_contents("/var/www/memestocks.io/public_html/assets/scripts/gtrends/gtrenderrors.log", $log, FILE_APPEND);
}
finally{
  fclose($data);
  $db = NULL;
}

//update db
try {
    $db = db_connect();
    $allMemes = getAllMemes();
    foreach($allMemes as $finra){
        $currentValue = getMemeValue($finra['finra']);
        $sql = "UPDATE memes
                SET currentValue = ?
                WHERE finra = ?";
        $values = [$currentValue, $finra['finra']];
        $stmt = $db->prepare($sql);
        $stmt->execute($values);
    }
} catch (Exception $e) {
    echo($e->getMessage());
}
finally{
  $db = NULL;
}

?>
