<?php require_once "../ConnDb.php"; ?>
<!DOCTYPE html>
<html>

<head>
    <title>Speaker Presentatori</title>
    <link rel="stylesheet" href="../Operazioni_Generali/OperazioniStyle.css">
</head>

<body>
<?php
    session_Start();
    $nome = $_SESSION['username'];
    ?>

<p><br><a href="VisualizzazioneSessioniPresentazioni.php"> TORNA INDIETRO </a></p>

    <center>
        <h1> Visualizza gli Speaker del tutorial <?php echo($_GET["submit"]); ?></h1>
    </center>



    <?php 
           $pdo = ConnDb::connToDb();

           $codiceTutorial = $_GET["submit"];
           $selectSpeaker = "SELECT UsernameSpeaker
                               FROM PRESENTAZIONE_TUTORIAL    
                               WHERE CodiceTutorial='$codiceTutorial'";
       
           $listaSpeaker = $pdo->query($selectSpeaker);    
       
            
            //tabella parole chiave
            echo ("<table border='1' align=center>"
            . "<tr>"
            . "<th> USERNAME SPEAKER </th> "
            . "</tr>");

            foreach($listaSpeaker as $row)
            {
                echo ("<tr>"
                . "<td>" . $row["UsernameSpeaker"] . "</td>"
                . "</tr>");
            }

            echo ("</table>");

    ?>
  
</body>
</html>
