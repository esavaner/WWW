<?php
    $dbhost = 'localhost:3306';
    $dbuser = 'user';
    $dbpass = '1q2w3e4r';
    $dbname = 'www5';
    define('DB_SERVER', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASSWORD', '1q2w3e4r');
    define('DB_NAME', 'www5');

    $link = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);

    if($link === false) {
        die(mysqli_connect_error());
    }
?>