<?php require_once "../ConnDb.php"; ?>
<!DOCTYPE html>
<html>

<head>
    <title>Tutorial da presentare </title>
    <link rel="stylesheet" href="../Operazioni_Presenter/OperazioniPresenterStyle.css">
</head>

<body>

<p><br><a href="../Dashboard_Utenti/Presenter.php"> TORNA ALLA DASHBOARD </a></p>

        <h1>VISUALIZZAZIONE ARTICOLI DA PRESENTARE</h1>
    
    <?php
    session_Start();
    $nome = $_SESSION['username'];
    

    $pdo = ConnDb::connToDb();
    $selectArticoli = "SELECT * 
                        FROM ARTICOLO,PRESENTAZIONE
                        WHERE  UsernamePresenter='$nome' AND PRESENTAZIONE.Codice = ARTICOLO.CodiceArticolo";

    $listaArticoli = $pdo->query($selectArticoli);    

     
     //stampa tutorial
     echo ("<table border='1' align=center>"
     . "<tr>"
     . "<caption> TABELLA ARTICOLI</caption>"
     . "<th> Codice </th> "
     . "<th> Numero Sequenza </th>"
     . "<th> Codice Sessione </th>"
     . "<th> Orario Inizio </th>"
     . "<th> Orario fine </th>"
     . "<th> Titolo </th> "
     . "<th> FilePDF </th>"
     . "<th> Numero Pagine </th>"
     . "</tr>");

     foreach($listaArticoli as $row)
     {
         echo ("<tr>"
             . "<td>" . $row["Codice"] . "</td>"
             . "<td>" . $row["NumeroSequenza"] . "</td>"
             . "<td>" . $row["CodiceSessione"] . "</td>"
             . "<td>" . $row["OraInizio"] . "</td>"
             . "<td>" . $row["OraFine"] . "</td>"
             . "<td>" . $row["Titolo"] . "</td>"
             . "<td>" . $row["FilePDF"] . "</td>"
             . "<td>" . $row["NumeroPagine"] . "</td>"
             . "</tr>");
     }
 
    echo ("</table>");

    ?>

    


</body>
</html>