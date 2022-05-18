<?php require_once "../ConnDb.php"; ?>
<?php require_once "../ConnessioneMongoDB.php"; ?>
<!DOCTYPE html>
<html>

<head>
    <title>Presentazioni Disponibili</title>
    <link rel="stylesheet" href="../Operazioni_Amministratore/OperazioneGeneraliStyle.css">
    <script src="aggiungiFormPresentazione.js"></script>

</head>

<body>
    <?php
    session_Start();
    $nome = $_SESSION["username"];

    $pdo = ConnDb::connToDb();

    try {
        // Query SQL per leggere i codici delle presentazioni che posso gestire
        $selectPresentazioni = "SELECT Codice,NumeroSequenza,CodiceSessione,OraInizio,OraFine
                                FROM PRESENTAZIONE
                                WHERE (CodiceSessione IN (SELECT Codice 
                                                          FROM SESSIONE 
                                                          WHERE IdProgramma IN (SELECT ID  
                                                                                FROM PROGRAMMAGIORNALIERO,CREAZIONECONFERENZA
                                                                                WHERE (AcronimoConferenza=AcronimoConf AND AnnoEdizioneConferenza=AnnoEdizConf AND UsernameAmministratore='$nome'))))";


        $arrayPresentazioni = $pdo->query($selectPresentazioni);
    } catch (PDOException $e) {
        echo ("[ERRORE] Query SQL non riuscita. Errore: " . $e->getMessage());
        exit();
    }


    // Query SQL per leggere i codici delle sessioni che posso gestire
    $pdo = ConnDb::connToDb();
    try {
        $username = $_SESSION["username"];
        // Query SQL per leggere le sessioni delle conferenze che posso gestire
        $selectCodiciSessioni = "SELECT Codice
                           FROM SESSIONE,PROGRAMMAGIORNALIERO,CREAZIONECONFERENZA
                           WHERE SESSIONE.IdProgramma = PROGRAMMAGIORNALIERO.ID AND AnnoEdizioneConferenza=AnnoEdizConf AND AcronimoConferenza=AcronimoConf AND UsernameAmministratore='$username'";
        $arrayCodiciSessioni = $pdo->query($selectCodiciSessioni);
    } catch (PDOException $e) {
        echo ("[ERRORE] Query SQL non riuscita. Errore: " . $e->getMessage());
        exit();
    }

    ?>

    <p><br><a href="../Dashboard_Utenti/Amministratore.php"> TORNA ALLA DASHBOARD </a></p>

    <center>
        <h1>GESTIONE PRESENTAZIONI </h1>
    </center>

    <form id="form1" action="" method="post" style="margin: 150px;">
        <h3> ELIMINA PRESENTAZIONE </h3>
        <span> Scegli Codice Presentazione: </span>
        <select name="codice">
            <option>SCEGLI</option>
            <?php
            foreach ($arrayPresentazioni as $row) {
                $codicePresentazione = $row['Codice'];
                echo ("<option value='$codicePresentazione'>$codicePresentazione</option>");
            }
            ?>
        </select>
        <input type="submit" name="submit" value="ELIMINA">
    </form>

    <form id="form2" action="" method="post" style="margin: 150px;">
        <h3 id="titolo"> CREAZIONE PRESENTAZIONE </h3>
        <input type="text" placeholder="Codice presentazione [7 CARATTERI] " name="codice" maxlength="7" required>
        <span> Scegli Codice Sessione: </span>
        <select name="codiceSessione">
            <option>SCEGLI</option>
            <?php
            $arrayCodiciSessioni = $pdo->query($selectCodiciSessioni);
            foreach ($arrayCodiciSessioni as $row) {
                $codiceSessione = $row['Codice'];
                echo ("<option value='$codiceSessione'>$codiceSessione</option>");
            }
            ?>
        </select>
        <input type="time" placeholder="Ora inizio" name="oraInzio" required>
        <input type="time" placeholder="Ora fine" name="oraFine" required>
        <span id="tipoPres"> TIPO DI PRESENTAZIONE:
            <input type="button" name="tutorial" value="TUTORIAL" onclick="aggiungiFormTutorial()">
            <input type="button" name="articolo" value="ARTICOLO" onclick="aggiungiFormArticolo()">
        </span>
        <input id="btnCrea" type="submit" name="submit" value="CREA" disabled>
    </form>

    <?php

    //TABELLA DELLE PRESENTAZIONI CHE POSSO GESTIRE

    $arrayPresentazioni = $pdo->query($selectPresentazioni);

    echo ("<table border='1' align=left>"
        . "<tr>"
        . "<th> Codice Presentazione </th> "
        . "<th> Numero Sequenza </th>"
        . "<th> Codice Sessione </th>"
        . "<th> Ora Inizio </th>"
        . "<th> Ora fine </th>"
        . "</tr>");

    foreach ($arrayPresentazioni as $row) {
        echo ("<tr>"
            . "<td>" . $row["Codice"] . "</td>"
            . "<td>" . $row["NumeroSequenza"] . "</td>"
            . "<td>" . $row["CodiceSessione"] . "</td>"
            . "<td>" . $row["OraInizio"] . "</td>"
            . "<td>" . $row["OraFine"] . "</td>"
            . "</tr>");
    }
    echo ("</table>");


    if (isset($_POST["submit"])) {
        if ($_POST["submit"] == "ELIMINA") {
            return eliminaPresentazione();
        } else if ($_POST["submit"] == "CREA") {
            return creaPresentazione();
        }
    }

    // ELIMINA PRESENTAZIONE 

    function eliminaPresentazione()
    {
        $pdo = ConnDb::connToDb();
        try {

            $codice = $_POST["codice"];

            $sql1 = 'CALL eliminaPresentazione(:codice)';
            $statement1 = $pdo->prepare($sql1);
            $statement1->bindParam(':codice', $codice, PDO::PARAM_STR);
            $statement1->execute();

            echo '<script language="javascript">';
            echo 'alert("Presentazione eliminata con successo")';
            echo '</script>';

            header("Refresh:0");
        } catch (PDOException $e) {
            echo '<script language="javascript">';
            echo 'alert("ERRORE : impossibile eliminare la presentazione")';
            echo '</script>';
            exit();
        }
    }


    // CREA PRESENTAZIONE

    function creaPresentazione()

    {
        $pdo = ConnDb::connToDb();

        try {
            $codice = $_POST["codice"];
            $numeroSeq = numeroSequenza();
            $codiceSessione = $_POST["codiceSessione"];
            $oraInizio = $_POST["oraInzio"];
            $oraFine = $_POST["oraFine"];

            if (controlloOrariPresentazione() == 1) {

                    $sql2 = "CALL inserimentoPresentazione(:codice, :numS, :codiceSessione, :oraInizio, :oraFine)";
                    $statement1 = $pdo->prepare($sql2);
                    $statement1->bindParam(':codice', $codice, PDO::PARAM_STR);
                    $statement1->bindParam(':numS', $numeroSeq, PDO::PARAM_INT);
                    $statement1->bindParam(':codiceSessione', $codiceSessione, PDO::PARAM_STR);
                    $statement1->bindParam(':oraInizio', $oraInizio, PDO::PARAM_STR);
                    $statement1->bindParam(':oraFine', $oraFine, PDO::PARAM_STR);
                    $statement1->execute();

                    if ($_POST["tipoPres"] == "ARTICOLO") {
                        creaArticolo();
                    }
                    if ($_POST["tipoPres"] == "TUTORIAL") {
                        creaTutorial();
                    }


                    echo '<script language="javascript">';
                    echo 'alert("Presentazione aggiunta con successo")';
                    echo '</script>';
                 
                }else {
                echo '<script language="javascript">';
                echo 'alert("ERRORE: Orario non disponibile per la presentazione")';
                echo '</script>';
            }

            header("Refresh:0");
        } catch (PDOException $e) {
            echo '<script language="javascript">';
                echo 'alert("ERRORE: \n 1 - il codice presentazione è già esistente \n 2 - Non possono esserci presentazioni con lo stesso numero sequenza per la stessa sessione")';
                echo '</script>';
            exit();
        }
    }

    function creaTutorial()
    {
        $pdo = ConnDb::connToDb();
        try {
            $codice = $_POST["codice"];
            $titoloTutorial = $_POST["titoloTutorial"];
            $abstract = $_POST["abstract"];

            $sql2 = "CALL inserimentoTutorial(:codice, :titoloTutorial, :abstract)";
            $statement1 = $pdo->prepare($sql2);
            $statement1->bindParam(':codice', $codice, PDO::PARAM_STR);
            $statement1->bindParam(':titoloTutorial', $titoloTutorial, PDO::PARAM_STR);
            $statement1->bindParam(':abstract', $abstract, PDO::PARAM_STR);
            $statement1->execute();

            $nome = $_SESSION["username"];
            ConnessioneMongoDB::connMongoDB($nome, $sql2);
        } catch (PDOException $e) {
            echo (" codice errore: " . $e->getMessage());
            exit();
        }
    }

    function creaArticolo()
    {
        $pdo = ConnDb::connToDb();
        try {
            $codice = $_POST["codice"];
            $titoloArticolo = $_POST["titoloArticolo"];
            $filePDF = $_POST["filePDF"];
            $numeroPagine = $_POST["numeroPagine"];



            $sql2 = "CALL inserimentoArticolo(:codice, :titoloArticolo, :filePDF, :numeroPagine)";
            $statement1 = $pdo->prepare($sql2);
            $statement1->bindParam(':codice', $codice, PDO::PARAM_STR);
            $statement1->bindParam(':titoloArticolo', $titoloArticolo, PDO::PARAM_STR);
            $statement1->bindParam(':filePDF', $filePDF, PDO::PARAM_STR);
            $statement1->bindParam(':numeroPagine', $numeroPagine, PDO::PARAM_STR);
            $statement1->execute();

            $nome = $_SESSION["username"];
            ConnessioneMongoDB::connMongoDB($nome, $sql2);
            
        } catch (PDOException $e) {
            echo (" codice errore: " . $e->getMessage());
            exit();
        }
    }



    //metodo che controlla se gli orari della presentazione che voglio creare siano compresi negli orari della sessione (e che non si svrappongono ad altre presentazioni)
    function controlloOrariPresentazione()
    {
        $pdo = ConnDb::connToDb();

        $codiceSessione = $_POST["codiceSessione"];

        $selectOrariSessione = "SELECT OraInizio AS OraInizioSessione ,OraFine AS OraFineSessione
                                FROM SESSIONE 
                                WHERE Codice ='$codiceSessione'";

        $orariSessione = $pdo->query($selectOrariSessione);
        $rowOrariSessione = $orariSessione->fetch();

        $oraInizioSessione = $rowOrariSessione["OraInizioSessione"];
        $oraFineSessione = $rowOrariSessione["OraFineSessione"];

        $oraInizioPresentazione = $_POST["oraInzio"];
        $oraFinePresentazione = $_POST["oraFine"];


        $result = 0;
        //controllo che l'orario della presentazione sia compreso nell'orario della sessione
        if ($oraInizioSessione <= $oraInizioPresentazione && $oraFineSessione >= $oraFinePresentazione  && $oraInizioPresentazione <= $oraFinePresentazione) {
            $result = 1;
        } else {
            $result = 0;
            return $result;
        }


        //controllo che l'orario della presentazione non si sovrapponga ad altre presentazioni per la stessa sessione 

        $selectOrariPresentazioni = "SELECT OraInizio AS OraInizioPresentazione ,OraFine AS OraFinePresentazione
                                     FROM PRESENTAZIONE 
                                     WHERE CodiceSessione ='$codiceSessione'";

        $orariPresentazioni = $pdo->query($selectOrariPresentazioni);

       
        foreach ($orariPresentazioni as $row) {

            if ($oraInizioPresentazione >= $row["OraFinePresentazione"]) {
                $result = 1;
            }
            else return 0;
            
        }
        return $result;
    }

    function numeroSequenza()
    {   
        $pdo = ConnDb::connToDb();

        $codiceSessione = $_POST["codiceSessione"];
        $numeroSequenzaSelect = "SELECT NumeroPresentazioni
                                 FROM SESSIONE 
                                 WHERE Codice ='$codiceSessione'";

        $risultatoQuery = $pdo->query($numeroSequenzaSelect);
        $numeroSequenza= $risultatoQuery->fetch(); 
        return $numeroSequenza["NumeroPresentazioni"]+1;
    }

    ?>

</body>

</html>