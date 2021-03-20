<?php
// Funktion zum laden von Schlüsseln
function loadKeys() {
  $ServerSecKey = base64_decode(file_get_contents("src/keys/server.priv"));
  $ClientPubKey = base64_decode(file_get_contents("src/keys/user.pub"));
  $ServerPubKey = base64_decode(file_get_contents("src/keys/server.pub"));
  return $ServerSecKey . $ClientPubKey;
}

// Funktion zum entladen von Schlüsseln
function unloadKeys()
{
  unset($ServerSecKey);
  unset($ClientPubKey);
  unset($ServerPubKey);
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

// Funktion zum Daten Entschlüsseln
function decryptdata($encData, $privkey) {
  if (is_string($privkey) == true) {
    $decKey = $privkey . $ServerPubKey;
    $Clinonce = mb_substr($encData, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
    $encrypted = mb_substr($encData, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');
    $decrypted = sodium_crypto_box_open($encrypted, $Clinonce, $decKey);
    unloadKeys();
    return $decrypted;
    unset($decKey, $privkey);
  } else {
    echo "Error: Key must be String";
  }
}
?>
