<?php require_once "../ConnDb.php"; ?>
<!DOCTYPE html>
<html>

<head>

    <title>AssociazionePresenterArticolo</title>
    <link rel="stylesheet" href="../Operazioni_Amministratore/OperazioneGeneraliStyle.css">
</head>

<body>

<p><br><a href="../Dashboard_Utenti/Amministratore.php"> TORNA ALLA DASHBOARD </a></p>
<h1>ASSOCIAZIONE ARTICOLO PRESENTER</h1>
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



    <center>
        <form action="" method="post" style="margin: 150px;">
        <h3> ASSOCIA UN PRESENTER AD UN ARTICOLO </h3>
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
        <input type="text" placeholder="Username Presenter" name="usernamePresenter" maxlength="30" required>
        <input type="submit" name="submit" value="AGGIUNGI">
    </form>
    </center>
    

    <?php
  

    //TABELLA PRESENTER REGISTRATI ALLE CONFERNZE DELL'AMMINISTRATORE
    $pdo = ConnDb::connToDb();

    //query per avere conferenze che può gestire l'amminnistratore

    try {
        $username=$_SESSION["username"];
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
      . "<caption> TABELLA PRESENTER REGISTRATI ALLE MIE CONFERENZE </caption>"
      . "<tr>"
      . "<th> Username </th> "
      . "<th> Nome </th>"
      . "<th> Cognome </th>"
      . "<th> Acronimo Conferenza </th>"
      . "<th> Anno Edizione </th>"

      . "</tr>");

    //query per avere gli utenti presenter registrati ad una conferenza dell'amministratore
    
    foreach($arrayConferenzeGestibili as $row)
    {
    $acronimoConfCorrente = $row["Acronimo"];
    $annoEdizConfCorrente = $row["AnnoEdizione"];
    $selectUtenti = "SELECT Username,Nome,Cognome
                     FROM UTENTE,REGISTRAZIONE,PRESENTER 
                     WHERE  UTENTE.Username=REGISTRAZIONE.UsernameUtente  AND PRESENTER.UsernamePresenter=REGISTRAZIONE.UsernameUtente AND AcronimoConf='$acronimoConfCorrente' AND AnnoEdiziConf='$annoEdizConfCorrente'";
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



    
    if(isset($_POST["submit"])) { 
        return aggiungiPresenter();   
     }  


    #---------------------------------------------------------#
    # AGGIUNGI PRESENTER

    function aggiungiPresenter()
    {
        $pdo = ConnDb::connToDb();
        try {

            $codiceArticolo = $_POST["codiceArticolo"];
            $usernamePresenter = $_POST["usernamePresenter"];

            if(controlloPresenter()==1){

            $sql1 = 'CALL presenterArticoloAssoc(:codiceArticolo,:usernamePresenter)';
            $statement1 = $pdo->prepare($sql1);

            $statement1->bindParam(':codiceArticolo', $codiceArticolo, PDO::PARAM_STR);
            $statement1->bindParam(':usernamePresenter', $usernamePresenter, PDO::PARAM_STR);
            $statement1->execute();
            echo '<script language="javascript">';
            echo 'alert("PRESENTER AGGIUNTO CON SUCCESSO")';
            echo '</script>';
            }
            else  {  
            echo '<script language="javascript">';
            echo 'alert("ERRORE: Impossibile associare questo presenter \n 1 - Il presenter non è registrato alla conferenza dell\'articolo \n 2 - Il presenter non è l\'autore dell\'articolo \n 3 - Il presenter non esiste")';
            echo '</script>';
            }

            header("Refresh:0");
        } catch (PDOException $e) {
            echo '<script language="javascript">';
            echo 'alert("ERRORE: Impossibile associare questo presenter \n 1 - Il presenter non è registrato alla conferenza dell\'articolo \n 2 - Il presenter non è l\'autore dell\'articolo \n 3 - Il presenter non esiste")';
            echo '</script>';
            exit();
        }
    }


    //CONTROLLO se il presenter può essere associato a quell'articolo , ovvero se è registrato alla conferenza dell'amministratore e sia un autore dell'articolo
    // 1 il presenter può essere aggiunto , 0 altrimenti.

    function controlloPresenter()
    {
        $pdo = ConnDb::connToDb();
        try {

            $codiceArticolo = $_POST["codiceArticolo"];
            $usernamePresenter = $_POST["usernamePresenter"];
            

            //controllo se il presenter è registrato alla conferenza
            $queryControllo1 = "SELECT Count(*) AS PRESENTE
            FROM REGISTRAZIONE, PROGRAMMAGIORNALIERO
            WHERE AnnoEdizioneConferenza=AnnoEdiziConf AND AcronimoConferenza=AcronimoConf AND UsernameUtente='$usernamePresenter' AND ID IN(SELECT IdProgramma 
                                                                      FROM SESSIONE
                                                                      WHERE Codice IN (SELECT CodiceSessione 
                                                                                        FROM PRESENTAZIONE 
                                                                                        WHERE Codice IN (SELECT CodiceArticolo FROM ARTICOLO WHERE CodiceArticolo='$codiceArticolo')))";
            $risultatoQuery1 = $pdo->query($queryControllo1);
            $row1=$risultatoQuery1->fetch();
            
            //controllo che il presenter sia un autore dell'articolo
            $queryControllo2 = "SELECT Count(*) AS AUTORE
                                FROM UTENTE,LISTA_PUBBLICAZIONI
                                WHERE UTENTE.Nome=LISTA_PUBBLICAZIONI.NomeAutore AND UTENTE.Cognome=LISTA_PUBBLICAZIONI.CognomeAutore AND Utente.Username='$usernamePresenter' 
                                        AND LISTA_PUBBLICAZIONI.CodiceArticolo='$codiceArticolo'";
            $risultatoQuery2 = $pdo->query($queryControllo2);
            $row2=$risultatoQuery2->fetch();


            if($row1["PRESENTE"==1] && $row2["AUTORE"]==1)
            {
                $risultato=1;
            }
            else $risultato=0;

            return $risultato;
            



        } catch (PDOException $e) {
            echo (" codice errore: " . $e->getMessage());
            exit();
        }
    }
    
    ?>
    
</body>


</html>