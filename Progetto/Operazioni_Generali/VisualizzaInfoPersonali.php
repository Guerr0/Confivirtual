<?php require_once "../ConnDb.php"; ?>
<?php require_once "../ConnessioneMongoDB.php"; ?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../Operazioni_Generali/OperazioniStyle.css">
    <title>INFO PERSONALI</title>
</head>

<body>
    <?php
    session_Start();
    $username = $_SESSION['username'];
    $tipoUtente = $_SESSION['tipoUtente'];

         # TORNA ALLA DASHBOARD
         if($tipoUtente=="GENERICO"){ echo("<p><br><a href='../Dashboard_Utenti/Generico.php'> TORNA ALLA DASHBOARD </a></p>"); }
         if($tipoUtente=="PRESENTER"){ echo("<p><br><a href='../Dashboard_Utenti/Presenter.php'> TORNA ALLA DASHBOARD </a></p>"); }
         if($tipoUtente=="SPEAKER"){ echo("<p><br><a href='../Dashboard_Utenti/Speaker.php'> TORNA ALLA DASHBOARD </a></p>"); }
         if($tipoUtente=="AMMINISTRATORE"){ echo("<p><br><a href='../Dashboard_Utenti/Amministratore.php'> TORNA ALLA DASHBOARD </a></p>"); }

    ?>
    <h1>INFORMAZIONI PERSONALI</h1>
    <!--Inserimento dati  -->
    <form id="form1" action="" method="post" style="margin: 150px;" enctype='multipart/form-data'>
        <?php
        #RECUPERO INFORMAZIONI PERSONALI
        $pdo = ConnDb::connToDb();
        try {
            
            $selectInfoPersonali = "SELECT * FROM UTENTE WHERE Username='$username'";
            $arrayInfo = $pdo->query($selectInfoPersonali);

            $row = $arrayInfo->fetch();

            $nome = $row["Nome"];
            $cognome = $row["Cognome"];
            $password = $row["Password"];
            $luogoNascita = $row["LuogoNascita"];
            $dataNascita = $row["DataNascita"];
           


            echo ("<table border='1'>"
                . "<caption>LE MIE INFO</caption>"
                . "<tr>"
                . "<th> Username</th>"
                . "<th> Nome </th>"
                . "<th> Cognome </th>"
                . "<th> Password </th>"
                . "<th> Luogo Nascita </th>"
                . "<th> Data Nascita </th>"
                . "</tr>");
            echo ("<tr>"
                . "<td>" . $username . "</td>"
                . "<td>" . $nome . "</td>"
                . "<td>" . $cognome . "</td>"
                . "<td>" . $password . "</td>"
                . "<td>" . $luogoNascita . "</td>"
                . "<td>" . $dataNascita . "</td>"
                
                . "</tr>");

            echo ("</table>");
        } catch (PDOException $e) {
            echo ("[ERRORE] Query SQL non riuscita. Errore: " . $e->getMessage());
            exit();
        }

        ?>

        <h2>Modifica informazioni </h2>
        <input type="text"  placeholder="nome" name="nome" maxlength="30" value='<?php echo ($nome) ?>' required>
        <input type="text"  placeholder="cognome" name="cognome" maxlength="30" value='<?php echo ($cognome) ?>'required>
        <input type="text"  placeholder="password" name="password" maxlength="12" value='<?php echo ($password) ?>' required>
        <input type="text"  placeholder="luogo nascita" name="luogoNascita" maxlength="30" value='<?php echo ($luogoNascita) ?>' required>
        <input type="date"  placeholder="data nascita" name="dataNascita" value='<?php echo ($dataNascita) ?>' required>
        <input type="submit" name="submit" value="MODIFICA" >

    </form>


    <?php
    if (isset($_POST["submit"])) {
        if ($_POST["submit"] == "MODIFICA") {
            return modificaInfo(); 
        }     
    }

    // Funzione che modifica le informazioni personali
    function modificaInfo()
    {
        $pdo = ConnDb::connToDb();
        try {
            $username = $_SESSION['username'];
            $nome = $_POST['nome'];
            $cognome = $_POST['cognome'];
            $password = $_POST['password'];
            $luogoNascita = $_POST['luogoNascita'];
            $dataNascita= $_POST['dataNascita'];

            $sql1 = 'CALL modificaInfoPersonali(:username, :nome, :cognome, :password, :luogoNascita, :dataNascita)';
            $statement1 = $pdo->prepare($sql1);
            $statement1->bindParam(':username', $username, PDO::PARAM_STR);
            $statement1->bindParam(':nome', $nome, PDO::PARAM_STR);
            $statement1->bindParam(':cognome', $cognome, PDO::PARAM_STR);
            $statement1->bindParam(':password', $password, PDO::PARAM_STR);
            $statement1->bindParam(':luogoNascita', $luogoNascita, PDO::PARAM_STR);
            $statement1->bindParam(':dataNascita', $dataNascita, PDO::PARAM_LOB);
            $statement1->execute();
            echo '<script language="javascript">';
            echo 'alert("MODIFICA AVVENUTA CON SUCCESSO")';
            echo '</script>';

            ConnessioneMongoDB::connMongoDB($username,$sql1);

            header("Refresh:0");

        } catch (PDOException $e) {
            echo ("[ERRORE] Query SQL non riuscita. Errore: " . $e->getMessage());
            exit();
        }
    }


    ?>


</body>

</html>