<?php
include($_SERVER['DOCUMENT_ROOT']."/src/crypto_conf.php");

// Funktion zum laden von Schlüsseln
function loadKeys() {
  global $keyfolder_path, $server_priv, $user_public;
  $ServerSecKey = base64_decode(file_get_contents("{$keyfolder_path}/{$server_priv}"));
  $ClientPubKey = base64_decode(file_get_contents("{$keyfolder_path}/{$user_public}"));
  return $ServerSecKey . $ClientPubKey;
}

function loadSPK() {
  global $keyfolder_path, $server_public;
  $ServerPubKey = base64_decode(file_get_contents("{$keyfolder_path}/{$server_public}"));
  $GLOBALS['ServerPubKey'] = $ServerPubKey;
}

// Funktion zum entladen von Schlüsseln
function unloadKeys()
{
  unset($ServerSecKey);
  unset($ClientPubKey);
  unset($ServerPubKey);
  unset($GLOBALS['ServerPubKey']);
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
    loadSPK();
    $decKey = $privkey . $GLOBALS['ServerPubKey'];
    $Clinonce = mb_substr(base64_decode($encData), 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
    $encrypted = mb_substr(base64_decode($encData), SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');
    $decrypted = sodium_crypto_box_open($encrypted, $Clinonce, $decKey);
    unloadKeys();
    return $decrypted;
    unset($decKey, $privkey);
  } else {
    echo "Error: Key must be String";
  }
}
?>
