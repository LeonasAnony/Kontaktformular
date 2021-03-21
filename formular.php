<?php
$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
$acceptLang = ['fr', 'de', 'es', 'en'];
$lang = in_array($lang, $acceptLang) ? $lang : 'de';
require_once "src/locale/".$lang.".php";
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8" name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0'/>
    <title><?php echo $locale['header'];?></title>
    <link rel="stylesheet" href="https://unpkg.com/spectre.css/dist/spectre.min.css">
    <link rel="stylesheet" href="resource/style.css">
  </head>
<?php
$error = false;

include("src/db.php");
include("src/crypto_helper.php");
include("src/helper.php");
$pdo = new PDO('mysql:host='.$host.':'.$port.';dbname='.$dbname, $dbuser, $dbpw);


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $tel = trim($_POST['tel']);

  // Error wenn kein Nachname angegeben wurde
  if ($name == null) {
    echo "<style>.box p3 {display: inline;}</style>";
    $error = true;
  }

  // Error wenn keine Email adresse oder Telefonnummer angegeben
  if ($tel == null && $email == null) {
    echo "<style>.box p4 {display: inline;}</style>";
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
      echo "<style>.box p {display: inline;}</style>";
    } else {
      echo "<style>.box p7 {display: inline;}</style>";
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
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
          <div class="text-break"><h1><? echo $locale['title'];?></h1></div>
          <p><?php echo $locale['p'];?></p>
          <label for="<?php echo $locale['name'];?>"><input type="text" size="40" maxlength="50" name="name" placeholder="<?php echo $locale['name'];?>" value="<?php echo $name; ?>"></label>
          <label for="<?php echo $locale['email'];?>"><input type="email" size="40" maxlength="250" name="email" placeholder="<?php echo $locale['email'];?>" value="<?php echo $email; ?>"></label>
          <label for="<?php echo $locale['tel'];?>"><input type="tel" size="30" name="tel" placeholder="<?php echo $locale['tel'];?>" pattern="^(\+[0-9]{2}|[0]{2}|01)[0-9]{8,20}$" value="<?php echo $tel; ?>"></label>
          <p1><?php echo $locale['p1'];?></p1><label for="<?php echo $locale['p1for'];?>"><input type="checkbox" name="einverständnis" required></label>
          <p3><br/><?php echo $locale['p3'];?></p3>
          <p4><br/><?php echo $locale['p4'];?></p4>
          <p5><br/><?php echo $locale['p5'];?></p5>
          <p6><br/><?php echo $locale['p6'];?></p6>
          <p7><br/><?php echo $locale['p7'];?></p7>
          <input type="submit" value="<?php echo $locale['submit'];?>">
>>>>>>> fd1dc57cac3994855c9337a4ff04bc295fc5f00e
          <a href="https://bremen.klimacamp.eu"><?php echo $locale['link'];?></a>
        </form>
      </div>
    </div>
  </body>
</html>
