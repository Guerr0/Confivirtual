<?php require_once "../ConnDb.php"; ?>
<?php require_once "../ConnessioneMongoDB.php"; ?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="RegistratiStyle.css">
    <title>Registrazione Utente</title>
</head>

<body>
<p><br><a href="../Homepage.php"> TORNA ALLA HOMEPAGE </a></p>

    <form method="post" action="RegistratiUtente.php">
        <h1>Registrazione</h1>
        <input type="text" id="username" placeholder="Username" name="username" maxlength="30" required>
        <input type="text" id="nome" placeholder="Nome" name="nome" maxlength="30" required>
        <input type="text" id="cognome" placeholder="Cognome" name="cognome" maxlength="30" required>
        <input type="text" id="luogonascita" placeholder="Luogo di Nascita" name="luogoNascita" maxlength="30" required>
        <input type="date" id="datanascita" placeholder="Data di Nascita" name="dataNascita" required>
        <input type="password" id="password" placeholder="Password" name="password" maxlength="12" required>
        <input type="submit" name="submit" value="Registrati">
        <p><br><a href="../Login.php">Sei già iscritto? Accedi</a></p>
    </form>

    <?php

    // Evento invio form Registrati
    if (isset($_POST["submit"])) {
        return registrazioneUtente();
    }

    // Metodo per registrare un Utente Generico alla piattaforma
    function registrazioneUtente()
    {
        $pdo = ConnDb::connToDb();
        try {

            $username = $_POST["username"];
            $nome = $_POST["nome"];
            $cognome = $_POST["cognome"];
            $luogoNascita = $_POST["luogoNascita"];
            $dataNascita = $_POST["dataNascita"];
            $password = $_POST["password"];


            $sql = 'CALL RegistrazioneUtente(:username,:nome,:cognome,:password,:luogoNascita,:dataNascita)';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':username', $username, PDO::PARAM_STR);
            $statement->bindParam(':nome', $nome, PDO::PARAM_STR);
            $statement->bindParam(':cognome', $cognome, PDO::PARAM_STR);
            $statement->bindParam(':password', $password, PDO::PARAM_STR);
            $statement->bindParam(':luogoNascita', $luogoNascita, PDO::PARAM_STR);
            $statement->bindParam(':dataNascita', $dataNascita, PDO::PARAM_STR);
            $statement->execute();
            //echo("INSERIMENTO AVVENUTO");
            echo '<script language="javascript">';
            echo 'alert("REGISTRATAZIONE AVVENUTA CON SUCCESSO")';
            echo '</script>';
            ConnessioneMongoDB::connMongoDB($nome, $sql);
        } catch (PDOException $e) {
            echo '<script language="javascript">';
            echo 'alert("ERRORE: Username già esistente ")';
            echo '</script>';
            exit();
        }
    }



    ?>
</body>


</html>