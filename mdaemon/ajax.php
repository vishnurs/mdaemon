<?php
require_once("config.php");
$con = mysqli_connect($host, $username, $password, $database);


if(isset($_POST['ajax']) && $_POST['ajax'] == 1) {
	$email_array = array($_POST['addr']);
}
else {
	$i = 0;
	foreach ($_POST as $key=>$value) {
		
		if($key != "tbl1_length") {
			if($key == $value) {
				$email_array[$i]['username'] = $_POST[$value];
				$email_array[$i]['email'] = $_POST[$value."-email"];
				$email_array[$i]['password'] = $_POST[$value."-pass"];
				$i++;	
			}
			
		}
	}	
} 


require_once("assets/PHPMailer/PHPMailerAutoload.php");


/*******************************/
$filename = "assets/files/accounts.csv";
$temp_file = "assets/files/acc_new.csv";
/*******************************/


$file = fopen($filename, 'r+');

while(!feof($file))
{
	$csv_file[] =fgetcsv($file);
}
fclose($file);
$i = 0;
$date = date("Y-m-d H:i:s");
foreach($csv_file as $csv) {
	//print_r($csv);echo "<br />";
	echo "<script>alert('".strlen($csv[0])."')</script>";
	if(in_array("email", $csv) || !strlen($csv[0])) {
		
	}
	else {	
		$result  = $con->query("SELECT id FROM accounts WHERE email='$csv[0]'");
	//echo $result->num_rows;die();
		if(!$result->num_rows) {
			mysqli_query($con,"INSERT INTO accounts(email,username,password,preqdate) VALUES('$csv[0]','$csv[1]','$csv[2]','$date')");	
		} else {
			$value = mysqli_fetch_assoc($result);	
			mysqli_query($con, "UPDATE accounts SET preqdate='$date' WHERE id='$value[id]'");
		}	
	}
}

//die();
$mail = new PHPMailer;
$mail->isSMTP(true); 
$mail->Host = 'localhost';
$mail->SMTPAuth = false;
$mail->setFrom($adminemail);
$mail->Port = 587;
$mail->isHTML(true); 


$link = 'http://localhost/mdaemon/changepassword.php?u=email@domain.com&p=cryptedpassword';
$tpl = file_get_contents('emailtemplate.txt');
$tpl = str_replace('{{link}}', $link, $tpl);

$mail->Subject = 'Mdaemon - Password Change Request';
$mail->Body    = $tpl;
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

foreach($email_array as $e) {
	$to = $e['email'];
	$mail->addAddress($to);  // Add a recipient
	if($mail->send()) {
		$result  = $con->query("SELECT * FROM accounts WHERE email='$to' ");
		if($result->num_rows) {
			$value = mysqli_fetch_assoc($result);	
			print_r($value);
			mysqli_query($con, "UPDATE accounts SET preqdate='$date' WHERE id='$value[id]'");
		}
		else {
			$email = $e['email'];
				$username = $e['username'];
				$password = $e['password'];
				mysqli_query($con,"INSERT INTO accounts(email,username,password,preqdate) VALUES('$email','$username','$password','$date')");
		}
		/*while($value = mysqli_fetch_row($result)) {
			//print_r($value);die();	
			if(in_array($e['email'], $value)) {
				mysqli_query($con, "UPDATE accounts SET preqdate='$date' WHERE email='$value[email]'");
			}
			else {
				$email = $e['email'];
				$username = $e['username'];
				$password = $e['password'];
				mysqli_query($con,"INSERT INTO accounts(email,username,password,preqdate) VALUES('$email','$username','$password','$date')");
			}	
		}*/
	}
}

$result  = $con->query("SELECT * FROM accounts");
$handle = fopen($temp_file, 'a');
while($row = mysqli_fetch_row($result)) {
	unset($row[0]);
	if($row) {
		fputcsv($handle, $row);	
	}
	
}
fclose($handle);
if(unlink($filename)) {
	rename($temp_file, $filename);
	?>
	<script>window.location.href = 'userlist.php'</script>
	<?php
	//header("Location: userlist.php");
	die();
}



?>