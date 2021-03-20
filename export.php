<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8" name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0'/>
    <title>Kontaktdaten - Export</title>
  </head>
<?php
$error = false;

include("src/db.php");
include("src/crypto_helper.php");
$pdo = new PDO('mysql:host='.$host.':'.$port.';dbname='.$dbname, $dbuser, $dbpw);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $privkey = $_POST['privkey'];

  // output headers so that the file is downloaded rather than displayed
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename=contactlist.csv');

  // create a file pointer connected to the output stream
  $output = fopen('php://output', 'w');

  // output the column headings
  fputcsv($output, array('Nachname', 'E-mail', 'Telefonnummer', 'Anreise', 'Abreise'));

  // fetch the data
  $statement = $pdo->prepare("SELECT `Nachname`, `Email`, `Telefonnummer`, `Anreise`, `Abreise` FROM `kontaktverfolgung_tbl`");
  $statement->execute();

  loadKeys();

  while ($encexport = $statement->fetch()) {
    fputcsv($output, decryptdata($encexport["Nachname"], $privkey));
    fputcsv($output, decryptdata($encexport["Email"], $privkey));
    fputcsv($output, decryptdata($encexport["Telefonnummer"], $privkey));
    fputcsv($output, $encexport["Anreise"]);
    fputcsv($output, $encexport["Abreise"]);
  }

  unloadKeys();
  unset($privkey);
}
?>
  <body>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
      <div class="text-break"><h1>Data Export</h1></div>
      <input type="text" size="40" maxlength="100" name="privkey" placeholder="Enter Private Key" required>
      <input type="submit" value="Daten Exportieren">
    </form>
  </body>
</html>
