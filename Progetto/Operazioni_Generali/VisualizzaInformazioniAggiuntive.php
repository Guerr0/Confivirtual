<?php require_once "../ConnDb.php"; ?>

<!DOCTYPE html>
<html>

<head>
    <title>Informazioni Aggiuntive </title>
    <link rel="stylesheet" href="../Operazioni_Generali/OperazioniStyle.css">
</head>

<body>
<?php
    session_Start();
    $nome = $_SESSION['username'];
    ?>

<p><br><a href="VisualizzazioneSessioniPresentazioni.php"> TORNA INDIETRO </a></p>

    <center>
        <h1> Visualizza Informazioni aggiuntive del tutorial <?php echo($_GET["submit"]); ?></h1>
    </center>



    <?php 
           $pdo = ConnDb::connToDb();

           $codiceTutorial = $_GET["submit"];
           $selectInformazioniAggiuntive = "SELECT * 
                               FROM  INFORMAZIONEAGGIUNTIVA
                               WHERE  CodiceTutorial='$codiceTutorial'";
       
           $listaInformazioniAggiuntive = $pdo->query($selectInformazioniAggiuntive);    
       
            
            //tabella parole chiave
            echo ("<table border='1' align=center>"
            . "<tr>"
            . "<th> Link </th> "
            . "<th> Descrizione </th> "
            . "<th> Aggiunta da   </th> "
            . "</tr>");

            foreach($listaInformazioniAggiuntive as $row)
            {
                echo ("<tr>"
                . "<td>" . $row["Link"] . "</td>"
                . "<td>" . $row["Descrizione"] . "</td>"
                . "<td>" . $row["UsernameSpeaker"] . "</td>"
                . "</tr>");
            }

            echo ("</table>");

    ?>
  
</body>
</html>
