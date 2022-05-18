<?php require_once "../ConnDb.php"; ?>
<!DOCTYPE html>
<html>

<head>
    <title>Tutorial da presentare </title>
    <link rel="stylesheet" href="../Operazioni_Speaker/OperazioniSpeakerStyle.css">
</head>

<body>

<p><br><a href="../Dashboard_Utenti/Speaker.php"> TORNA ALLA DASHBOARD </a></p>

        <h1>VISUALIZZAZIONE TUTORIAL DA PRESENTARE</h1>
    <?php
    session_Start();
    $nome = $_SESSION['username'];
    

    //


    $pdo = ConnDb::connToDb();
    $selectTutorial = "SELECT * 
                        FROM TUTORIAL,PRESENTAZIONE_TUTORIAL,PRESENTAZIONE
                        WHERE TUTORIAL.CodiceTutorial=PRESENTAZIONE_TUTORIAL.CodiceTutorial  AND UsernameSpeaker='$nome'
                               AND PRESENTAZIONE.Codice = TUTORIAL.CodiceTutorial";

    $listaTutorial = $pdo->query($selectTutorial);    

     
     //stampa tutorial
     echo ("<table border='1' align=center>"
     . "<tr>"
     . "<caption> TABELLA TUTORIAL</caption>"
     . "<th> Codice </th> "
     . "<th> Numero Sequenza </th>"
     . "<th> Codice Sessione </th>"
     . "<th> Orario Inizio </th>"
     . "<th> Orario fine </th>"
     . "<th> Titolo </th> "
     . "<th> Abstract </th>"
     . "</tr>");

     foreach($listaTutorial as $row)
     {
         echo ("<tr>"
             . "<td>" . $row["Codice"] . "</td>"
             . "<td>" . $row["NumeroSequenza"] . "</td>"
             . "<td>" . $row["CodiceSessione"] . "</td>"
             . "<td>" . $row["OraInizio"] . "</td>"
             . "<td>" . $row["OraFine"] . "</td>"
             . "<td>" . $row["Titolo"] . "</td>"
             . "<td>" . $row["Abstract"] . "</td>"
             . "</tr>");
     }
 
 echo ("</table>");

    ?>

    


</body>
</html>