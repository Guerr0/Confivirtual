<?php require_once "../ConnDb.php"; ?>
<?php require_once "../ConnessioneMongoDB.php"; ?>
<!DOCTYPE html>
<html>

<head>
    <title>Articolo-Parola Chiave</title>
    <link rel="stylesheet" href="../Operazioni_Amministratore/OperazioneGeneraliStyle.css"> 
</head>

<body>

<p><br><a href="../Dashboard_Utenti/Amministratore.php"> TORNA ALLA DASHBOARD </a></p>
    <h1>GESTIONE ARTICOLO-PAROLA CHIAVE</h1>
<?php

session_start();
$username=$_SESSION["username"];

$pdo = ConnDb::connToDb();

// query per avere i codici dei tutorial che gestisce l'amministratore
try{
$selectCodiciArticoli ="SELECT Codice
                        FROM PRESENTAZIONE,ARTICOLO
                        WHERE (CodiceSessione IN (SELECT Codice 
                                                  FROM SESSIONE 
						                          WHERE IdProgramma IN (SELECT ID  
                                                                         FROM PROGRAMMAGIORNALIERO,CREAZIONECONFERENZA
                                                                        WHERE (AcronimoConferenza=AcronimoConf AND AnnoEdizioneConferenza=AnnoEdizConf AND UsernameAmministratore='$username'))))
		AND Codice=CodiceArticolo;";

$arrayCodiciArticoli = $pdo->query($selectCodiciArticoli);

}catch (PDOException $e) {
    echo ("[ERRORE] Query SQL non riuscita. Errore: " . $e->getMessage());
    exit();
}


?>

 
    <form action="" method="post" style="margin: 150px;">
        <h3> ASSOCIA PAROLA CHIAVE ALL'ARTICOLO </h3>
        <span> Scegli Codice Articolo: </span>
        <select name="codiceArticolo">
            <option>SCEGLI</option>
            <?php
            foreach ($arrayCodiciArticoli as $row) {
                $codiceArticolo = $row['Codice'];
                echo ("<option value='$codiceArticolo'>$codiceArticolo</option>");
            }
            ?>
        </select>
        <input type="text" placeholder="Parola Chiave" name="parolaChiave" maxlength="20" required>
        <input type="submit" name="submit" value="ASSOCIA">
    </form>

    <form id="formAggiungiParola" action="" method="post" style="margin: 150px;">
        <h3> AGGIUNGI PAROLA CHIAVE </h3>
        <input type="text" placeholder="Parola" name="parola" maxlength="20" required>
        <input type="submit" name="submit" value="AGGIUNGI">
    </form>
    

    <?php
  

    if(isset($_POST["submit"])) { 

        if($_POST["submit"]=="ASSOCIA")
        {
            return associaParolaChiave();  
        }

        if($_POST["submit"]=="AGGIUNGI")
        {
            return aggiungiParola(); 
        }
     }  

    #---------------------------------------------------------#
    # AGGIUNGI PAROLA CHIAVE
     
    function aggiungiParola()
    {
        $pdo = ConnDb::connToDb();
        try {
            $parola = $_POST["parola"];

            $sql1 = 'CALL inserimentoParolaChiave(:parola)';
            $statement1 = $pdo->prepare($sql1);

            $statement1->bindParam(':parola', $parola, PDO::PARAM_STR);
            $statement1->execute();
            echo '<script language="javascript">';
            echo 'alert("Parola aggiunta con successo ")';
            echo '</script>';

            $username=$_SESSION["username"];
            ConnessioneMongoDB::connMongoDB($username,$sql1);
            
            header("Refresh:0");

        } catch (PDOException $e) {
            echo '<script language="javascript">';
            echo 'alert("ERRORE :  parola già esistente ")';
            echo '</script>';
            exit();
        }
    }

      #---------------------------------------------------------#
    # ASSOCIA PAROLACHIAVE-ARTICOLO

    function associaParolaChiave()
    {
        $pdo = ConnDb::connToDb();
        try {

            $codiceArticolo = $_POST["codiceArticolo"];
            $parolaChiave = $_POST["parolaChiave"];

            $sql1 = 'CALL articoloParolaChiaveAssoc(:codiceArticolo,:parolaChiave)';
            $statement1 = $pdo->prepare($sql1);

            $statement1->bindParam(':codiceArticolo', $codiceArticolo, PDO::PARAM_STR);
            $statement1->bindParam(':parolaChiave', $parolaChiave, PDO::PARAM_STR);
            $statement1->execute();
            echo '<script language="javascript">';
            echo 'alert("ASSOCIAZIONE AVVENUTA CON SUCCESSO")';
            echo '</script>';

            header("Refresh:0");
        } catch (PDOException $e) {
            echo '<script language="javascript">';
            echo 'alert("ERRORE : parola inesistente ")';
            echo '</script>';
            exit();
        }
    }
    ?>

    
</body>


</html>