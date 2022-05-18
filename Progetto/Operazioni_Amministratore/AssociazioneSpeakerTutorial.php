<?php require_once "../ConnDb.php"; ?>
<!DOCTYPE html>
<html>

<head>

    <title>AssociazioneSpeakerTutorial</title>
    <link rel="stylesheet" href="../Operazioni_Amministratore/OperazioneGeneraliStyle.css">
    
</head>

<body>

    <?php

    session_start();
    $username = $_SESSION["username"];

    $pdo = ConnDb::connToDb();

    // query per avere i codici dei tutorial che gestisce l'amministratore
    try {
        $selectCodiciTutorial = " SELECT Codice
FROM PRESENTAZIONE,TUTORIAL
WHERE (CodiceSessione IN (SELECT Codice 
                          FROM SESSIONE 
						  WHERE IdProgramma IN (SELECT ID  
                                                  FROM PROGRAMMAGIORNALIERO,CREAZIONECONFERENZA
                                                  WHERE (AcronimoConferenza=AcronimoConf AND AnnoEdizioneConferenza=AnnoEdizConf AND UsernameAmministratore='$username'))))
		AND Codice=CodiceTutorial";

        $arrayCodiciTutorial = $pdo->query($selectCodiciTutorial);
    } catch (PDOException $e) {
        echo ("[ERRORE] Query SQL non riuscita. Errore: " . $e->getMessage());
        exit();
    }


    ?>

    <p><br><a href="../Dashboard_Utenti/Amministratore.php"> TORNA ALLA DASHBOARD </a></p>
    <h1>ASSOCIAZIONE SPEAKER-TUTORIAL</h1>

    <center>
        <form action="" method="post" style="margin: 150px;">
            <h3> ASSOCIA UNO SPEAKER AD UN TUTORIAL </h3>
            <span> Scegli Codice Tutorial: </span>
            <select name="codiceTutorial">
                <option>SCEGLI</option>
                <?php
                foreach ($arrayCodiciTutorial as $row) {
                    $codiceTutorial = $row['Codice'];
                    echo ("<option value='$codiceTutorial'>$codiceTutorial</option>");
                }
                ?>
            </select>
            <input type="text" placeholder="Username Speaker" name="usernameSpeaker" maxlength="30" required>
            <input type="submit" name="submit" value="AGGIUNGI">
        </form>
    </center>


    <?php


    //TABELLA SPEAKER REGISTRATI ALLE CONFERENZE DELL'AMMINISTRATORE
    $pdo = ConnDb::connToDb();

    //query per avere conferenze che può gestire l'amministratore

    try {
        $username = $_SESSION["username"];
        // Query SQL per leggere le conferenze che posso gestire come amministratore
        $selectConferenzeGestibili = "SELECT Acronimo,AnnoEdizione
                                      FROM CONFERENZA,CREAZIONECONFERENZA
                                      WHERE Acronimo=AcronimoConf AND AnnoEdizione=AnnoEdizConf AND UsernameAmministratore='$username'";
        $arrayConferenzeGestibili = $pdo->query($selectConferenzeGestibili);
    } catch (PDOException $e) {
        echo ("[ERRORE] Query SQL non riuscita. Errore: " . $e->getMessage());
        exit();
    }





    echo ("<table border='1' align=left>"
        . "<caption> TABELLA SPEAKER REGISTRATI ALLE MIE CONFERENZE </caption>"
        . "<tr>"
        . "<th> Username </th> "
        . "<th> Nome </th>"
        . "<th> Cognome </th>"
        . "<th> Acronimo Conferenza </th>"
        . "<th> Anno Edizione </th>"

        . "</tr>");

    //query per avere gli utenti presenter registrati ad una conferenza dell'amministratore

    foreach ($arrayConferenzeGestibili as $row) {
        $acronimoConfCorrente = $row["Acronimo"];
        $annoEdizConfCorrente = $row["AnnoEdizione"];
        $selectUtenti = "SELECT Username,Nome,Cognome
                     FROM UTENTE,REGISTRAZIONE,SPEAKER 
                     WHERE  UTENTE.Username=REGISTRAZIONE.UsernameUtente  AND SPEAKER.UsernameSpeaker=REGISTRAZIONE.UsernameUtente AND AcronimoConf='$acronimoConfCorrente' AND AnnoEdiziConf='$annoEdizConfCorrente'";
        $listaUtenti = $pdo->query($selectUtenti);

        foreach ($listaUtenti as $row) {
            echo ("<tr>"
                . "<td>" . $row["Username"] . "</td>"
                . "<td>" . $row["Nome"] . "</td>"
                . "<td>" . $row["Cognome"] . "</td>"
                . "<td>" . $acronimoConfCorrente . "</td>"
                . "<td>" . $annoEdizConfCorrente . "</td>"
                . "</tr>");
        }
    }

    echo ("</table>");




    #---------------------------------------------------------#
    # AGGIUNGI SPEAKER

    if (isset($_POST["submit"])) {
        return aggiungiSpeaker();
    }

    function aggiungiSpeaker()
    {
        $pdo = ConnDb::connToDb();
        try {

            $codiceTutorial = $_POST["codiceTutorial"];
            $usernameSpeaker = $_POST["usernameSpeaker"];

            if (controlloSpeaker()==1) {
                $sql1 = 'CALL speakerTutorialAssoc(:codiceTutorial,:usernameSpeaker)';
                $statement1 = $pdo->prepare($sql1);

                $statement1->bindParam(':codiceTutorial', $codiceTutorial, PDO::PARAM_STR);
                $statement1->bindParam(':usernameSpeaker', $usernameSpeaker, PDO::PARAM_STR);
                $statement1->execute();
                echo '<script language="javascript">';
                echo 'alert("SPEAKER AGGIUNTO CON SUCCESSO")';
                echo '</script>';
            } else {
                echo '<script language="javascript">';
                echo 'alert("ERRORE: Impossibile associare questo speaker \n 1 - Lo speaker non esiste \n 2 - Lo speaker non è registrato alla conferenza del tutorial ")';
                echo '</script>';
            }

            header("Refresh:0");
        } catch (PDOException $e) {
            echo '<script language="javascript">';
            echo 'alert("ERRORE : lo speaker è già stato associato a questo tutorial ")';
            echo '</script>';
            exit();
        }
    }



    //CONTROLLO se lo speaker può essere associato a quel tutorial , ovvero se è registrato alla conferenza dell'amministratore
    // Return : 1 se lo speaker può essere aggiunto , 0 altrimenti.

    function controlloSpeaker()
    {
        $pdo = ConnDb::connToDb();
        try {

            $codiceTutorial = $_POST["codiceTutorial"];
            $usernameSpeaker = $_POST["usernameSpeaker"];



            $queryControllo = "SELECT Count(*) AS PRESENTE
            FROM REGISTRAZIONE, PROGRAMMAGIORNALIERO
            WHERE AnnoEdizioneConferenza=AnnoEdiziConf AND AcronimoConferenza=AcronimoConf AND UsernameUtente='$usernameSpeaker' AND ID IN(SELECT IdProgramma 
                                                                      FROM SESSIONE
                                                                      WHERE Codice IN (SELECT CodiceSessione 
                                                                                        FROM PRESENTAZIONE 
                                                                                        WHERE Codice IN (SELECT CodiceTutorial FROM TUTORIAL WHERE CodiceTutorial='$codiceTutorial')))";
            $risultatoQuery = $pdo->query($queryControllo);
            $row = $risultatoQuery->fetch();

            if ($row["PRESENTE" == 1]) {
                $risultato = 1;
            } else $risultato = 0;

            return $risultato;

        } catch (PDOException $e) {
            echo (" codice errore: " . $e->getMessage());
            exit();
        }
    }
    ?>




</body>


</html>