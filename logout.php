<?php
// HSTS aktivieren
header("Strict-Transport-Security:max-age=31536000; includeSubdomains");
// Sicherstellen das die Seite über HTTPS aufgerufen wird
function isSSL(){
  if($_SERVER['https'] == 1) /* Apache */ {
    return TRUE;
  } elseif ($_SERVER['https'] == 'on') /* IIS */ {
    return TRUE;
  } elseif ($_SERVER['SERVER_PORT'] == 443) /* others */ {
    return TRUE;
  } elseif (!empty($_SERVER['HTTPS'])) {
    return TRUE;
  } else {
    return FALSE; /* just using http */
  }
}
if (!isSSL()) {
  header('location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
  exit();
}
// Richtige Sprache laden
$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
$acceptLang = ['fr', 'de', 'es', 'en'];
$lang = in_array($lang, $acceptLang) ? $lang : 'de';
$overwrite = $_GET['lang'];
$lang = $overwrite ?? $lang;
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

include("src/config.php");
$pdo = new PDO('mysql:host='.$host.':'.$port.';dbname='.$dbname, $dbuser, $dbpw);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $code = $_POST['code'];

  // Kontrollieren ob der Code 8 Zeichen lang ist
  if (strlen($code) != 8) {
    // echo 'Bitte einen richtigen Code angeben<br>';
    echo "<style>.box p3 {display: inline;}</style>";
    $error = true;
  }

  // Schauen ob Code existiert oder schon ausgetragen wurde
  if (!$error) {
    $statement = $pdo->prepare("SELECT Abreise FROM ".$tablename." WHERE code = :code");
    $result = $statement->execute(array('code' => $code));
    $exists = $statement->fetch();
    if ($exists == true) {
      if (!is_null($exists[0])) {
        echo "<style>.box p5 {display: inline;}</style>";
        $error = true;
      }
    } else {
      echo "<style>.box p4 {display: inline;}</style>";
      $error = true;
    }
  }

  // Dauer = 0
  // Abreise = date
  // Einträge in die Datenbank schreiben
  if (!$error) {
    $statement = $pdo->prepare("UPDATE ".$tablename." SET Abreise = :abreise WHERE code = :code");
    $done = $statement->execute(array('abreise' => date("Y-m-d H:i:s"), 'code' => $code));
    if ($done == 1) {
      echo "<style>.box p {display: inline;}</style>";
    } else {
      echo "<style>.box p6 {display: inline;}</style>";
    }
  }
}
?>
  <body>
    <div class="columns">
      <div class="box column col-xs-11 col-sm-8 col-md-7 col-lg-6 col-xl-5 col-4">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
          <div class="text-break"><h1><?php echo $locale['title'];?></h1></div>
          <p><?php echo $locale['lp'];?></p>
          <label for="<?php echo $locale['code'];?>"><input type="text" size="40" maxlength="8" name="code" placeholder="<?php echo $locale['code'];?>"></label>
          <p3><br/><?php echo $locale['lp3'];?></p3>
          <p4><br/><?php echo $locale['lp4'];?></p4>
          <p5><br/><?php echo $locale['lp5'];?></p5>
          <p6><br/><?php echo $locale['lp6'];?></p6>
          <input type="submit" value="<?php echo $locale['lsubmit'];?>">
          <a href="https://bremen.klimacamp.eu"><?php echo $locale['link'];?></a>
        </form>
      </div>
    </div>
  </body>
</html>
