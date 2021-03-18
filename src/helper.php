<?php
// teste ob eingabe eine valides Datum ist
function checkIfIsAValidDate($DateString){
    return (bool)strtotime($DateString);
}

// teste ob eingabe eine valide Telefonnummer ist
function checkIfIsAValidPhonenumber($TelString){
    return true;
}

// Funktion um zufälligen String für "Code" zu generieren
function generateRandomString($length = 8) {
  $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $charactersLength = strlen($characters);
  $randomString = '';
  for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
  }
  return $randomString;
}
?>
