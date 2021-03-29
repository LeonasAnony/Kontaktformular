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
include("src/config.php");
include("src/crypto_helper.php");
$pdo = new PDO('mysql:host='.$host.':'.$port.';dbname='.$dbname, $dbuser, $dbpw);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $privkey = base64_decode($_POST['privkey']);

  // get entries
  $statement = $pdo->prepare("SELECT `Nachname`,`Email`,`Telefonnummer`,`Anreise`,`Abreise` FROM ".$tablename);
  $statement->execute();

  $entries = array();
  while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
     $entries[] = $row;
  }

  $decentries = array();
  loadSPK();

  foreach ($entries as $encrypted) {
    $decentries[] = [decryptdata($encrypted["Nachname"], $privkey), decryptdata($encrypted["Email"], $privkey), decryptdata($encrypted["Telefonnummer"], $privkey), $encrypted["Anreise"], $encrypted["Abreise"] ?? "0000-00-00 00:00:00"];
  }

  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename=db_export_'.date('Y-m-d').'.csv');
  $output = fopen('php://output', 'w');
  fputcsv($output, array('Nachname', 'Email', 'Telefonnummer', 'Anreise', 'Abreise'));

  foreach ($decentries as $row) {
    fputcsv($output, $row);
  }

  unloadKeys();
  unset($privkey);
  unset($decentries);
}
?>
