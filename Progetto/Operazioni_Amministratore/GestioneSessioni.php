<?php require_once "../ConnDb.php"; ?>
<?php require_once "../ConnessioneMongoDB.php"; ?>
<!DOCTYPE html>
<html>

<head>
    <title>Sessioni Disponibili</title>
    <link rel="stylesheet" href="../Operazioni_Amministratore/OperazioneGeneraliStyle.css">
</head>

<body>
<?php
    session_Start();


    //query per selezionare i codici sessioni delle conferenze che l'amministratore può gestire
    $pdo = ConnDb::connToDb();
    try {
        $username=$_SESSION["username"];
        // Query SQL per leggere le sessioni delle conferenze che posso gestire
        $selectCodiciSessioni = "SELECT Codice
                           FROM SESSIONE,PROGRAMMAGIORNALIERO,CREAZIONECONFERENZA
                           WHERE SESSIONE.IdProgramma = PROGRAMMAGIORNALIERO.ID AND AnnoEdizioneConferenza=AnnoEdizConf AND AcronimoConferenza=AcronimoConf AND UsernameAmministratore='$username'";
        $arrayCodiciSessioni = $pdo->query($selectCodiciSessioni);
    } catch (PDOException $e) {
        echo ("[ERRORE] Query SQL non riuscita. Errore: " . $e->getMessage());
        exit();
    }


    //query per selezionare gli id programmi delle sessioni delle conferenze che l'amministratore può gestire
    $pdo = ConnDb::connToDb();
    try {
        $username=$_SESSION["username"];
        // Query SQL per leggere le sessioni delle conferenze che posso gestire
        $selectIdProgrammi = "SELECT ID,Data,AnnoEdizioneConferenza,AcronimoConferenza 
                              FROM PROGRAMMAGIORNALIERO,CREAZIONECONFERENZA
                              WHERE AcronimoConferenza=AcronimoConf AND AnnoEdizioneConferenza=AnnoEdizConf AND UsernameAmministratore='$username'
                              ORDER BY DATA ASC";
        $arrayIdProgrammi = $pdo->query($selectIdProgrammi);
    } catch (PDOException $e) {
        echo ("[ERRORE] Query SQL non riuscita. Errore: " . $e->getMessage());
        exit();
    }


?>

