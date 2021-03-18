<?php
// Funktion zum laden von Schlüsseln
function loadKeys() {
  $ServerSecKey = base64_decode(file_get_contents("keys/server.priv"));
  $ClientPubKey = base64_decode(file_get_contents("keys/user.pub"));
  return $ServerSecKey . $ClientPubKey;
}

// Funktion zum entladen von Schlüsseln
function unloadKeys()
{
  unset($ServerSecKey);
  unset($ClientPubKey);
}

// Funktion zum Daten Verschlüsseln
function encryptdata($data) {
  if (is_string($data) == true) {
    $encKey = loadKeys();
    unloadKeys();
    $Sernonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
    return base64_encode($Sernonce . sodium_crypto_box($data, $Sernonce, $encKey));
    unset($encKey);
  } else {
    echo "Error: Data must be String";
  }
}
?>
