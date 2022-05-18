<?php require_once "../ConnDb.php"; ?>
<!DOCTYPE html>
<html>

<head>
    <title> Autori </title>
    <link rel="stylesheet" href="../Operazioni_Generali/OperazioniStyle.css">
</head>

<body>
<?php
    session_Start();
    $nome = $_SESSION['username'];
    ?>

<p><br><a href="VisualizzazioneSessioniPresentazioni.php"> TORNA INDIETRO </a></p>

    <center>
        <h1> Autori dell'articolo <?php echo($_GET["submit"]); ?></h1>
    </center>



    <?php 
           $pdo = ConnDb::connToDb();

           $codiceArticolo = $_GET["submit"];
           $selectAutori = "SELECT NomeAutore , CognomeAutore 
                               FROM LISTA_PUBBLICAZIONI
                               WHERE  CodiceArticolo='$codiceArticolo'";
       
           $listaAutori = $pdo->query($selectAutori);    
       
            
            //tabella parole chiave
            echo ("<table border='1' align=center>"
            . "<tr>"
            . "<th> NOME AUTORE </th> "
            . "<th> COGNOME AUTORE </th> "
            . "</tr>");

            foreach($listaAutori as $row)
            {
                echo ("<tr>"
                . "<td>" . $row["NomeAutore"] . "</td>"
                . "<td>" . $row["CognomeAutore"] . "</td>"
                . "</tr>");
            }

            echo ("</table>");

    ?>
  
</body>
</html>
