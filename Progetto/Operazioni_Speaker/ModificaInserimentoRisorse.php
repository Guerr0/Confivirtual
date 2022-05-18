<?php require_once "../ConnDb.php"; ?>
<?php require_once "../ConnessioneMongoDB.php"; ?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../Operazioni_Speaker/OperazioniSpeakerStyle.css">
    <title>INERISCI / MODIFICA INFORMAZIONI AGGIUNTIVE</title>
}

</head>

<body>
    <?php

    session_Start();
    $nome = $_SESSION['username'];
    ?>

    <p><br><a href="../Dashboard_Utenti/Speaker.php"> TORNA ALLA DASHBOARD </a></p>
    <h1> INSERIMENTO E MODIFICA DELLE RISORSE AGGIUNTIVE </h1>
    <?php
    $pdo = ConnDb::connToDb();
    //  option Tutorial tenuti da uno speaker
    $selectTutorial = "SELECT CodiceTutorial 
                        FROM PRESENTAZIONE_TUTORIAL
                        WHERE UsernameSpeaker='$nome'";
    $arrayTutorial = $pdo->query($selectTutorial);

    // TABELLA TUTORIAL TENUTE DALLO SPEKAER
    $selectTutorialInfo = "SELECT INFORMAZIONEAGGIUNTIVA.CodiceTutorial,Link,Descrizione
            FROM PRESENTAZIONE_TUTORIAL,INFORMAZIONEAGGIUNTIVA 
            WHERE INFORMAZIONEAGGIUNTIVA.CodiceTutorial=PRESENTAZIONE_TUTORIAL.CodiceTutorial AND PRESENTAZIONE_TUTORIAL.UsernameSpeaker='$nome'";
    $arrayTutorialInfo = $pdo->query($selectTutorialInfo);


    echo ("<table border='1' align=center>"
        . "<tr>"
        . "<th> CodiceTutorial</th>"
        . "<th> Link </th>"
        . "<th> Descrizione </th>"
        . "<th> ELIMINA </th>"
        . "</tr>");
    foreach ($arrayTutorialInfo as $row) {
        echo ("<tr>"
            . "<td>" . $row["CodiceTutorial"]  .  "</td>"
            . "<td>" . $row["Link"] . "</td>"
            . "<td>" . $row["Descrizione"] . "</td>"
            . "<td>  <form  action='' method='post'>
                     <input type='hidden' name='submit' value='$row[Link]'>
                     <input type='submit' value='ELIMINA'>
                     </form> </td>"
            . "</tr>");
    }
    echo ("</table>");




    ?>
    <!--Inserimento dati  -->
    <form class="form" action="" method="post" style="margin: 150px;">

        <h2>Aggiungi informazioni </h2>
        <span> Scegli il codice tutorial : </span>
        <select name="codiceTutorial">
            <option>SCEGLI</option>
            <?php
            foreach ($arrayTutorial as $row) {
                $nomeTutorial = $row['CodiceTutorial'];
                echo ("<option value='$nomeTutorial'>$nomeTutorial</option>");
            }
            ?>
        </select>
        <input type="text" placeholder="Link" name="link" maxlength="100" required>
        <input type="text" placeholder="Descrizione" name="descrizione" maxlength="255" required>
        <input type="submit" name="submit" value="INSERISCI">

    </form>

    <?php
    if (isset($_POST["submit"])) {
        if ($_POST["submit"] == "INSERISCI") {
            return inserimentoRisorsa();
        }

        else {
            eliminaRisorsa();
            
        }
    }

    // Funzione che inserisce le informazioni E AGGIORNA le risorse
    function inserimentoRisorsa()
    {
        $pdo = ConnDb::connToDb();
        try {

            $link = $_POST['link'];
            $descrizione = $_POST['descrizione'];
            $codiceTutorial = $_POST['codiceTutorial'];
            $nome = $_SESSION['username'];

            echo($link);
            echo($descrizione); 
            echo($codiceTutorial);
            echo($nome);

            $sql1 = "CALL inserisciRisorsaAggiuntiva(:link, :descrizione, :codiceTutorial, :nome)";
            $statement1 = $pdo->prepare($sql1);
            $statement1->bindParam(':link', $link, PDO::PARAM_STR);
            $statement1->bindParam(':descrizione', $descrizione, PDO::PARAM_STR);
            $statement1->bindParam(':codiceTutorial', $codiceTutorial, PDO::PARAM_STR);
            $statement1->bindParam(':nome', $nome, PDO::PARAM_STR);

            $statement1->execute();

            echo '<script language="javascript">';
            echo 'alert("INSERIMENTO AVVENUTO CON SUCCESSO")';
            echo '</script>';
            header("Refresh:0");

            ConnessioneMongoDB::connMongoDB($nome,$sql1);

        } catch (PDOException $e) {
            echo '<script language="javascript">';
            echo 'alert("ERRORE : impossibile aggiungere l\'informazione aggiuntiva \n 1 - Link già presente \n 2 - Scegli correttamente il tutorial")';
            echo '</script>';
            exit();
        }
    }

    function eliminaRisorsa()
    {
        $pdo = ConnDb::connToDb();
        try {

            $link = $_POST['submit'];

            $sql1 = "CALL eliminaRisorsaAggiuntiva(:link)";
            $statement1 = $pdo->prepare($sql1);
            $statement1->bindParam(':link', $link, PDO::PARAM_STR);

            $statement1->execute();

            echo '<script language="javascript">';
            echo 'alert("ELIMINAZIONE AVVENUTA CON SUCCESSO")';
            echo '</script>';
            header("Refresh:0");
            

        } catch (PDOException $e) {
            echo ("[ERRORE] Query SQL non riuscita. Errore: " . $e->getMessage());
            exit();
        }
    }

    ?>


</body>

</html>