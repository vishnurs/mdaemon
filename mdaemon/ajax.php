<?php
require_once("config.php");
$con = mysqli_connect($host, $username, $password, $database);


if(isset($_POST['ajax']) && $_POST['ajax'] == 1) {
	$email_array[0]['username'] = $_POST['username'];
	$email_array[0]['email'] = $_POST['email'];
	$email_array[0]['password'] = $_POST['password'];
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

//print_r($email_array);die();
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
$error = 0;
$date = date("Y-m-d H:i:s");
foreach($csv_file as $csv) {
	if($csv)
	if(in_array("email", $csv)) {
		
	}
	else {	
		$result  = $con->query("SELECT id FROM accounts WHERE email='$csv[0]'");
		if(!$result->num_rows) {
			mysqli_query($con,"INSERT INTO accounts(email,username,password,preqdate) VALUES('$csv[0]','$csv[1]','$csv[2]','$date')");	
		} else {
			
			//$value = mysqli_fetch_assoc($result);	
			//mysqli_query($con, "UPDATE accounts SET preqdate='$date' WHERE id='$value[id]'");
		}	
	}
}

//die();
$mail = new PHPMailer;
$mail->isSMTP(true); 
$mail->Host = $emailhost;
$mail->SMTPAuth = true;
$mail->Username   = $adminemail; // SMTP account username
$mail->Password   = $passemail;  // SMTP account password

$mail->setFrom($adminemail);
$mail->Port = 587;
$mail->isHTML(true); 




$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

foreach($email_array as $e) {
		
	$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($passkey), $e['password'], MCRYPT_MODE_CBC, md5(md5($passkey))));
    $link = $pass_change_link."?u=".$e['email']."&p=".$encrypted;
	
	$tpl = file_get_contents('emailtemplate.txt');
	$tpl = str_replace('{{link}}', $link, $tpl);
	
	$mail->Subject = 'Mdaemon - Password Change Request';
	$mail->Body    = $tpl;

	$to = $e['email'];
	$mail->addAddress($to);  // Add a recipient
	if($mail->send()) {
		$result  = $con->query("SELECT * FROM accounts WHERE email='$to' ");
		if($result->num_rows) {
			$value = mysqli_fetch_assoc($result);	
			//print_r($value);
			mysqli_query($con, "UPDATE accounts SET preqdate='$date', status=1 WHERE id='$value[id]'");
		}
		else {
			$email = $e['email'];
				$username = $e['username'];
				$password = $e['password'];
				mysqli_query($con,"INSERT INTO accounts(email,username,password,preqdate,status) VALUES('$email','$username','$password','$date','1')");
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
	else {

		//echo "Mailer ERROR ".$mail->ErrorInfo."<br />";
		$error = 1;
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
	if(!isset($_POST['ajax']) && !$_POST['ajax'] == 1) {
	?>
	<script>window.location.href = 'userlist.php'</script>
	<?php
	}
	else {
		if($error) {
			echo $mail->ErrorInfo;
			die();	
		}
		else {
			//echo date("Y-m-d H:i:s");
			echo "success";
			die();
		}
	}
}



?>