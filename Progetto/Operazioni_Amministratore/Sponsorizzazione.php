<?php require_once "../ConnDb.php"; ?>
<?php require_once "../ConnessioneMongoDB.php"; ?>
<!DOCTYPE html>
<html>

<head>
    <title>Sposorizzazione</title>
    <link rel="stylesheet" href="../Operazioni_Amministratore/OperazioneGeneraliStyle.css">
</head>

<body>
<?php
    session_Start();
?>


<p><br><a href="../Dashboard_Utenti/Amministratore.php"> TORNA ALLA DASHBOARD </a></p>

    <center>
        <h1>GESTIONE SPONSOR </h1>
    </center>
    <form id="form" action="" method="post" style="margin: 150px; " enctype="multipart/form-data">
        <h3> INSERIMENTO SPONSOR </h3>
        <input type="text" placeholder="Nome " name="nome" maxlength="30" required>
        <hr> UPLOAD LOGO </hr>
        <p> Aggiungoi il logo </p>
        <table>
            <tr>
                <td> <b> </b> Seleziona file: </b></td>
                <td><input type='file' name="upload" id="upload" required>
                <td>
            </tr>
        </table>
        <input type="submit" name="submit" value="CREA">
    </form>



    <?php
    $pdo = ConnDb::connToDb();
    $username=$_SESSION["username"];

    try {
    //select per avere i nomi degli sponsor
    $selectSponsor = "SELECT * FROM SPONSOR";
    $listaSponsor = $pdo->query($selectSponsor);

    //select per avere gli acronimi delle conferenze che gestisco
    $selectConferenze = "SELECT Acronimo 
                            FROM CONFERENZA,CREAZIONECONFERENZA
                           WHERE Acronimo=AcronimoConf AND AnnoEdizione=AnnoEdizConf AND UsernameAmministratore='$username' AND Svolgimento='ATTIVA'";
    $listaAcronimi = $pdo->query($selectConferenze);
    }catch (PDOException $e) {
        echo ("[ERRORE] Query SQL non riuscita. Errore: " . $e->getMessage());
        exit();
    }

    ?>
    <form id="form1" action="" method="post" style="margin: 150px; " enctype="multipart/form-data">
    <h3> AGGIUNGI SPONSORIZZAZIONE </h3>
        <span> Scegli sponsor: </span>
        <select name="sponsor">
            <option>SCEGLI</option>
            <?php
            foreach ($listaSponsor as $row) {
                $nomeSponsor = $row['Nome'];
                echo ("<option value='$nomeSponsor'>$nomeSponsor</option>");
            }
            ?>
        </select>
        <input type="number" step="0.01" placeholder="Importo Sponsorizzazione" name="importoSponsorizzazione" required> 
        <input type="number" placeholder="Anno Edizione Conferenza" name="annoEdizioneConf" required> 
        <span> Scegli acronimo conferenza: </span> 
        <select name="acronimiConferenze">
            <option>SCEGLI</option>
            <?php
            foreach ($listaAcronimi as $row) {
                $acronimo = $row['Acronimo'];
                echo ("<option value='$acronimo'>$acronimo</option>");
            }
            ?>
        </select>
        

        <input type="submit" name="submit" value="INSERISCI">
    </form>
    <?php
    $pdo = ConnDb::connToDb();
    try {
        // Query SQL per leggere le conferenze 
        $selectPresentazioni = "SELECT Nome
                                         FROM SPONSOR";
        $arrayPresentazioni = $pdo->query($selectPresentazioni);
    } catch (PDOException $e) {
        echo ("[ERRORE] Query SQL non riuscita. Errore: " . $e->getMessage());
        exit();
    }


    
    if (isset($_POST["submit"])) {
        if ($_POST["submit"] == "INSERISCI") {
            return inserisciSponsorizzazione();
        } else if ($_POST["submit"] == "CREA") {
            return creaSponsor();
        }
    }


    // Creazione di uno sposor
    function creaSponsor()
    {
        $pdo = ConnDb::connToDb();
        try {
            $nome = $_POST["nome"];
            $uploadFile = $_FILES['upload']['tmp_name'];
            $foto = (file_get_contents($uploadFile));

            $sql1 = "CALL insertSponsor(:nome, :upload)";
            $statement1 = $pdo->prepare($sql1);
            $statement1->bindParam(':nome', $nome, PDO::PARAM_STR);
            $statement1->bindParam(':upload', $foto, PDO::PARAM_LOB);
            $statement1->execute();

            echo '<script language="javascript">';
            echo 'alert("Sponsor aggiunto con successo")';
            echo '</script>';
            header("Refresh:0");

            ConnessioneMongoDB::connMongoDB($nome,$sql1);
            
        } catch (PDOException $e) {
            echo '<script language="javascript">';
            echo 'alert("ERRORE: Sponsor già esistente")';
            echo '</script>';
            exit();
        }
    }

    // inserisci sposorizzazione
    function inserisciSponsorizzazione()
    {
        $pdo = ConnDb::connToDb();
        try {

            $nomeSponsor = $_POST["sponsor"];
            $annoEdizioneConf = $_POST["annoEdizioneConf"];
            $acronimoConf = $_POST["acronimiConferenze"];
            $importoSponsorizzazione = $_POST["importoSponsorizzazione"];

            $sql1 = "CALL inserimentoSponsorizzazione(:annoEdizioneConf, :acronimoConf,:nomeSponsor,:importoSponsorizzazione)";
            $statement1 = $pdo->prepare($sql1);
            $statement1->bindParam(':annoEdizioneConf', $annoEdizioneConf, PDO::PARAM_STR);
            $statement1->bindParam(':acronimoConf', $acronimoConf, PDO::PARAM_STR);
            $statement1->bindParam(':nomeSponsor', $nomeSponsor, PDO::PARAM_STR);
            $statement1->bindParam(':importoSponsorizzazione', $importoSponsorizzazione, PDO::PARAM_STR);
            $statement1->execute();

            echo '<script language="javascript">';
            echo 'alert("Sponsorizzazione aggiunta con successo")';
            echo '</script>';
            header("Refresh:0");
        } catch (PDOException $e) {
            echo '<script language="javascript">';
            echo 'alert("ERRORE: Impossibile aggiungere la sponsorizzazione \n 1 - Scegli correttamente lo sponsor \n 2 - Conferenza inesistente ")';
            echo '</script>';
            exit();
        }
    }



    ?>

</body>

</html>