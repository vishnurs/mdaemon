<?php 

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);


$accountfilename = 'assets/files/userlist1.csv';
$adminemail = '';
$passemail = '';
$key = '';

/* Database variables*/

$username = "";
$password = "";
$database = "";
$host = "localhost";

$link = ""; //link mentioned in the mail

$con = mysqli_connect($host, $username, $password, $database);

?>