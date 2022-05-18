<?php require_once "../ConnDb.php"; ?>
<html lang="it">
<head>
    <title> Calendario  </title>
</head>
<body>
        <link rel="stylesheet" href="../Operazioni_Generali/OperazioniStyle.css">
<?php
session_start();
$username=$_SESSION["username"];
$tipoUtente = $_SESSION["tipoUtente"];

    # TORNA ALLA DASHBOARD
    if ($tipoUtente == "GENERICO") {echo ("<p><br><a href='../Dashboard_Utenti/Generico.php'> TORNA ALLA DASHBOARD </a></p>"); }
    if ($tipoUtente == "PRESENTER") { echo ("<p><br><a href='../Dashboard_Utenti/Presenter.php'> TORNA ALLA DASHBOARD </a></p>");  }
    if ($tipoUtente == "SPEAKER") {  echo ("<p><br><a href='../Dashboard_Utenti/Speaker.php'> TORNA ALLA DASHBOARD </a></p>"); }
    if ($tipoUtente == "AMMINISTRATORE") {  echo ("<p><br><a href='../Dashboard_Utenti/Amministratore.php'> TORNA ALLA DASHBOARD </a></p>"); }
?>
    <h1>CALENDARIO</h1>
 <?php

# TABELLA DEI PROGRAMMI GIORNALIERI delle conferenze a cui sono iscritto

$pdo = ConnDb::connToDb();
    try {
        $selectProgrammi = "SELECT ID,Data,AnnoEdizioneConferenza,AcronimoConferenza 
                            FROM PROGRAMMAGIORNALIERO,REGISTRAZIONE, CONFERENZA
                            WHERE AcronimoConferenza=AcronimoConf AND AnnoEdizioneConferenza=AnnoEdiziConf AND UsernameUtente='$username'
                                  AND CONFERENZA.AnnoEdizione=PROGRAMMAGIORNALIERO.AnnoEdizioneConferenza 
                                  AND CONFERENZA.Acronimo=PROGRAMMAGIORNALIERO.AcronimoConferenza
                                  AND CONFERENZA.Svolgimento='ATTIVA'
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
</body>
</html>
