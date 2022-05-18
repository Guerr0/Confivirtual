<?php require_once "../ConnDb.php"; ?>
<?php require_once "../ConnessioneMongoDB.php"; ?>
<!DOCTYPE html>
<html>

<head>

    <title>Gestione Conferenze</title>
    <link rel="stylesheet" href="../Operazioni_Amministratore/GestioneConferenza.css">
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
                           WHERE Acronimo=AcronimoConf AND AnnoEdizione=AnnoEdizConf AND UsernameAmministratore='$username'";
        $arrayAcronimi = $pdo->query($selectAcronimi);
    } catch (PDOException $e) {
        echo ("[ERRORE] Query SQL non riuscita. Errore: " . $e->getMessage());
        exit();
    }

    ?>

    <p><br><a href="../Dashboard_Utenti/Amministratore.php"> TORNA ALLA DASHBOARD </a></p>

    <center>
        <h1>GESTIONE CONFERENZE</h1>
    </center>
    <form class="form" action="" method="post" style="margin: 150px;">
        <h3> ELIMINA CONFERENZA </h3>
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
        <input type="submit" name="submit" value="ELIMINA">
    </form>


    <form class="form" action="" method="post" style="margin: 150px; " enctype="multipart/form-data">
        <h3> CREA CONFERENZA </h3>
        <input type="number" placeholder="Anno Edizione " name="annoEdizione" required>
        <input type="text" placeholder="Acronimo " name="acronimo" maxlength="20" required>
        <input type="text" placeholder="Nome " name="nome" maxlength="30" required>
        <span> Inserisci Logo : <input type="file" name="upload" id="upload" required></span>

        <input type="submit" name="submit" value="CREA">
    </form>

    <form class="form" action="" method="post" style="margin: 150px;">
        <h3> AGGIUNGI DATA AD UNA CONFERENZA </h3>
        <span> Scegli Acronimo: </span>
        <select name="acronimo">
            <option>SCEGLI</option>
            <?php
            $arrayAcronimi = $pdo->query($selectAcronimi);
            foreach ($arrayAcronimi as $row) {
                $acronimoConferenza = $row['Acronimo'];
                echo ("<option value='$acronimoConferenza'>$acronimoConferenza</option>");
            }
            ?>
        </select>
        <input type="number" placeholder="Anno Edizione " name="annoEdizione" required>
        <input type="date" placeholder="Data" name="data" required>
        <input type="submit" name="submit" value="AGGIUNGI DATA">
        <br>
        <span> Visualizza i programmi delle conferenze: <button id="button1" onclick="location.href='ProgrammiGiornalieri.php'">CALENDARIO </button> </span>

    </form>
    <?php

    # TABELLA DELLE CONFERENZE CHE POSSO GESTIRE COME AMMINISTRORE

    $pdo = ConnDb::connToDb();
    try {
        $username=$_SESSION["username"];
        // Query SQL per leggere le conferenze che posso gestire come amministratore
        $selectConferenzeGestibili = "SELECT Nome,Acronimo,AnnoEdizione,Svolgimento 
                                      FROM CONFERENZA,CREAZIONECONFERENZA
                                      WHERE Acronimo=AcronimoConf AND AnnoEdizione=AnnoEdizConf AND UsernameAmministratore='$username'";
        $arrayConferenzeGestibili = $pdo->query($selectConferenzeGestibili);
    } catch (PDOException $e) {
        echo ("[ERRORE] Query SQL non riuscita. Errore: " . $e->getMessage());
        exit();
    }
    
    echo ("<table border='1' align=center>"
        ."<caption> CONFERENZE GESTIBILI </caption>"
        . "<tr>"
        . "<th> Nome</th>"
        . "<th> Acronimo </th>"
        . "<th> Anno Edizione </th>"
        . "<th> Svolgimento</th>"
        . "<th> VISUALIZZA UTENTI REGISTRATI</th>"
       
        . "</tr>");
    
    foreach ($arrayConferenzeGestibili as $row) {
        echo ("<tr>"
            . "<td>" . $row["Nome"] . "</td>"
            . "<td>" . $row["Acronimo"] . "</td>"
            . "<td>" . $row["AnnoEdizione"] . "</td>"
            . "<td>" . $row["Svolgimento"] . "</td>"
            . "<td>  <form  action='TabellaUtentiRegistrati.php' method='GET'>
            <input type='hidden' name='acronimo' value='$row[Acronimo]' readOnly>
            <input type='hidden' name='annoEdizione' value='$row[AnnoEdizione]' readOnly>
            <input type='submit' name='submit' value='VISUALIZZA'>
            </form> </td>"
            . "</tr>");
    }
    echo ("</table>");
  
  

    if (isset($_POST["submit"])) {
        if ($_POST["submit"] == "ELIMINA") {
            return eliminaConferenza();
        } else if ($_POST["submit"] == "CREA") {
            return creaConferenza();
        } else if ($_POST["submit"] == "AGGIUNGI DATA") {
            return aggiungiData();
        }
    }

    #---------------------------------------------------------#

    # ELIMINA CONFERENZA

    function eliminaConferenza()
    {
        $pdo = ConnDb::connToDb();
        try {

            $annoEdizione = $_POST["annoEdizione"];
            $acronimo = $_POST["acronimo"];

            $sql1 = 'CALL eliminaConferenza(:annoEdizione,:acronimo)';
            $statement1 = $pdo->prepare($sql1);
            $statement1->bindParam(':annoEdizione', $annoEdizione, PDO::PARAM_STR);
            $statement1->bindParam(':acronimo', $acronimo, PDO::PARAM_STR);
            $statement1->execute();

            echo '<script language="javascript">';
            echo 'alert("Conferenza eliminata con successo")';
            echo '</script>';


            header("Refresh:0");

        } catch (PDOException $e) {
            echo '<script language="javascript">';
            echo 'alert("ERRORE : impossibile eliminare la conferenza  , controlla anno di edizione")';
            echo '</script>';

            exit();
        }
    }

    #---------------------------------------------------------#

    # CREA CONFERENZA 


    function creaConferenza()
    {
        $pdo = ConnDb::connToDb();
        try {


            $usernameAmministratore = $_SESSION["username"];
            $annoEdizione = $_POST["annoEdizione"];
            $acronimo = $_POST["acronimo"];
            $nome = $_POST["nome"];


            $uploadFile = $_FILES["upload"]["tmp_name"];
            $logo = (file_get_contents($uploadFile));



            $sql1 = 'CALL creaConferenza(:usernameAmministratore,:annoEdizione,:acronimo,:nome,:logo)';
            $statement1 = $pdo->prepare($sql1);

            $statement1->bindParam(':usernameAmministratore', $usernameAmministratore, PDO::PARAM_STR);
            $statement1->bindParam(':annoEdizione', $annoEdizione, PDO::PARAM_STR);
            $statement1->bindParam(':acronimo', $acronimo, PDO::PARAM_STR);
            $statement1->bindParam(':nome', $nome, PDO::PARAM_STR);
            $statement1->bindParam(':logo', $logo, PDO::PARAM_LOB);
            $statement1->execute();
            iscrizioneConferenza();

            echo '<script language="javascript">';
            echo 'alert("Creazione conferenza avvenuta con successo")';
            echo '</script>';

            header("Refresh:0");

            ConnessioneMongoDB::connMongoDB( $usernameAmministratore,$sql1);


        } catch (PDOException $e) {
            echo '<script language="javascript">';
            echo 'alert("ERRORE : la conferenza è già esistente")';
            echo '</script>';
            exit();
        }
    }
     #---------------------------------------------------------#
    #ISCIZIONE ALLA CONFERENZA CREATA 
    function iscrizioneConferenza()
    {
        $pdo = ConnDb::connToDb();
        try {
            $nome=$_SESSION['username'];
            $annoEdizione =$_POST['annoEdizione'];
            $acronimo = $_POST['acronimo'];

            $sql1 = 'CALL registrazioneConferenza(:nome, :acronimoConferenza, :annoEdizioneConf)';
            $statement1 = $pdo->prepare($sql1);
            $statement1->bindParam(':annoEdizioneConf', $annoEdizione, PDO::PARAM_STR);
            $statement1->bindParam(':acronimoConferenza', $acronimo, PDO::PARAM_STR);
            $statement1->bindParam(':nome',$nome, PDO::PARAM_STR);
            $statement1->execute();

        } catch (PDOException $e) {
            echo '<script language="javascript">';
            echo 'alert("ERRORE: impossibile iscriversi alla conferenza appena creata")';
            echo '</script>';
            exit();
        }
    }

    #---------------------------------------------------------#
    # AGGIUNGI DATA 

    function aggiungiData()
    {
        $pdo = ConnDb::connToDb();
        try {

            $annoEdizione = $_POST["annoEdizione"];
            $acronimo = $_POST["acronimo"];
            $data = $_POST["data"];


            $sql1 = 'CALL inserisciProgrammaGiornaliero(:data,:annoEdizione,:acronimo)';
            $statement1 = $pdo->prepare($sql1);

            $statement1->bindParam(':data', $data, PDO::PARAM_STR);
            $statement1->bindParam(':annoEdizione', $annoEdizione, PDO::PARAM_STR);
            $statement1->bindParam(':acronimo', $acronimo, PDO::PARAM_STR);
            $statement1->execute();

            echo '<script language="javascript">';
            echo 'alert("Data aggiunta con successo")';
            echo '</script>';

            header("Refresh:0");
            
            $nome=$_SESSION['username'];
            ConnessioneMongoDB::connMongoDB($nome,$sql1);

        } catch (PDOException $e) {
            echo '<script language="javascript">';
            echo 'alert("ERRORE : Impossibile aggiungere data")';
            echo '</script>';
            exit();
        }
    }



    ?>
</body>

</html>