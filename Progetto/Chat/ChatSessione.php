<?php require_once "../ConnessioneMongoDB.php"; ?>
<?php require_once "../ConnDb.php"; ?>
<!DOCTYPE html>
<html>

<head>
    <title>Chat</title>
    <link rel="stylesheet" href="ChatStyle2.css">
</head>

<body>
    <?php
    session_Start();
    $nome = $_SESSION["username"];
    $tipoUtente = $_SESSION["tipoUtente"];
    ?>
    <h1> CHAT  </h1>
    <p><br><a href="ListaChat.php"> TORNA INDIETRO </a></p>
    <form action="" method="post">
        <input type="text" placeholder="Testo" name="testo" maxlength="255" required>
        <input type="submit" name="submit" value="INVIA">
    </form>

    <?php

    mostraChat();

    if (isset($_POST["submit"])) {
        inserisciMessaggio();
    }

    ?>


    <?php
    
    function mostraChat()
    {
        try {
            $pdo = ConnDb::connToDb();
            $codiceSessione = $_GET["codiceSessione"];
            // select per leggere il contenuto della chat relativo alla SESSIONE
            $selectMessaggio = "SELECT DataInserimento,UsernameUtente,Testo
                              FROM MESSAGGIO 
                              WHERE CodiceSessione IN( SELECT Codice 
                                                      FROM SESSIONE 
                                                       WHERE IdProgramma IN (SELECT ID  
                                                                              FROM PROGRAMMAGIORNALIERO,REGISTRAZIONE
                                                                              WHERE (AcronimoConferenza=AcronimoConf AND AnnoEdizioneConferenza=AnnoEdiziConf))) AND CodiceSessione='$codiceSessione'";
            $visualizzazioneChat = $pdo->query($selectMessaggio);
            
            foreach ($visualizzazioneChat as $row) {
                echo ("<br>" .
                    "<tr>"
                    . "<td>" . $row["DataInserimento"] . "</td>" . "&nbsp"
                    . "<td>" . "[" . $row["UsernameUtente"] . "]" . "</td>" . "&nbsp" . " : "
                    . "<td>" . $row["Testo"] . "</td>"
                );
           
            }
            echo ("</table>");
            echo ("<br>");
        } catch (PDOException $e) {
            echo (" codice errore: " . $e->getMessage());
            exit();
        }
    }



    function inserisciMessaggio()
    {


        $pdo = ConnDb::connToDb();

        if(controlloSessioneTerminata())
        {

        try {
            $data = date('Y-m-d H:i:s', time());
            echo ($data);
            $nome = $_SESSION['username'];
            $testo = $_POST["testo"];
            $codSessione = $_GET["codiceSessione"];

            $sql1 = 'CALL inserimentoMessaggio(:data,:nome,:testo,:codSessione)';
            $statement1 = $pdo->prepare($sql1);

            $statement1->bindParam(':data', $data, PDO::PARAM_STR);
            $statement1->bindParam(':nome', $nome, PDO::PARAM_STR);
            $statement1->bindParam(':testo', $testo, PDO::PARAM_STR);
            $statement1->bindParam(':codSessione', $codSessione, PDO::PARAM_STR);

            $statement1->execute();

           // ConnessioneMongoDB::connMongoDB($nome, $sql1);

            header("Refresh:0");
        } catch (PDOException $e) {
            echo (" codice errore: " . $e->getMessage());
            exit();
        }
    }
    else{
        echo '<script language="javascript">';
        echo 'alert("SESSIONE TERMINATA: Impossibile inviare messaggi  ")';
        echo '</script>';
    }

    }


    //il metodo restituisce true se l'orario attuale è compreso tra l'orario di inizio e fine della sessione
    //          restituisce false altrimenti.
    function controlloSessioneTerminata()
    {
        try {
            $pdo = ConnDb::connToDb();
            $codiceSessione = $_GET["codiceSessione"];
            // select per leggere l'orario della sessione

            $selectOrariSessione = "SELECT OraInizio,OraFine
                                    FROM SESSIONE 
                                    WHERE Codice ='$codiceSessione'";

            $orariSessione = $pdo->query($selectOrariSessione);
            $rowOrariSessione = $orariSessione->fetch();

            $oraInizioSessione = $rowOrariSessione["OraInizio"];
            $oraFineSessione = $rowOrariSessione["OraFine"];

            $oraAttuale=date("H:i:s");
            $dataAttuale=date("Y-m-d");
            $dataSessione=$_GET["data"];

            if($oraAttuale>$oraInizioSessione && $oraAttuale<$oraFineSessione && $dataAttuale==$dataSessione)
            {
                return true;
            }

            else return false;

            

        } catch (PDOException $e) {
            echo (" codice errore: " . $e->getMessage());
            exit();
        }
    }
    ?>

</body>

</html>