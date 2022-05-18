<?php require_once "../ConnDb.php"; ?>
<!DOCTYPE html>
<html>

<head>
    <title>Inserimento presentazioni preferite</title>
    <link rel="stylesheet" href="../Operazioni_Generali/OperazioniStyle.css">
</head>

<body>
    <?php
    session_Start();
    $nome = $_SESSION['username'];
    $tipoUtente = $_SESSION["tipoUtente"];
    $pdo = ConnDb::connToDb();

    # TORNA ALLA DASHBOARD
    if ($tipoUtente == "GENERICO") {echo ("<p><br><a href='../Dashboard_Utenti/Generico.php'> TORNA ALLA DASHBOARD </a></p>"); }
    if ($tipoUtente == "PRESENTER") { echo ("<p><br><a href='../Dashboard_Utenti/Presenter.php'> TORNA ALLA DASHBOARD </a></p>");  }
    if ($tipoUtente == "SPEAKER") {  echo ("<p><br><a href='../Dashboard_Utenti/Speaker.php'> TORNA ALLA DASHBOARD </a></p>"); }
    if ($tipoUtente == "AMMINISTRATORE") {  echo ("<p><br><a href='../Dashboard_Utenti/Amministratore.php'> TORNA ALLA DASHBOARD </a></p>"); }


    //select per avere i codice delle presentazioni delle conferenze a cui sei iscritto

    $selectPresentazioni = "SELECT Codice 
                            FROM PRESENTAZIONE
                            WHERE (CodiceSessione IN (SELECT Codice 
                                                      FROM SESSIONE 
                                                     WHERE IdProgramma IN (SELECT ID  
                                                                           FROM PROGRAMMAGIORNALIERO,REGISTRAZIONE,CONFERENZA
                                                                           WHERE (AcronimoConferenza=AcronimoConf 
                                                                           AND AnnoEdizioneConferenza=AnnoEdiziConf AND UsernameUtente='$nome'
                                                                           AND CONFERENZA.AnnoEdizione=PROGRAMMAGIORNALIERO.AnnoEdizioneConferenza 
                                                                           AND CONFERENZA.Acronimo=PROGRAMMAGIORNALIERO.AcronimoConferenza
                                                                           AND CONFERENZA.Svolgimento='ATTIVA'))))
                            AND Codice NOT IN (SELECT CodicePresentazione 
                            FROM lista_presentazioni_preferite
                            WHERE UsernameUtente='$nome');";
    $listaPresentazioni = $pdo->query($selectPresentazioni);


    ?>
    <center>
        <h1>INSERIMENTO PRESENTAZIONE TRA I PREFERITI </h1>
    </center>

    <form id="form1" action="" method="post" style="margin: 150px;">
        <h3> INSERIMENTO PRESENTAZIONE TRA I PREFERITI </h3>
        <span> Scegli presentazione: </span>
        <select name="codicePresentazione">
            <option>SCEGLI</option>
            <?php
            foreach ($listaPresentazioni as $row) {
                $codicePresentazione = $row['Codice'];
                echo ("<option value='$codicePresentazione'>$codicePresentazione</option>");
            }
            ?>
        </select>
        <input type="submit" name="submit" value="INSERISCI">
    </form>

    <?php
    $pdo = ConnDb::connToDb();
    try {
        // Query SQL per leggere le presentazioni preferite
        $selectArticoli = "SELECT CodiceArticolo,Titolo,FilePdf,NumeroPagine,UsernamePresenter
        FROM LISTA_PRESENTAZIONI_PREFERITE,ARTICOLO 
        WHERE (UsernameUtente = '$nome' AND CodicePresentazione=CodiceArticolo);";
        $arrayArticoli = $pdo->query($selectArticoli);
    } catch (PDOException $e) {
        echo ("[ERRORE] Query SQL non riuscita. Errore: " . $e->getMessage());
        exit();
    }

    //TABELLA ARTICOLI PREFERITI
    echo ("<table border='1' align=center>"
        . "<caption> ARTICOLI PREFERITI </caption>"
        . "<tr>"
        . "<th> Codice Articolo </th> "
        . "<th> Titolo </th> "
        . "<th> FilePdf </th> "
        . "<th> Numero Pagine </th> "
        . "<th> Presenter  </th> "
        . "</tr>");

    foreach ($arrayArticoli as $row) {

        echo ("<tr>"
            . "<td>" . $row["CodiceArticolo"] . "</td>"
            . "<td>" . $row["Titolo"] . "</td>"
            . "<td>" . $row["FilePdf"] . "</td>"
            . "<td>" . $row["NumeroPagine"] . "</td>"
            . "<td>" . $row["UsernamePresenter"] . "</td>"
            . "</tr>");
    }
    echo ("</table>");

    echo ("<br>");
    echo ("<br>");


    //TABELLA TUTORIAL PREFERITI
    try {
        // Query SQL per leggere le presentazioni preferite
        $selectTutorial = "SELECT CodiceTutorial,Titolo,Abstract
                               FROM LISTA_PRESENTAZIONI_PREFERITE,TUTORIAL 
                               WHERE (UsernameUtente = '$nome' AND CodicePresentazione=CodiceTutorial);";
        $arrayTutorial = $pdo->query($selectTutorial);
    } catch (PDOException $e) {
        echo ("[ERRORE] Query SQL non riuscita. Errore: " . $e->getMessage());
        exit();
    }

    echo ("<table border='1' align=center>"
        . "<caption> TUTORIAL PREFERITI </caption>"
        . "<tr>"
        . "<th> Codice Presentazione </th> "
        . "<th> Titolo </th> "
        . "<th> Abstract </th> "
        . "</tr>");

    foreach ($arrayTutorial as $row) {

        echo ("<tr>"
            . "<td>" . $row["CodiceTutorial"] . "</td>"
            . "<td>" . $row["Titolo"] . "</td>"
            . "<td>" . $row["Abstract"] . "</td>"
            . "</tr>");
    }
    echo ("</table>");



    if (isset($_POST["submit"])) {
        if ($_POST["submit"] == "INSERISCI") {
            return InserisciPresentazione();
        }
    }

    //Funzione che inserisce le presentazione 
    function InserisciPresentazione()
    {
        $pdo = ConnDb::connToDb();
        try {
            $codicePresentazione = $_POST["codicePresentazione"];
            $nome = $_SESSION['username'];
            $sql1 = 'CALL inserimentoPresentazioniFavorite(:codicePresentazione, :nome)';
            $statement1 = $pdo->prepare($sql1);
            $statement1->bindParam(':codicePresentazione', $codicePresentazione, PDO::PARAM_STR);
            $statement1->bindParam(':nome', $nome, PDO::PARAM_STR);
            $statement1->execute();
            header("Refresh:0");
        } catch (PDOException $e) {
            echo '<script language="javascript">';
            echo 'alert("ERRORE :  scegli correttamente una presentazione")';
            echo '</script>';
            exit();
        }
    }
    ?>
</body>

</html>