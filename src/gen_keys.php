<?php
$path = "keys/";
$server_public = 'server.pub';
$server_priv = 'server.priv';

$user_public = 'user.pub';
$user_priv = 'user.priv';

if ( file_exists("{$path}{$user_public}") == false) {
  $keypair1 = sodium_crypto_box_keypair();
  print("public server key:<br>");
  print(base64_encode(sodium_crypto_box_publickey($keypair1))."<br>");

  file_put_contents("{$path}{$server_public}", base64_encode(sodium_crypto_box_publickey($keypair1)));
  file_put_contents("{$path}{$server_priv}", base64_encode(sodium_crypto_box_secretkey($keypair1)));

  $keypair2 = sodium_crypto_box_keypair();
  file_put_contents("{$path}{$user_public}", base64_encode(sodium_crypto_box_publickey($keypair2)));
  print("This is your private key. save this key it will not be stored on the server!!!:<br>");
  print(base64_encode(sodium_crypto_box_secretkey($keypair2)));
}else{
  print("files already generated");
}
?>
