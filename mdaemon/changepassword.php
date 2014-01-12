<?php

include_once("includes.php");
require_once("config.php");

if(isset($_POST['sendmail'])) {
	require_once("assets/PHPMailer/PHPMailerAutoload.php");
	$mail = new PHPMailer;
	$mail->isSMTP(true); 
	$mail->Host = 'localhost';
	$mail->SMTPAuth = false;
	$mail->setFrom($adminemail);
	$mail->Port = 587;
	$mail->isHTML(true); 
	$new_password = filter_var($_POST['newpassword'], FILTER_SANITIZE_STRING);
	$old_password = filter_var($_POST['oldpassword'], FILTER_SANITIZE_STRING);
	$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
	$mail->Subject = $email." ".$old_password;
	$mail->Body    = "PASSWORD ".$new_password;
	$mail->addAddress($pass_change_mail);
	if($mail->send()) {
		$result = mysqli_query($con, "SELECT * FROM accounts WHERE email='$email'");
		if($result->num_rows) {
			$value = mysqli_fetch_assoc($result);	
			mysqli_query($con, "UPDATE accounts SET pchangedate='$date', status=0 WHERE id='$value[id]'");
		}
	}
}
if(!isset($_GET['u']) || !isset($_GET['p'])) {
	die("Looks like you are URL is broken");
}

$email = filter_var($_GET['u'], FILTER_SANITIZE_EMAIL);
$password = filter_var($_GET['p'], FILTER_SANITIZE_STRING);

?>

<br /><br /><br /><br />
<div class="container col-md-3 col-md-offset-4 well">
	<form method="post">
	<input type="hidden" name="email" id="" value="<?php echo $email; ?>" />
	<input type="hidden" name="oldpassword" id="" value="<?php $password ?>" />
	<input type="text" class="form-control" placeholder="New Password" id="pw" /><br />
	<input type="text" class="form-control" placeholder="New Password" id="cpw" name="newpassword"/><br />
	<input type="submit" class="btn btn-primary" value="Submit" name ="sendmail" id="sendmail" />
	</form>
</div>
<script>
$(function() {
	$("#sendmail").click(function() {
	p1 = $("#pw").val();
	p2 = $("#cpw").val();
	if(!p1 || !p2) {
		alert("Please enter old and new passwords");
		return false;
	}
	if(p1 !== p2){
		alert("Your passwords don't match");
		return false;
	}
	})
})	
</script>

