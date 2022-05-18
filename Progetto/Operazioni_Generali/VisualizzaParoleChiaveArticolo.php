<?php require_once "../ConnDb.php"; ?>
<!DOCTYPE html>
<html>

<head>
    <title>Parole Chiave</title>
    <link rel="stylesheet" href="../Operazioni_Generali/OperazioniStyle.css">
</head>

<body>
<?php
    session_Start();
    $nome = $_SESSION['username'];
    ?>

<p><br><a href="VisualizzazioneSessioniPresentazioni.php"> TORNA INDIETRO </a></p>

    <center>
        <h1> Parole Chiave <?php echo($_GET["submit"]); ?></h1>
    </center>



    <?php 
           $pdo = ConnDb::connToDb();

           $codiceArticolo = $_GET["submit"];
           $selectParoleChiave = "SELECT ParolaChiave 
                               FROM LISTA_PAROLE_CHIAVE
                               WHERE  CodiceArticolo='$codiceArticolo'";
       
           $listaParoleChiave = $pdo->query($selectParoleChiave);    
       
            
            //tabella parole chiave
            echo ("<table border='1' align=center>"
            . "<tr>"
            . "<th> PAROLA </th> "
            . "</tr>");

            foreach($listaParoleChiave as $row)
            {
                echo ("<tr>"
                . "<td>" . $row["ParolaChiave"] . "</td>"
                . "</tr>");
            }

            echo ("</table>");

    ?>
  
</body>
</html>
