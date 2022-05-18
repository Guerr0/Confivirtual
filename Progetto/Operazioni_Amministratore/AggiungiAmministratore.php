<?php require_once "../ConnDb.php"; ?>
<!DOCTYPE html>
<html>

<head>

    <title>Gestione Conferenze</title>
    <link rel="stylesheet" href="../Operazioni_Amministratore/OperazioneGeneraliStyle.css">
</head>


<body>
    <?php
    session_Start();

    // query per avere gli acronimi conferenza da inserire nel form Elimina conferenza
    $pdo = ConnDb::connToDb();
    try {
        $username=$_SESSION["username"];
        // Query SQL per leggere le conferenze che posso gestire come amministratore
        $selectAcronimi = "SELECT Acronimo 
                            FROM CONFERENZA,CREAZIONECONFERENZA
                           WHERE Acronimo=AcronimoConf AND AnnoEdizione=AnnoEdizConf AND UsernameAmministratore='$username'
                           AND CONFERENZA.Svolgimento='ATTIVA'";
        $arrayAcronimi = $pdo->query($selectAcronimi);
    } catch (PDOException $e) {
        echo ("[ERRORE] Query SQL non riuscita. Errore: " . $e->getMessage());
        exit();
    }

    ?>

    <p><br><a href="../Dashboard_Utenti/Amministratore.php"> TORNA ALLA DASHBOARD </a></p>

        <h1>GESTIONE AMMINISTRATORI</h1>

    <form action="" method="post" style="margin: 150px;">
        <h3> INSERISCI IL CO-AMMINISTRATORE</S> </h3>
        <input type="number" placeholder="Anno Edizione " name="annoEdizione" required>
        <span> Scegli Acronimo: </span>
        <select name="acronimo">
            <option>SCEGLI</option>
            <?php
            foreach ($arrayAcronimi as $row) {
                $acronimoConferenza = $row['Acronimo'];
                echo ("<option value='$acronimoConferenza'>$acronimoConferenza</option>");
            }
            ?>
        </select>
        <input type="text" placeholder="Username Amministratore " name="usernameAmministratore" maxlength="30" required>
        <input type="submit" name="submit" value="AGGIUNGI">
    </form>

    <?php

    
    if (isset($_POST["submit"])) 
    {
        if ($_POST["submit"] == "AGGIUNGI") {
         return aggiungiAmministratore();
        } 
    }

    // Metodo per aggiungere un amministratore ad una conferenza che gestisce l'amministratore loggato
    function aggiungiAmministratore()
    {
        $pdo = ConnDb::connToDb();
        try {
            $usernameAmministratore = $_POST["usernameAmministratore"];
            $acronimoConf=$_POST["acronimo"];
            $annoEdizioneConf=$_POST["annoEdizione"];
    
            $sql1 = 'CALL aggiungiAmministratore(:acronimoConf, :annoEdizioneConf, :usernameAmministratore)';
            $statement1 = $pdo->prepare($sql1);
            $statement1->bindParam(':acronimoConf', $acronimoConf, PDO::PARAM_STR);
            $statement1->bindParam(':annoEdizioneConf', $annoEdizioneConf, PDO::PARAM_STR);
            $statement1->bindParam(':usernameAmministratore', $usernameAmministratore, PDO::PARAM_STR);
            $statement1->execute();

            echo '<script language="javascript">';
            echo 'alert("Amministratore aggiunto con successo")';
            echo '</script>';

            header("Refresh:0");
        } catch (PDOException $e) {

            echo '<script language="javascript">';
            echo 'alert("ERRORE : \n 1 - L\'amministratore è già un gestore di quella conferenza \n 2 - L\'anno della conferenza non esiste \n 3 - L\'amministratore non esiste ")';
            echo '</script>';
           
            exit();
        }
    }
    ?>


</body>
</html>