<p><br><a href="../Dashboard_Utenti/Amministratore.php"> TORNA ALLA DASHBOARD </a></p>

    <center>
        <h1>GESTIONE SESSIONI</h1>
    </center>
    <form id="form" action="" method="post" style="margin: 150px;">
       <h3>ELIMINA SESSIONE</h3>
        <span> Scegli Codice Sessione: </span>
        <select name="codice">
            <option>SCEGLI</option>
            <?php
            $arrayCodiciSessioni = $pdo->query($selectCodiciSessioni);
            foreach ($arrayCodiciSessioni as $row) {
                $codiceSessione = $row['Codice'];
                echo ("<option value='$codiceSessione'>$codiceSessione</option>");
            }
            ?>
        </select>
        <input type="submit" name="submit" value="ELIMINA">
    </form>

    <form id="form1" action="" method="post" style="margin: 150px;">
        <h3>CREA SESSIONE</h3>
        <input type="text" placeholder="Codice Sessione [8 CARATTERI] " name="codice" maxlength="8" required>
        <input type="text" placeholder="Titolo " name="titolo" maxlength ="100" required>
        <input type="link" placeholder="LinkPartecipazione " name="link" maxlength="100" required >
        <input type="time" placeholder="Ora inizio " name="oraInizio" required >
        <input type="time" placeholder="Ora fine " name="oraFine" required>
        <span> Scegli Id Programma: </span>
        <select name="IDprogramma">
            <option>SCEGLI</option>
            <?php
            foreach ($arrayIdProgrammi as $row) {
                $idProgramma = $row['ID'];
                echo ("<option value='$idProgramma'>$idProgramma</option>");
            }
            ?>
        </select>
        <input type="submit" name="submit" value="CREA">
    </form>

    <?php

    //TABELLA SESSIONI
    $pdo = ConnDb::connToDb();
    try {
        $username=$_SESSION["username"];
        // Query SQL per leggere le sessioni delle conferenze che posso gestire
        $selectSessioni = "SELECT Codice,Titolo,OraInizio,OraFine,IdProgramma,Data,AnnoEdizioneConferenza,AcronimoConferenza,NumeroPresentazioni 
                           FROM SESSIONE,PROGRAMMAGIORNALIERO,CREAZIONECONFERENZA
                           WHERE SESSIONE.IdProgramma = PROGRAMMAGIORNALIERO.ID AND AnnoEdizioneConferenza=AnnoEdizConf AND AcronimoConferenza=AcronimoConf AND UsernameAmministratore='$username'";
        $arraySessioni = $pdo->query($selectSessioni);
    } catch (PDOException $e) {
        echo ("[ERRORE] Query SQL non riuscita. Errore: " . $e->getMessage());
        exit();
    }

    echo ("<table border='1'>"
        ."<caption> Sessioni Gestibili </caption>"
        . "<tr>"
        . "<th> Codice </th> "
        . "<th> Titolo </th>"
        . "<th> Ora Inizio</th>"
        . "<th> Ora Fine</th>"
        . "<th> Id Programma</th>"
        . "<th> Data </th>"
        . "<th> Anno Edizione Conferenza</th>"
        . "<th> Acronimo Conferenza</th>"
        . "<th> Numero Presentazioni</th>"
        . "</tr>");
    foreach ($arraySessioni as $row) {
        echo ("<tr>"
            . "<td>" . $row["Codice"] . "</td>"
            . "<td>" . $row["Titolo"] . "</td>"
            . "<td>" . $row["OraInizio"] . "</td>"
            . "<td>" . $row["OraFine"] . "</td>"
            . "<td>" . $row["IdProgramma"] . "</td>"
            . "<td>" . $row["Data"] . "</td>"
            . "<td>" . $row["AnnoEdizioneConferenza"] . "</td>"
            . "<td>" . $row["AcronimoConferenza"] . "</td>"
            . "<td>" . $row["NumeroPresentazioni"] . "</td>"
            . "</tr>");
    }
    echo ("</table>");



    if (isset($_POST["submit"])) {
        if ($_POST["submit"] == "ELIMINA") {
            return eliminaSessione();
        } else if ($_POST["submit"] == "CREA") {
            return creaSessione();
        }
    }


    //ELIMINA SESSIONE
    function eliminaSessione()
    {
        $pdo = ConnDb::connToDb();
        try {

            $codice = $_POST["codice"];

            $sql1 = 'CALL eliminaSessione(:codice)';
            $statement1 = $pdo->prepare($sql1);
            $statement1->bindParam(':codice', $codice, PDO::PARAM_STR);
            $statement1->execute();
            echo '<script language="javascript">';
            echo 'alert("Eliminazione avvenuta con successo")';
            echo '</script>';
            header("Refresh:0");

            $nome=$_SESSION["username"];
            ConnessioneMongoDB::connMongoDB($nome,$sql1);

        } catch (PDOException $e) {
            echo '<script language="javascript">';
            echo 'alert("ERRORE : impossibile eliminare la sessione")';
            echo '</script>';
            exit();
        }
    }

    //  CREA SESSIONE
    function creaSessione()
    {
        $pdo = ConnDb::connToDb();
        try {

            $codice = $_POST["codice"];
            $titolo = $_POST["titolo"];
            $link = $_POST["link"];
            $oraInizio = $_POST["oraInizio"];
            $oraFine = $_POST["oraFine"];
            $idProgramma = $_POST["IDprogramma"];

            $sql1 = 'CALL creazioneSessione(:codice, :titolo, :link, :oraInizio, :oraFine, :IDprogramma)';
            $statement1 = $pdo->prepare($sql1);
            $statement1->bindParam(':codice', $codice, PDO::PARAM_STR);
            $statement1->bindParam(':titolo', $titolo, PDO::PARAM_STR);
            $statement1->bindParam(':link', $link, PDO::PARAM_STR);
            $statement1->bindParam(':oraInizio', $oraInizio, PDO::PARAM_STR);
            $statement1->bindParam(':oraFine', $oraFine, PDO::PARAM_STR);
            $statement1->bindParam(':IDprogramma', $idProgramma, PDO::PARAM_INT);
            $statement1->execute();
            echo '<script language="javascript">';
            echo 'alert("Sessione creata con successo")';
            echo '</script>';
            header("Refresh:0");

            $nome=$_SESSION["username"];
            ConnessioneMongoDB::connMongoDB($nome,$sql1);

        } catch (PDOException $e) {
            echo '<script language="javascript">';
            echo 'alert("ERRORE : impossibile creare la sessione \n 1 - controlla id programma \n 2 - Codice sessione già esistente")';
            echo '</script>';
            exit();
        }
    }

    ?>

</body>

</html>