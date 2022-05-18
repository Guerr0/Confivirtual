<?php require_once "../ConnessioneMongoDB.php"; ?>
<?php require_once "../ConnDb.php"; ?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="RegistratiStyle.css">
    <title>Registrazione Amministratore</title>
</head>

<body>

<p><br><a href="../Homepage.php"> TORNA ALLA HOMEPAGE </a></p>

    <form method="post" action="RegistratiAdmin.php">
        <h1>Registrazione Amministratore</h1>
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


    // Metodo per registrare un Amministratore alla piattaforma
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

            $sql1 = 'CALL RegistrazioneUtente(:username,:nome,:cognome,:password,:luogoNascita,:dataNascita)';
            $statement1 = $pdo->prepare($sql1);
            $statement1->bindParam(':username', $username, PDO::PARAM_STR);
            $statement1->bindParam(':nome', $nome, PDO::PARAM_STR);
            $statement1->bindParam(':cognome', $cognome, PDO::PARAM_STR);
            $statement1->bindParam(':password', $password, PDO::PARAM_STR);
            $statement1->bindParam(':luogoNascita', $luogoNascita, PDO::PARAM_STR);
            $statement1->bindParam(':dataNascita', $dataNascita, PDO::PARAM_STR);
            $statement1->execute();


            $sql2 = 'CALL registrazioneAmministratore(:username)';
            $statement2 = $pdo->prepare($sql2);
            $statement2->bindParam(':username', $username, PDO::PARAM_STR);
            $statement2->execute();

            echo '<script language="javascript">';
            echo 'alert("REGISTRATAZIONE AVVENUTA CON SUCCESSO")';
            echo '</script>';

            ConnessioneMongoDB::connMongoDB($nome, $sql1);
        } catch (PDOException $e) {
            echo (" codice errore: " . $e->getMessage());
            exit();
        }
    }



    ?>


 
</body>

</html>