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
    <script src="https://hcaptcha.com/1/api.js" async defer></script>
    <link rel="stylesheet" href="resource/style.css">
  </head>
<?php
$error = false;

include("src/config.php");
include("src/crypto_helper.php");
include("src/helper.php");
$pdo = new PDO('mysql:host='.$host.':'.$port.';dbname='.$dbname, $dbuser, $dbpw);


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $tel = trim($_POST['tel']);
  $data = array(
    'secret' => $HCsecret_key,
    'response' => $_POST['h-captcha-response'],
    'sitekey' => $HCsite_key
  );

  $verify = curl_init();
  curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
  curl_setopt($verify, CURLOPT_POST, true);
  curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
  curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($verify);
  // var_dump($response);

  $responseData = json_decode($response);
  if(!$responseData->success) {
    echo "<style>.box p8 {display: inline;}</style>";
    $error = true;
  }

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
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
          <div class="text-break"><h1><?php echo $locale['title'];?></h1></div>
          <p><?php echo $locale['p'].$code."</b>";?></p>
          <label for="<?php echo $locale['name'];?>"><input type="text" size="40" maxlength="50" name="name" placeholder="<?php echo $locale['name'];?>" value="<?php echo $name; ?>"></label>
          <label for="<?php echo $locale['email'];?>"><input type="email" size="40" maxlength="250" name="email" placeholder="<?php echo $locale['email'];?>" value="<?php echo $email; ?>"></label>
          <label for="<?php echo $locale['tel'];?>"><input type="tel" size="30" name="tel" placeholder="<?php echo $locale['tel'];?>" pattern="^(\+[0-9]{2}|[0]{2}|01)[0-9]{8,20}$" value="<?php echo $tel; ?>"></label>
          <p1><?php echo $locale['p1'];?></p1><label for="<?php echo $locale['p1for'];?>"><input type="checkbox" name="einverständnis" required></label>
          <p3><br/><?php echo $locale['p3'];?></p3>
          <p4><br/><?php echo $locale['p4'];?></p4>
          <p5><br/><?php echo $locale['p5'];?></p5>
          <p6><br/><?php echo $locale['p6'];?></p6>
          <p7><br/><?php echo $locale['p7'];?></p7>
          <br/><div class="h-captcha" data-sitekey="<?php echo $HCsite_key;?>" data-theme="dark"></div>
          <p8><br/><?php echo $locale['p8'];?></p8>
          <input type="submit" value="<?php echo $locale['submit'];?>">
          <a href="https://bremen.klimacamp.eu"><?php echo $locale['link'];?></a>
        </form>
      </div>
    </div>
  </body>
</html>
