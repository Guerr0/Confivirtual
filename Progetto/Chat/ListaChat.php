<?php require_once "../ConnessioneMongoDB.php"; ?>
<?php require_once "../ConnDb.php"; ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Lista Chat</title>
        <link rel="stylesheet" href="ChatStyle.css">
    </head>
    <body>
    <?php
      session_Start();
      $nome=$_SESSION["username"];
      $tipoUtente= $_SESSION["tipoUtente"];

      
     # TORNA ALLA DASHBOARD
    if($tipoUtente=="GENERICO"){ echo("<p><br><a href='../Dashboard_Utenti/Generico.php'> TORNA ALLA DASHBOARD </a></p>"); }
    if($tipoUtente=="PRESENTER"){ echo("<p><br><a href='../Dashboard_Utenti/Presenter.php'> TORNA ALLA DASHBOARD </a></p>"); }
    if($tipoUtente=="SPEAKER"){ echo("<p><br><a href='../Dashboard_Utenti/Speaker.php'> TORNA ALLA DASHBOARD </a></p>"); }
    if($tipoUtente=="AMMINISTRATORE"){ echo("<p><br><a href='../Dashboard_Utenti/Amministratore.php'> TORNA ALLA DASHBOARD </a></p>"); }
    ?>
    <h1>LISTA CHAT</h1>
    
        
    <?php
    $pdo = ConnDb::connToDb();
         $selectSessioni = "SELECT Codice,Titolo,OraInizio,OraFine,AcronimoConferenza,AnnoEdizioneConferenza,Data
                            FROM SESSIONE,PROGRAMMAGIORNALIERO,REGISTRAZIONE
                            WHERE IdProgramma=ID AND AcronimoConferenza=AcronimoConf AND AnnoEdizioneConferenza=AnnoEdiziConf 
                            AND UsernameUtente='$nome'";
        $listaSessioni = $pdo->query($selectSessioni);
    ?>

    <?php
    
     echo ("<h1> SESSIONI </h1>");
   
   
    // stampa le sessioni 
    echo ("<table border='1' align=center>"
    . "<tr>"
    . "<th> Codice </th> "
    . "<th> Data </th>"
    . "<th> Acronimo Conferenza </th>"
    . "<th> Anno Edizione Conferenza </th>"
    . "<th> Titolo </th>"
    . "<th> Orario Inizio </th>"
    . "<th> Orario Fine </th>"
    . "<th> ACCEDI ALLA CHAT </th>"
    . "</tr>");

    foreach ($listaSessioni as $row) {
    echo ("<tr>"
        . "<td>" . $row["Codice"] . "</td>"
        . "<td>" . $row["Data"] . "</td>"
        . "<td>" . $row["AcronimoConferenza"] . "</td>"
        . "<td>" . $row["AnnoEdizioneConferenza"] . "</td>"
        . "<td>" . $row["Titolo"] . "</td>"
        . "<td>" . $row["OraInizio"] . "</td>"
        . "<td>" . $row["OraFine"] . "</td>"
        . "<td>  
                <form  action='ChatSessione.php' method='get'>
                 <input type='hidden' name='data' value='$row[Data]'>
                 <input type='hidden' name='codiceSessione' value='$row[Codice]'>
                 <input type='submit' name='inviaChat' value='CHAT'>
                 </form> 
        </td>"
        . "</tr>");
    }
    echo ("</table>");


 ?>



 



        
        

        
    </body>
    
</html>