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

  // Error wenn kein Nachname angegeben wurde
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

<<<<<<< HEAD
  // Error wenn kein Anreise Datum angegeben wurde oder das Datum nicht valide ist
  if ($anreise == null || !checkIfIsAValidDate($anreise)) {
    echo "<style>.box p8 {display: inline;}</style>";
    $error = true;
  }

  // Error wenn kein Abreise Datum oder Dauer angegeben/ abgehakt
  if (($abreise == null && $dauer == null) ||  !checkIfIsAValidDate($abreise)) {
    echo "<style>.box p9 {display: inline;}</style>";
    $error = true;
  }

  // Error wenn Abreise vor anreise ist
  if ($dauer == null && (strtotime($abreise) < strtotime($anreise))) {
    echo "<style>.box p11 {display: inline;}</style>";
    $error = true;
  }

  // Wert für Dauer/ Code definieren
  if ($dauer == "on") {
    $dauer = 1;
  } else {
    $dauer = 0;
  }

=======
>>>>>>> fd1dc57cac3994855c9337a4ff04bc295fc5f00e

  // In die Datenbank einfügen
  if (!$error) {
    //encrypt data
    $code = generateRandomString();
    $encname = encryptdata($name);
    $encemail = encryptdata($email);
    $enctel = encryptdata($tel);

    $statement = $pdo->prepare("INSERT INTO ".$tablename." (Code, Nachname, Email, Telefonnummer, Anreise) VALUES (:code, :name, :mail, :tel, :anrei)");
    $result = $statement->execute(array('code' => $code, 'name' => $encname, 'mail' => $encemail, 'tel' => $enctel, 'anrei' => date("Y-m-d H:i:s")));
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
<<<<<<< HEAD
        <form id="BX" action="?data=true" method="post">
          <h1>Kontaktverfolgung</h1>
          <p1>Daten wurden verschlüsselt gespeichert. Daher du dich ohne Abreise Datum eingetragen hast nutze bitte diesen Code: <?php if(isset($code)){echo $code;}?> um dich<a href="logout.php"> hier</a> auzutragen sobald du gehst.</p1>
          <input type="text" id="NN" size="40" maxlength="50" name="name" placeholder="Nachname">
          <input type="email" id="EM" size="40" maxlength="250" name="email" placeholder="Email oder">
          <input type="tel" id="TL" size="30" name="tel" placeholder="Telefonnummer" pattern="^(\+[0-9]{2}|[0]{2}|01)[0-9]{8,20}$" value="<?php print($tel); ?>">
          <p2>Anreise:</p2>
          <input type="date" id="AN" size="40" name="andate" placeholder="Anreise">
          <p3>Abreise:</p3>
          <input type="date" id="AB" size="40 "name="abdate" placeholder="Abreise">
          <p3>oder:<br/></p3>
          <p4>Ich weiß noch nicht wann ich wieder gehe: </p4><input type="checkbox" id="DA" name="dauer">
          <p>E-Mail oder Telefonnummer muss ausgefüllt sein<br/></p>
          <p5>Bitte eine gültige Telefonnummer angeben<br/></p5>
          <p6>Bitte eine gültige Telefonnummer angeben<br/></p6>
          <p7>Bitte einen Namen angeben<br/></p7>
          <p8>Bitte ein Anreisedatum angeben<br/></p8>
          <p9>Bitte ein Abreisedatum angeben oder den Haken aktivieren<br/></p9>
          <p10>Beim Abspecheichern ist ein Fehler aufgetreten. Bitte versuche es erneut. Wenn das Problem weiterhin besteht ende dich an T:@Le0nas<br/></p10>
          <p11></br>Abreise darf nicht vor Anreise stattfinden</br></p11>
          <input type="submit" id="SA" value="Speichern">
=======
        <form action="?data=true" method="post">
          <h1>Kontakt-</h1>
          <h1>formular</h1>
          <p1>Daten wurden verschlüsselt gespeichert, nutze bitte diesen Code:<b> <?php if(isset($code)){echo $code;}?></b> um dich <a href="logout.php">hier</a> auzutragen sobald du gehst.</p1>
          <label for="Nachname"><input type="text" size="40" maxlength="50" name="name" placeholder="Nachname"></label>
          <label for="Email"><input type="email" size="40" maxlength="250" name="email" placeholder="Email oder"></label>
          <label for="Telefonnummer"><input type="tel" size="30" name="tel" placeholder="Telefonnummer" pattern="^(\+[0-9]{2}|[0]{2}|01)[0-9]{8,20}$"></label>
          <p4>Ich bin mit der Verarbeitung meiner Daten zu zwecken der Kontaktverfolgung im Falle einer Infektion im Camp einverstanden: </p4><label for="Ich bin mit der Verarbbeitung meiner Daten einverstanden"><input type="checkbox" name="einverständnis" required></label>
          <p7><br/>Bitte einen Namen angeben</p7>
          <p><br/>E-Mail oder Telefonnummer muss ausgefüllt sein</p>
          <p5><br/>Bitte eine gültige Email angeben</p5>
          <p6><br/>Bitte eine gültige Telefonnummer angeben</p6>
          <p10><br/>Beim Abspecheichern ist ein Fehler aufgetreten. Bitte versuche es erneut. Wenn das Problem weiterhin besteht wende dich an T:@Le0nas</p10>
          <input type="submit" value="Speichern">
>>>>>>> fd1dc57cac3994855c9337a4ff04bc295fc5f00e
          <a href="https://bremen.klimacamp.eu">Klimacamp</a>
        </form>
      </div>
    </div>
  </body>
</html>
