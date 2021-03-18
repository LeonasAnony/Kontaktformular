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
  $code = $_POST['code'];

  // Kontrollieren ob der Code 6 Zeichen lang ist
  if (strlen($code) != 6) {
    // echo 'Bitte einen richtigen Code angeben<br>';
    echo "<style>.box p {display: inline;}</style>";
    $error = true;
  }

  // Schauen ob Code existiert oder schon ausgetragen wurde
  if (!$error) {
    $statement = $pdo->prepare("SELECT Dauer FROM data WHERE code = :code");
    $result = $statement->execute(array('code' => $code));
    $exists = $statement->fetch();
    if ($exists == false) {
      echo "<style>.box p5 {display: inline;}</style>";
      $error = true;
    }
    if ($exists["Dauer"] === "0") {
      echo "<style>.box p6 {display: inline;}</style>";
      $error = true;
    }
  }

  // Dauer = 0
  // Abreise = date
  // Einträge in die Datenbank schreiben
  if (!$error) {
    $statement = $pdo->prepare("UPDATE data SET Dauer = :dauer, Abreise = :abreise WHERE code = :code");
    $done = $statement->execute(array('dauer' => 0, 'abreise' => date("Y-m-d H:i:s"), 'code' => $code));
    if ($done == 1) {
      echo "<style>.box p1 {display: inline;}</style>";
    } else {
      echo "<style>.box p7 {display: inline;}</style>";
    }
  }
}
?>
  <body>
    <div class="columns">
      <div class="box column col-xs-11 col-sm-8 col-md-7 col-lg-6 col-xl-5 col-4">
        <form id="BX" action="?data=true" method="post">
          <h1>Kontaktverfolgung</h1>
          <p1>Deine Kontaktdaten werden in 4 Wochen automatisch gelöscht. Danke fürs Besuchen des Klimacamps!</p1>
          <input type="text" id="NN" size="40" maxlength="50" name="code" placeholder="Code">
          <p>Bitte einen richtigen Code angeben<br/></p>
          <p5>Dein Code ist nicht in der Datenbank, gib bitte den Code vom Eintragen an, oder wende dich an T:@Le0nas<br/></p5>
          <p6>Der Code ist schon ausgetragen<br/></p6>
          <p7>etwas ist schiefgelauf, bitte probiere es erneut<br/></p7>
          <input type="submit" id="SA" value="Austragen">
          <a href="https://bremen.klimacamp.eu">Klimacamp</a>
        </form>
      </div>
    </div>
  </body>
</html>
