<?php require_once "../ConnDb.php"; ?>
<html lang="it">
<head>
    <title> Calendario  </title>
</head>
<body>

  <link rel="stylesheet" href="../Operazioni_Amministratore/OperazioneGeneraliStyle.css">
  <h1>CALENDARIO</h1>

<?php
session_start();
$username=$_SESSION["username"];

# TABELLA DEI PROGRAMMI GIORNALIERI delle conferenze a cui sono iscritto

$pdo = ConnDb::connToDb();
    try {
        $selectProgrammi = "SELECT ID,Data,AnnoEdizioneConferenza,AcronimoConferenza 
                            FROM PROGRAMMAGIORNALIERO,CREAZIONECONFERENZA
                            WHERE AcronimoConferenza=AcronimoConf AND AnnoEdizioneConferenza=AnnoEdizConf AND UsernameAmministratore='$username'
                            ORDER BY DATA ASC";
        $arrayProgrammi = $pdo->query($selectProgrammi);
    } catch (PDOException $e) {
        echo ("[ERRORE] Query SQL non riuscita. Errore: " . $e->getMessage());
        exit();
    }

    echo ("<table border='1' align='center'>"
    . "<tr>"
    . "<th> ID</th>"
    . "<th> Data </th>"
    . "<th> Anno Edizione Conferenza </th>"
    . "<th> Acronimo Conferenza </th>"
    . "</tr>");
foreach ($arrayProgrammi as $row) {
    echo ("<tr>"
        . "<td>" . $row["ID"] . "</td>"
        . "<td>" . $row["Data"] . "</td>"
        . "<td>" . $row["AnnoEdizioneConferenza"] . "</td>"
        . "<td>" . $row["AcronimoConferenza"] . "</td>"
        . "</tr>");
}
echo ("</table>");

?>
<p><br><a href="../Dashboard_Utenti/Amministratore.php"> TORNA ALLA DASHBOARD </a></p>
</body>
</html>
