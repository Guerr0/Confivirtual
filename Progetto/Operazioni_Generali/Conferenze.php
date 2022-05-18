<?php require_once "../ConnDb.php"; ?>
<!DOCTYPE html>
<html>

<head>
    <title>Gestione Conferenze</title>
    <link rel="stylesheet" href="../Operazioni_Generali/OperazioniStyle.css">
    <script src="noRefresh.js"></script>
</head>


<body>
    <?php
    session_Start();
    $pdo = ConnDb::connToDb();
    $nome = $_SESSION["username"];
    $tipoUtente = $_SESSION["tipoUtente"];

    # TORNA ALLA DASHBOARD
    if ($tipoUtente == "GENERICO") {echo ("<p><br><a href='../Dashboard_Utenti/Generico.php'> TORNA ALLA DASHBOARD </a></p>");}
    if ($tipoUtente == "PRESENTER") {echo ("<p><br><a href='../Dashboard_Utenti/Presenter.php'> TORNA ALLA DASHBOARD </a></p>");}
    if ($tipoUtente == "SPEAKER") {echo ("<p><br><a href='../Dashboard_Utenti/Speaker.php'> TORNA ALLA DASHBOARD </a></p>");}
    if ($tipoUtente == "AMMINISTRATORE") {echo ("<p><br><a href='../Dashboard_Utenti/Amministratore.php'> TORNA ALLA DASHBOARD </a></p>");}

    //select per avere gli acronimi delle conferenze
    $selectConferenze = "SELECT * FROM CONFERENZA WHERE NOT EXISTS (SELECT * FROM REGISTRAZIONE
                                                                     WHERE AnnoEdizione = AnnoEdiziConf  AND AcronimoConf = Acronimo AND UsernameUtente='$nome')
                                                                    AND  Svolgimento = 'ATTIVA'";

    $listaAcronimi = $pdo->query($selectConferenze);
    ?>

    <center>
        <h1>REGISTRAZIONE CONFERENZE</h1>
    </center>

    <form id="formConferenze" action="" method="post" style="margin: 150px;">

        <h3> ISCRIVITI A UNA CONFERENZA </h3>
        <input type="number" placeholder="Anno Edizione Conferenza" name="annoEdizioneConf" required>
        <span> Scegli acronimo conferenza: </span>
        <select name="acronimiConferenze">
            <option>SCEGLI</option>
            <?php
            foreach ($listaAcronimi as $row) {
                $acronimo = $row['Acronimo'];
                echo ("<option value='$acronimo'>$acronimo</option>");
            }
            ?>
        </select>
        <input id="btnIscriviti" type="submit" name="submit" value="ISCRIVITI">

    </form>

    <?php

    # TABELLA DELLE CONFERENZE

    $pdo = ConnDb::connToDb();
    try {
        // Query SQL per leggere le conferenze disponibili
        $selectConferenzeDisponibili = "SELECT * FROM CONFERENZA WHERE NOT EXISTS (SELECT * FROM REGISTRAZIONE
                                                                                     WHERE AnnoEdizione = AnnoEdiziConf 
                                                                                      AND AcronimoConf = Acronimo
                                                                                      AND UsernameUtente='$nome')
                                                                AND  Svolgimento = 'ATTIVA'";

        $arrayConferenzeDisponibili = $pdo->query($selectConferenzeDisponibili);
    } catch (PDOException $e) {
        echo ("[ERRORE] Query SQL non riuscita. Errore: " . $e->getMessage());
        exit();
    }

    echo ("<table border='1'>"
        . "<caption> CONFERENZE DISPONIBILI </caption>"
        . "<tr>"
        . "<th> Nome</th>"
        . "<th> Acronimo </th>"
        . "<th> Anno Edizione </th>"
        . "<th> Svolgimento</th>"
        . "</tr>");
    foreach ($arrayConferenzeDisponibili as $row) {
        echo ("<tr>"
            . "<td>" . $row["Nome"] . "</td>"
            . "<td>" . $row["Acronimo"] . "</td>"
            . "<td>" . $row["AnnoEdizione"] . "</td>"
            . "<td>" . $row["Svolgimento"] . "</td>"
            . "</tr>");
    }
    echo ("</table>");

    echo ("<br>");
    echo ("<br>");



   
    try {
         // Query SQL per leggere le conferenze a cui sono iscritto
        $selectConferenzeIscritte = "SELECT * FROM CONFERENZA,REGISTRAZIONE WHERE( AnnoEdizione = AnnoEdiziConf 
                                                                                      AND AcronimoConf = Acronimo
                                                                                      AND UsernameUtente='$nome')";
        $arrayConferenzeIscritte = $pdo->query($selectConferenzeIscritte);
    } catch (PDOException $e) {
        echo ("[ERRORE] Query SQL non riuscita. Errore: " . $e->getMessage());
        exit();
    }

    echo ("<table border='1'>"
        . "<caption> CONFERENZE A CUI SONO ISCRITTO </caption>"
        . "<tr>"
        . "<th> Nome</th>"
        . "<th> Acronimo </th>"
        . "<th> Anno Edizione </th>"
        . "<th> Svolgimento</th>"
        . "</tr>");
    foreach ($arrayConferenzeIscritte as $row) {
        echo ("<tr>"
            . "<td>" . $row["Nome"] . "</td>"
            . "<td>" . $row["Acronimo"] . "</td>"
            . "<td>" . $row["AnnoEdizione"] . "</td>"
            . "<td>" . $row["Svolgimento"] . "</td>"
            . "</tr>");
    }
    echo ("</table>");


    if (isset($_POST["submit"])) {
        if ($_POST["submit"] == "ISCRIVITI") {
            return iscrizioneConferenza();
        }
    }

    #---------------------------------------------------------#
    #iscrizione a una conferenza 
    function iscrizioneConferenza()
    {
        $pdo = ConnDb::connToDb();
        try {
            $nome = $_SESSION['username'];
            $annoEdizione = $_POST['annoEdizioneConf'];
            $acronimo = $_POST['acronimiConferenze'];

            $sql1 = 'CALL registrazioneConferenza(:nome, :acronimiConferenze, :annoEdizioneConf)';
            $statement1 = $pdo->prepare($sql1);
            $statement1->bindParam(':annoEdizioneConf', $annoEdizione, PDO::PARAM_STR);
            $statement1->bindParam(':acronimiConferenze', $acronimo, PDO::PARAM_STR);
            $statement1->bindParam(':nome', $nome, PDO::PARAM_STR);
            $statement1->execute();

            echo '<script language="javascript">';
            echo 'alert("Registrazione avvenuta con successo")';
            echo '</script>';
            header("Refresh:0");
        } catch (PDOException $e) {

            echo '<script language="javascript">';
            echo 'alert("ERRORE : Conferenza inesistente ")';
            echo '</script>';
            exit();
        }
    }


    ?>


</body>

</html>