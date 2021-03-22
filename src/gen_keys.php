<?php

$keyfolder_name = "keys";
$path = dirname(__DIR__, 1)."/{$keyfolder_name}";

$server_public = 'server.pub';
$server_priv = 'server.priv';

$user_public = 'user.pub';
$user_priv = 'user.priv';

$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";


if (!file_exists($path)){
  mkdir($path, 0754);
}

try{
    touch("{$path}/access_test");
    $header = get_headers("{$url}/{$keyfolder_name}/access_test");
    if (strpos($header[0],"200")) {
       throw new Exception('Error: Key is accessible from the web');
    }
    unlink("{$path}/access_test");
}catch (Exception $e) {
    exit($e->getMessage());
}

if ( file_exists("{$path}/{$user_public}") == false) {
  $keypair1 = sodium_crypto_box_keypair();
  print("public server key:<br>");
  print(base64_encode(sodium_crypto_box_publickey($keypair1))."<br>");

  file_put_contents("{$path}/{$server_public}", base64_encode(sodium_crypto_box_publickey($keypair1)));
  file_put_contents("{$path}/{$server_priv}", base64_encode(sodium_crypto_box_secretkey($keypair1)));

  $keypair2 = sodium_crypto_box_keypair();
  file_put_contents("{$path}/{$user_public}", base64_encode(sodium_crypto_box_publickey($keypair2)));
  print("This is your private key. save this key it will not be stored on the server!!!:<br>");
  print(base64_encode(sodium_crypto_box_secretkey($keypair2)));
}else{
  print("files already generated");
}
?>
