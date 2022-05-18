<?php require_once "../ConnDb.php"; ?>
<?php require_once "../ConnessioneMongoDB.php"; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Inserimento valutazione presentazioni </title>
    <link rel="stylesheet" href="../Operazioni_Amministratore/OperazioneGeneraliStyle.css">
</head>
<body>
<?php
    session_Start();
    $nome=$_SESSION['username'];

    $pdo = ConnDb::connToDb();

    //select per avere gli le presentazioni delle conferenze che gestisci
    $selectPresentazioni = "SELECT Codice 
                            FROM PRESENTAZIONE
                            WHERE (CodiceSessione IN (SELECT Codice 
                                                      FROM SESSIONE 
                                                      WHERE IdProgramma IN (SELECT ID  
                                                                            FROM PROGRAMMAGIORNALIERO,CREAZIONECONFERENZA
                                                                            WHERE (AnnoEdizioneConferenza=AnnoEdizConf AND AcronimoConferenza=AcronimoConf AND UsernameAmministratore='$nome'))))
                            AND Codice NOT IN (SELECT CodicePresentazione 
                                               FROM VALUTAZIONE
                                               WHERE UsernameAmministratore='$nome')";
    $listaPresentazioni = $pdo->query($selectPresentazioni);
    ?>

<p><br><a href="../Dashboard_Utenti/Amministratore.php"> TORNA ALLA DASHBOARD </a></p>

    <center>
        <h1>INSERIMENTO VALUTAZIONE PRESENTAZIONE  </h1>
    </center>
   
    <form id="form1" action="" method="post" style="margin: 150px;">
        <h3> VALUTAZIONE PRESENTAZIONE </h3>
        <span> Scegli presentazione: </span>
        <select name="codicePresentazione">
            <option>SCEGLI</option>
            <?php
            foreach ($listaPresentazioni as $row) {
                $codicePresentazione = $row['Codice'];
                echo ("<option value='$codicePresentazione'>$codicePresentazione</option>");
            }
            ?>
        </select>
        <input type="number" placeholder="Voto " name="Voto" max ="10" min="0" required>
        <input type="text" placeholder="Note " name="Note" maxlength="50" required>
        <input type="submit" name="submit" value="INSERISCI">
    </form>
    <?php
    $pdo = ConnDb::connToDb();
    try {
    // Query SQL per leggere le presentazioni
        $selectPresentazioni = "SELECT UsernameAmministratore,CodicePresentazione,Voto,Note
                            FROM VALUTAZIONE
                            WHERE ( UsernameAmministratore= '$nome')";
        $arrayPresentazioni = $pdo->query($selectPresentazioni);
    } catch (PDOException $e) {
         echo ("[ERRORE] Query SQL non riuscita. Errore: " . $e->getMessage());
         exit();
    }

    echo ("<table border='1'>"
    . "<tr>"
    . "<th> Username Amministratore </th> "
    . "<th> Codice Presentazione </th>"
    . "<th> Voto </th>"
    . "<th> Note </th>"
    . "</tr>");

    foreach ($arrayPresentazioni as $row) {
    echo ("<tr>"
        . "<td>" . $row["UsernameAmministratore"] . "</td>"
        . "<td>" . $row["CodicePresentazione"] . "</td>"
        . "<td>" . $row["Voto"] . "</td>"
        . "<td>" . $row["Note"] . "</td>"
        . "</tr>");
    }
    echo ("</table>");


    if (isset($_POST["submit"])) 
    {
        if ($_POST["submit"] == "INSERISCI") {
         return InserisciPresentazionePreferite();
        } 
    }


    //Funzione che inserisce le presentazioni
    function InserisciPresentazionePreferite(){
        $pdo = ConnDb::connToDb();
        try {
            $codicePresentazione = $_POST["codicePresentazione"];
            $voto=$_POST["Voto"];
            $note=$_POST["Note"];
            $nome=$_SESSION['username'];
            $sql1 = 'CALL inserisciValutazione(:note, :voto, :nome, :codicePresentazione )';
            $statement1 = $pdo->prepare($sql1);
            $statement1->bindParam(':note', $note, PDO::PARAM_STR);
            $statement1->bindParam(':voto', $voto, PDO::PARAM_STR);
            $statement1->bindParam(':nome', $nome, PDO::PARAM_STR);
            $statement1->bindParam(':codicePresentazione', $codicePresentazione, PDO::PARAM_STR);
            $statement1->execute();

            ConnessioneMongoDB::connMongoDB($nome,$sql1);

            header("Refresh:0");

        } catch (PDOException $e) {
            echo '<script language="javascript">';
            echo 'alert("ERRORE: Scegli correttamente una presentazione")';
            echo '</script>';

            exit();
        }
    }
?>

</body>

</html>