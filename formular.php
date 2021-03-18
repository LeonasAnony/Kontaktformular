<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8" name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0'/>
    <title>Klimacamp Coronaformular</title>
    <link rel="stylesheet" href="https://unpkg.com/spectre.css/dist/spectre.min.css">
    <link rel="stylesheet" href="style.css">
  </head>
<?php
$error = false;

include("db.php");
$pdo = new PDO('mysql:host='.$host.':'.$port.';dbname='.$dbname, $dbuser, $dbpw);

if (isset($_GET['data'])) {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $tel = strval($_POST['tel']);
  $anreise = $_POST['andate'];
  $abreise = $_POST['abdate'];
  $dauer = $_POST['dauer'];

  // Wurde ein Name angegeben
  if (empty(trim($name))) {
    echo "<style>.box p7 {display: inline;}</style>";
    $error = true;
  }
  // Wurde eine Email oder eine Telefonnummer angegeben
  if (trim($email) != null) {
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<style>.box p5 {display: inline;}</style>";
        $error = true;
    }
  }
  if ((trim($tel) == null && trim($email) == null)) {
    echo "<style>.box p {display: inline;}</style>";
    $error = true;
  }
  // Wurde ein Anreise Datum angegeben
  if ($anreise == null) {
    echo "<style>.box p8 {display: inline;}</style>";
    $error = true;
  }
  // Wurde ein Abreise Datum oder Dauer angegeben/ abgehakt
  if ($abreise == null && $dauer == null) {
    echo "<style>.box p9 {display: inline;}</style>";
    $error = true;
  }

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

  // Wert für Dauer/ Code definieren
  if ($dauer == "on") {
    $code = generateRandomString();
    $dauer = 1;
  } else {
    $dauer = 0;
    $code = NULL;
  }

  //encrypt data
  $encname = encryptdata($name);
  $encemail = encryptdata(trim($email));
  $enctel = encryptdata(trim($tel));

  // In die Datenbank einfügen
  if (!$error) {
    $statement = $pdo->prepare("INSERT INTO ".$tablename." (Nachname, Email, Telefonnummer, Anreise, Abreise, Dauer, Code) VALUES (:name, :mail, :tel, :anrei, :abrei, :dauer, :code)");
    $result = $statement->execute(array('name' => $encname, 'mail' => $encemail, 'tel' => $enctel, 'anrei' => $anreise, 'abrei' => $abreise, 'dauer' => $dauer, 'code' => $code));
    if($result) {
      echo "<style>.box p1 {display: inline;}</style>";
    } else {
      echo "<style>.box p10 {display: inline;}</style>";
      echo $statement->errorInfo()[2];
    }
  }
}
?>
  <body>
    <div class="columns">
      <div class="box column col-xs-11 col-sm-8 col-md-7 col-lg-6 col-xl-5 col-4">
        <form id="BX" action="?data=true" method="post">
          <h1>Kontaktverfolgung</h1>
          <p1>Daten wurden verschlüsselt gespeichert. Daher du dich ohne Abreise Datum eingetragen hast nutze bitte diesen Code: <?php if(isset($code)){echo $code;}?> um dich<a href="logout.php"> hier</a> auzutragen sobald du gehst.</p1>
          <input type="text" id="NN" size="40" maxlength="50" name="name" placeholder="Nachname">
          <input type="email" id="EM" size="40" maxlength="250" name="email" placeholder="Email oder">
          <input type="tel" id="TL" size="30" name="tel" placeholder="Telefonnummer" pattern="^(\+[0-9]{2}|[0]{2}|01)[0-9]{8,20}$">
          <p2>Anreise:</p2>
          <input type="date" id="AN" size="40" name="andate" placeholder="Anreise">
          <p3>Abreise:</p3>
          <input type="date" id="AB" size="40 "name="abdate" placeholder="Abreise">
          <p3>oder:<br/></p3>
          <p4>Ich weiß noch nicht wann ich wieder gehe: </p4><input type="checkbox" id="DA" name="dauer">
          <p>E-Mail oder Telefonnummer muss ausgefüllt sein<br/></p>
          <p5>E-Mail oder Telefonnummer muss ausgefüllt sein<br/></p5>
          <p6>Bitte eine gültige Telefonnummer angeben<br/></p6>
          <p7>Bitte einen Namen angeben<br/></p7>
          <p8>Bitte ein Anreisedatum angeben<br/></p8>
          <p9>Bitte ein Abreisedatum angeben oder den Haken aktivieren<br/></p9>
          <p10>Beim Abspecheichern ist ein Fehler aufgetreten. Bitte versuche es erneut. Wenn das Problem weiterhin besteht ende dich an T:@Le0nas<br/></p10>
          <input type="submit" id="SA" value="Speichern">
          <a href="https://bremen.klimacamp.eu">Klimacamp</a>
        </form>
      </div>
    </div>
  </body>
</html>
