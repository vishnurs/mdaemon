<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap-theme.min.css">

<?php
$date = date("Y-m-d H:i:s");
//include_once("includes.php");
require_once("config.php");
$flag = 0;
if(isset($_POST['sendmail'])) {
	require_once("assets/PHPMailer/PHPMailerAutoload.php");
	$mail = new PHPMailer;
	$mail->isSMTP(true); 
	$mail->Host = $emailhost;
	
	$mail->isHTML(true); 
	$new_password = htmlspecialchars($_POST['newpassword']);
	$old_password = htmlspecialchars($_POST['oldpassword']);
	$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($passkey), base64_decode($old_password), MCRYPT_MODE_CBC, md5(md5($passkey))), "\0");
	
	
	$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
	$mail->Subject = $email." ".$decrypted;
	$mail->Body    = "PASSWORD ".$new_password;
	
	/** SMTP AUTH ENABLE**/
	$mail->SMTPAuth = true;
	$mail->Port = 587;
	$mail->Username = $email;
	$mail->Password = $old_password;
	$mail->setFrom($email);
	/*********************/
	
	$mail->addAddress($pass_change_mail);
	if($mail->send()) {
		$result = mysqli_query($con, "SELECT * FROM accounts WHERE email='$email'");
		if($result->num_rows) {
			$value = mysqli_fetch_assoc($result);	
			mysqli_query($con, "UPDATE accounts SET pchangedate='$date', status=0 WHERE id='$value[id]'");
		}
		$flag = 1;
	}
}
if(!isset($_POST['sendmail']) && (!isset($_GET['u']) || !isset($_GET['p']))) {
	die("Looks like you are URL is broken");
}

$email = filter_var($_GET['u'], FILTER_SANITIZE_EMAIL);
$password = htmlspecialchars(stripslashes($_GET['p']));


?>

<br /><br /><br /><br />

<div class="container col-md-3 col-md-offset-4 well">
	<div class="alert alert-info <?php if($flag != 1) echo "hide" ?>">
		Password Change request Send
	</div>
	<form method="post">
	<input type="hidden" name="email" id="" value="<?php echo $email; ?>" />
	<input type="hidden" name="oldpassword" id="" value="<?php echo $password ?>" />
	<input type="text" class="form-control" placeholder="New Password" id="pw" /><br />
	<input type="text" class="form-control" placeholder="Confirm New Password" id="cpw" name="newpassword"/><br />
	<input type="submit" class="btn btn-primary" value="Submit" name ="sendmail" id="sendmail" />
	</form>
</div>
<script>
$(function() {
	$("#sendmail").click(function() {
	p1 = $("#pw").val();
	p2 = $("#cpw").val();
	if(!p1 || !p2) {
		alert("Please enter the password");
		return false;
	}
	if(p1 !== p2){
		alert("Your passwords don't match");
		return false;
	}
	})
})	
</script>

