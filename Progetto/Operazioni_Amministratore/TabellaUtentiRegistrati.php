<?php require_once "../ConnessioneMongoDB.php"; ?>
<?php require_once "../ConnDb.php"; ?>
<!DOCTYPE html>
<html>

<head>
    <title>TabellaUtentiRegistrati</title>
    <link rel="stylesheet" href="../Operazioni_Amministratore/OperazioneGeneraliStyle.css">
</head>

<body>
    <?php
    session_Start();
    $nome = $_SESSION["username"];
    $tipoUtente = $_SESSION["tipoUtente"];
    ?>

    <p><br><a href="GestioneConferenze.php"> TORNA INDIETRO </a></p>

    <h3> UTENTI REGISTRATI ALLA CONFERENZA <?php echo($_GET["acronimo"] . "    ".$_GET["annoEdizione"] ) ?> </h3>

</body>

      
<?php
    $pdo = ConnDb::connToDb();

    $acronimoConferenza =  $_GET["acronimo"] ;
    $annoEdizioneConferenza = $_GET["annoEdizione"];

    $selectUtenti = "SELECT Username,Nome,Cognome
                            FROM UTENTE,REGISTRAZIONE 
                            WHERE  Username=UsernameUtente AND AcronimoConf='$acronimoConferenza' AND AnnoEdiziConf='$annoEdizioneConferenza'";
    $listaUtenti = $pdo->query($selectUtenti);
    ?>

    <?php
    
 
    // stampa gli utenti
    echo ("<table border='1' align=left>"
    . "<tr>"
    . "<th> Username </th> "
    . "<th> Nome </th>"
    . "<th> Cognome </th>"
    . "</tr>");

    foreach ($listaUtenti as $row) {
    echo ("<tr>"
        . "<td>" . $row["Username"] . "</td>"
        . "<td>" . $row["Nome"] . "</td>"
        . "<td>" . $row["Cognome"] . "</td>"
        . "</tr>");
    }
    echo ("</table>");

    ?>

</html>