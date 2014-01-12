<?php 
$key = "useme";
$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), "wddsfdsfs$#%$%&@#$#@$!SAC", MCRYPT_MODE_CBC, md5(md5($key))));
$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($encrypted), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
echo $encrypted;echo "<br /><br /><br /><br />";
echo $decrypted;

?>