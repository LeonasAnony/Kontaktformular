<?php
// teste ob eingabe eine valides Datum ist
function checkIfIsAValidDate($myDateString){
    return (bool)strtotime($myDateString);
}

// teste ob eingabe eine valides Datum ist
function checkIfIsAValidPhonenumber($myDateString){
    return true;
}

// Funktion um zufälligen String für "Code" zu generieren
function generateRandomString($length = 6) {
  $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $charactersLength = strlen($characters);
  $randomString = '';
  for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
  }
  return $randomString;
}

?>
