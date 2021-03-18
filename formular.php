<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8" name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0'/>
    <title>Klimacamp Coronaformular</title>
    <link rel="stylesheet" href="https://unpkg.com/spectre.css/dist/spectre.min.css">
    <link rel="stylesheet" href="resource/style.css">
  </head>
<?php
$error = false;

include("src/db.php");
include("src/crypto_helper.php");
include("src/helper.php");


$pdo = new PDO('mysql:host='.$host.':'.$port.';dbname='.$dbname, $dbuser, $dbpw);

if (isset($_GET['data'])) {
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $tel = trim($_POST['tel']);
  $anreise = trim($_POST['andate']);
  $abreise = trim($_POST['abdate']);
  $dauer = trim($_POST['dauer']);

  // Error wenn kein Name angegeben wurde
  if ($name == null) {
    echo "<style>.box p7 {display: inline;}</style>";
    $error = true;
  }

  // Error wenn keine Email adresse oder Telefonnummer angegeben
  if ($tel == null && $email == null) {
    echo "<style>.box p {display: inline;}</style>";
    $error = true;
  }

  // Error wenn eine Email angeben ist, aber keine valide
  if ($email != null && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<style>.box p5 {display: inline;}</style>";
    $error = true;
  }

  // Error wenn eine Telefonnummer angeben ist, aber keine valide
  if ($tel != null && !checkIfIsAValidPhonenumber($tel)) {
    echo "<style>.box p6 {display: inline;}</style>";
    $error = true;
  }

  // Error wenn kein Anreise Datum angegeben wurde oder das Datum nicht valide ist
  if ($anreise == null || !checkIfIsAValidDate($anreise)) {
    echo "<style>.box p8 {display: inline;}</style>";
    $error = true;
  }

  // Error wenn kein Abreise Datum oder Dauer angegeben/ abgehakt
  if (($abreise == null && $dauer == null) || !checkIfIsAValidDate($abreise)) {
    echo "<style>.box p9 {display: inline;}</style>";
    $error = true;
  }

  // Error wenn Abreise vor anreise ist
  if (strtotime($abreise) < strtotime($anreise)) {
    echo "<style>.box p11 {display: inline;}</style>";
    $error = true;
  }

  // Wert für Dauer/ Code definieren
  if ($dauer == "on") {
    $dauer = 1;
    echo "<style>.box p3 {display: inline;}</style>";
  } else {
    $dauer = 0;
  }


  // In die Datenbank einfügen
  if (!$error) {
    //encrypt data
    $code = generateRandomString();
    $encname = encryptdata($name);
    $encemail = encryptdata($email);
    $enctel = encryptdata($tel);

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
          <h1>Kontakt-</h1>
          <h1>formular</h1>
          <p1>Daten wurden verschlüsselt gespeichert<br/></p1>
          <p3>Daher du dich ohne Abreise Datum eingetragen hast nutze bitte diesen Code:<b> <?php if(isset($code)){echo $code;}?></b> um dich <a href="logout.php">hier</a> auzutragen sobald du gehst.</p3>
          <label for="Nachname"><input type="text" size="40" maxlength="50" name="name" placeholder="Nachname"></label>
          <label for="Email"><input type="email" size="40" maxlength="250" name="email" placeholder="Email oder"></label>
          <label for="Telefonnummer"><input type="tel" size="30" name="tel" placeholder="Telefonnummer" pattern="^(\+[0-9]{2}|[0]{2}|01)[0-9]{8,20}$"></label>
          <p2>Anreise:</p2>
          <label for="Anreise"><input type="date" size="40" name="andate" placeholder="Anreise"></label>
          <p2>Abreise:</p2>
          <label for="Abreise"><input type="date" size="40 "name="abdate" placeholder="Abreise"></label>
          <p2>oder:<br/></p2>
          <p4>Ich weiß noch nicht wann ich wieder gehe: </p4><label for="Weiß noch nicht wann ich gehe"><input type="checkbox" name="dauer"></label>
          <p>E-Mail oder Telefonnummer muss ausgefüllt sein<br/></p>
          <p5>Bitte eine gültige Email angeben<br/></p5>
          <p6>Bitte eine gültige Telefonnummer angeben<br/></p6>
          <p7>Bitte einen Namen angeben<br/></p7>
          <p8>Bitte ein Anreisedatum angeben<br/></p8>
          <p9>Bitte ein Abreisedatum angeben oder den Haken aktivieren<br/></p9>
          <p10>Beim Abspecheichern ist ein Fehler aufgetreten. Bitte versuche es erneut. Wenn das Problem weiterhin besteht ende dich an T:@Le0nas<br/></p10>
          <p11>Abreise darf nicht vor Anreise stattfinden<br/></p11>
          <input type="submit" value="Speichern">
          <a href="https://bremen.klimacamp.eu">Klimacamp</a>
        </form>
      </div>
    </div>
  </body>
</html>
