<?php
function db_connect() {
    $config = parse_ini_file('config.ini');
    $connection =  new PDO("mysql:host=" . $config['servername'] . ";dbname=" . $config['dbname'], $config['username'], $config['password']);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $connection;
}

?>
