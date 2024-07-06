<?php
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "pup_lms";
$port = "3307";

$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname, $port);

if (!is_null($mysqli->connect_error)) {
    throw new Exception('Connection failed: ' . $mysqli->connect_error);
}
?>