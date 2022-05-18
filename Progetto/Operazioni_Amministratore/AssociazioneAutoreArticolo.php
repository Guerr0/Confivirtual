<?php require_once "../ConnDb.php"; ?>
<?php require_once "../ConnessioneMongoDB.php"; ?>
<!DOCTYPE html>
<html>

<head>

    <title>AssociazionePresenterArticolo</title>
    <link rel="stylesheet" href="../Operazioni_Amministratore/OperazioneGeneraliStyle.css">
</head>

<body>

<p><br><a href="../Dashboard_Utenti/Amministratore.php"> TORNA ALLA DASHBOARD </a></p>
    <h1>ASSOCIZIONE ARTICOLO PRESENTER</h1>
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
        <h3> ASSOCIA UN AUTORE AD UN ARTICOLO </h3>
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
        <input type="text" placeholder="Nome Autore" name="nomeAutore" maxlength="30" required>
        <input type="text" placeholder="Cognome Autore" name="cognomeAutore" maxlength="30" required>
        <input type="submit" name="submit" value="ASSOCIA">
    </form>

    <form action="" method="post" style="margin: 150px;">
        <h3> AGGIUNGI AUTORE </h3>
        <input type="text" placeholder="Nome" name="nome" maxlength="30" required>
        <input type="text" placeholder="Cognome" name="cognome" maxlength="30" required>
        <input type="submit" name="submit" value="AGGIUNGI">
    </form>
    

    <?php
  
    #---------------------------------------------------------#
    # TABELLA AUTORI 
       
    $pdo = ConnDb::connToDb();
    try {
        // Query SQL per leggere le sessioni delle conferenze che posso gestire
        $selectAutori = "SELECT * FROM AUTORE";
        $arrayAutori = $pdo->query($selectAutori);
    } catch (PDOException $e) {
        echo ("[ERRORE] Query SQL non riuscita. Errore: " . $e->getMessage());
        exit();
    }

    echo ("<table border='1'>"
        ."<caption> LISTA AUTORI </caption>"
        . "<tr>"
        . "<th> Nome </th> "
        . "<th> Cognome </th>"
        . "</tr>");

    foreach ($arrayAutori as $row) {
        echo ("<tr>"
            . "<td>" . $row["Nome"] . "</td>"
            . "<td>" . $row["Cognome"] . "</td>"
            . "</tr>");
    }
    echo ("</table>");


        
    if(isset($_POST["submit"])) { 

        if($_POST["submit"]=="ASSOCIA")
        {
            return associaAutore();  
        }

        if($_POST["submit"]=="AGGIUNGI")
        {
            return aggiungiAutore(); 
        }
     }  

    #---------------------------------------------------------#
    # AGGIUNGI AUTORE
     
    function aggiungiAutore()
    {
        $pdo = ConnDb::connToDb();
        try {
            $nome = $_POST["nome"];
            $cognome=$_POST["cognome"];

            $sql1 = 'CALL inserimentoAutore(:nome,:cognome)';
            $statement1 = $pdo->prepare($sql1);

            $statement1->bindParam(':nome', $nome, PDO::PARAM_STR);
            $statement1->bindParam(':cognome', $cognome, PDO::PARAM_STR);
            $statement1->execute();
            echo '<script language="javascript">';
            echo 'alert("AUTORE AGGIUNTO CON SUCCESSO")';
            echo '</script>';

            $username=$_SESSION["username"];
            ConnessioneMongoDB::connMongoDB($username,$sql1);

            header("Refresh:0");
        } catch (PDOException $e) {
            echo '<script language="javascript">';
            echo 'alert("ERRORE : l\'autore è già esistente")';
            echo '</script>';
            
            exit();
        }
    }

      #---------------------------------------------------------#
    # ASSOCIA AUTORE-ARTICOLO

    function associaAutore()
    {
        $pdo = ConnDb::connToDb();
        try {

            $codiceArticolo = $_POST["codiceArticolo"];
            $nomeAutore = $_POST["nomeAutore"];
            $cognomeAutore=$_POST["cognomeAutore"];

            $sql1 = 'CALL autoreArticoloAssoc(:nomeAutore,:cognomeAutore, :codiceArticolo)';
            $statement1 = $pdo->prepare($sql1);

            $statement1->bindParam(':nomeAutore', $nomeAutore, PDO::PARAM_STR);
            $statement1->bindParam(':cognomeAutore', $cognomeAutore, PDO::PARAM_STR);
            $statement1->bindParam(':codiceArticolo', $codiceArticolo, PDO::PARAM_STR);
            $statement1->execute();
            echo '<script language="javascript">';
            echo 'alert("ASSOCIAZIONE AVVENUTA CON SUCCESSO")';
            echo '</script>';

            header("Refresh:0");
        } catch (PDOException $e) {
            echo '<script language="javascript">';
            echo 'alert("ERRORE : autore inesistente ")';
            echo '</script>';

            exit();
        }
    }
    ?>

    
</body>


</html>