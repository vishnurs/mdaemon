<?php 

/*** DEVELOPMENT MODE **/
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);


//$accountfilename = 'assets/files/userlist1.csv';
$accountfilename = 'assets/files/accounts1.csv';

/*** EMAIL settings ***/
$emailhost = '';
$adminemail = ''; // Email address to send mails from
$passemail = ''; // Email Password
$passkey = 'usemeforencrption'; 

/* Database variables*/

$username = "";
$password = "";
$database = "";
$host = "localhost";

//$change_password_email_host = "localhost"; // email host in changepassword.php

$pass_change_link = "http://dev.vishnurs.com/mdaemon/changepassword.php/"; //path to the changepassword.php file with trailing '/'
$pass_change_mail = "mdaemon@pizzaitaliastorre.it"; // Email id to which password change request is send.


$con = mysqli_connect($host, $username, $password, $database);

?>