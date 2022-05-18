<?php require_once "../ConnDb.php"; ?>
<!DOCTYPE html>
<html>

<head>
    <title>Visualizzazione sessioni e presentazioni </title>
    <link rel="stylesheet" href="../Operazioni_Generali/OperazioniVisualizzazione.css">
</head>

<body>
    <?php
    session_Start();
    $nome = $_SESSION['username'];
    $tipoUtente = $_SESSION["tipoUtente"];

    # TORNA ALLA DASHBOARD
    if ($tipoUtente == "GENERICO") {echo ("<p><br><a href='../Dashboard_Utenti/Generico.php'> TORNA ALLA DASHBOARD </a></p>"); }
    if ($tipoUtente == "PRESENTER") {echo ("<p><br><a href='../Dashboard_Utenti/Presenter.php'> TORNA ALLA DASHBOARD </a></p>");}
    if ($tipoUtente == "SPEAKER") { echo ("<p><br><a href='../Dashboard_Utenti/Speaker.php'> TORNA ALLA DASHBOARD </a></p>");}
    if ($tipoUtente == "AMMINISTRATORE") {echo ("<p><br><a href='../Dashboard_Utenti/Amministratore.php'> TORNA ALLA DASHBOARD </a></p>"); }

    $pdo = ConnDb::connToDb();

    ?>

    <h1>VISUALIZZAZIONE PRESENTAZIONI E SESSIONI</h1>

    <?php
    $pdo = ConnDb::connToDb();
    $selectSessioni = "SELECT Codice,Titolo,LinkPartecipazione,OraInizio,OraFine,NumeroPresentazioni,AcronimoConferenza,AnnoEdizioneConferenza,Data
                            FROM SESSIONE,PROGRAMMAGIORNALIERO,REGISTRAZIONE,CONFERENZA
                            WHERE IdProgramma=ID AND AcronimoConferenza=AcronimoConf 
                            AND AnnoEdizioneConferenza=AnnoEdiziConf AND UsernameUtente='$nome'
                            AND CONFERENZA.AnnoEdizione=PROGRAMMAGIORNALIERO.AnnoEdizioneConferenza 
                            AND CONFERENZA.Acronimo=PROGRAMMAGIORNALIERO.AcronimoConferenza
                            AND CONFERENZA.Svolgimento='ATTIVA'";
    $listaSessioni = $pdo->query($selectSessioni);
    ?>

    <?php

    echo ("<h1>TABELLA SESSIONI </h1>");


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
        . "<th> Numero Presentazioni </th>"
        . "<th> Link partecipazione </th>"
        . "<th> Visualizza Presentazioni </th>"
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
            . "<td>" . $row["NumeroPresentazioni"] . "</td>"
            . "<td>" . $row["LinkPartecipazione"] . "</td>"
            . "<td>  <form id='nome'  action='' method='post'>
                 <input type='submit' name='submit' value='$row[Codice]'>
                 </form> </td>"
            . "</tr>");
    }
    echo ("</table>");


    if (isset($_POST["submit"])) {
        stampaTabellaPresentazioni();
    }



    // METODO PER STAMPARE TUTTI GLI ARTICOLI E TUTORIAL DI UNA SESSIONE

    function stampaTabellaPresentazioni()
    {
        $nome = $_SESSION['username'];
        $codiceSessione = $_POST["submit"];
        //select per avere le presentazioni delle conferenze a cui sei iscritto
        $pdo = ConnDb::connToDb();
        $selectPresentazioni = "SELECT * 
         FROM PRESENTAZIONE
        WHERE (CodiceSessione IN (SELECT Codice 
                             FROM SESSIONE 
                             WHERE IdProgramma IN (SELECT ID  
                                                   FROM PROGRAMMAGIORNALIERO,REGISTRAZIONE
                                                   WHERE (AcronimoConferenza=AcronimoConf AND AnnoEdizioneConferenza=AnnoEdiziConf AND UsernameUtente='$nome'))))
                               AND CodiceSessione = '$codiceSessione'";
        $listaPresentazioni = $pdo->query($selectPresentazioni);
        // Stampa ARTICOLI

        echo ("<table border='1'>"
            . "<tr>"
            . "<caption> TABELLA ARTICOLI</caption>"
            . "<th> Codice </th> "
            . "<th> Numero Sequenza </th>"
            . "<th> Codice Sessione </th>"
            . "<th> Orario Inizio </th>"
            . "<th> Orario fine </th>"
            . "<th> Titolo </th> "
            . "<th> StatoSvolgimento </th>"
            . "<th> FilePDF </th>"
            . "<th> NumeroPagine </th>"
            . "<th> UsernamePresenter </th>"
            . "<th> VISUALIZZA PAROLE CHIAVE </th>"
            . "<th> VISUALIZZA AUTORI </th>"
            . "</tr>");


        //stampa articoli 
        $listaPresentazioni = $pdo->query($selectPresentazioni);
        foreach ($listaPresentazioni as $rowPresentazione) {
            $codiceArticoloCorrente = $rowPresentazione["Codice"];
            $selectArticoli = "SELECT * FROM ARTICOLO WHERE CodiceArticolo='$codiceArticoloCorrente' ";
            $listaArticoli = $pdo->query($selectArticoli);

            foreach($listaArticoli as $rowArticolo)
            {
                echo ("<tr>"
                    . "<td>" . $codiceArticoloCorrente . "</td>"
                    . "<td>" . $rowPresentazione["NumeroSequenza"] . "</td>"
                    . "<td>" . $rowPresentazione["CodiceSessione"] . "</td>"
                    . "<td>" . $rowPresentazione["OraInizio"] . "</td>"
                    . "<td>" . $rowPresentazione["OraFine"] . "</td>"
                    . "<td>" . $rowArticolo["Titolo"] . "</td>"
                    . "<td>" . $rowArticolo["StatoSvolgimento"] . "</td>"
                    . "<td>" . $rowArticolo["FilePDF"] . "</td>"
                    . "<td>" . $rowArticolo["NumeroPagine"] . "</td>"
                    . "<td>" . $rowArticolo["UsernamePresenter"] . "</td>"
                    . "<td>  <form  action='VisualizzaParoleChiaveArticolo.php' method='GET'>
                    <input type='submit' name='submit' value='$codiceArticoloCorrente'>
                    </form> </td>"
                    . "<td>  <form  action='VisualizzaAutoriArticolo.php' method='GET'>
                    <input type='submit' name='submit' value='$codiceArticoloCorrente'>
                    </form> </td>"
                    . "</tr>");
            }
        }
        
        echo ("</table>");

        echo("<br>");
        echo("<br>");

        //stampa tutorial
        echo ("<table border='1'>"
        . "<tr>"
        . "<caption> TABELLA TUTORIAL</caption>"
        . "<th> Codice </th> "
        . "<th> Numero Sequenza </th>"
        . "<th> Codice Sessione </th>"
        . "<th> Orario Inizio </th>"
        . "<th> Orario fine </th>"
        . "<th> Titolo </th> "
        . "<th> Abstract </th>"
        . "<th> VISUALIZZA SPEAKER </th>"
        . "<th> VISUALIZZA INFORMAZIONI AGGIUNTIVE </th>"
        . "</tr>");


        $listaPresentazioni = $pdo->query($selectPresentazioni);

        foreach ($listaPresentazioni as $rowPresentazione) {
            $codiceTutorialCorrente = $rowPresentazione["Codice"];
            $selectTutorial = "SELECT * FROM TUTORIAL WHERE CodiceTutorial='$codiceTutorialCorrente' ";
            $listaTutorial = $pdo->query($selectTutorial);

            foreach($listaTutorial as $rowTutorial)
            {
                echo ("<tr>"
                    . "<td>" . $codiceTutorialCorrente . "</td>"
                    . "<td>" . $rowPresentazione["NumeroSequenza"] . "</td>"
                    . "<td>" . $rowPresentazione["CodiceSessione"] . "</td>"
                    . "<td>" . $rowPresentazione["OraInizio"] . "</td>"
                    . "<td>" . $rowPresentazione["OraFine"] . "</td>"
                    . "<td>" . $rowTutorial["Titolo"] . "</td>"
                    . "<td>" . $rowTutorial["Abstract"] . "</td>"
                    . "<td>  <form  action='VisualizzaSpeakerTutorial.php' method='GET'>
                    <input type='submit' name='submit' value='$codiceTutorialCorrente'>
                    </form> </td>"
                    . "<td>  <form  action='VisualizzaInformazioniAggiuntive.php' method='GET'>
                    <input type='submit' name='submit' value='$codiceTutorialCorrente'>
                    </form> </td>"

                    . "</tr>");
            }
        }
        echo ("</table>");





    }




    ?>
</body>

</html>