<?php require_once "../ConnDb.php"; ?>
<?php require_once "../ConnessioneMongoDB.php"; ?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="RegistratiStyle.css">
    <title>Registrazione Presenter </title>
</head>

<body>
<p><br><a href="../Homepage.php"> TORNA ALLA HOMEPAGE </a></p>

    <form method="post" action="" enctype="multipart/form-data">
        <h1>Registrazione Presenter</h1>
        <input type="text" id="username" placeholder="Username" name="username" maxlength="30" required>
        <input type="text" id="nome" placeholder="Nome" name="nome" maxlength="30" required>
        <input type="text" id="cognome" placeholder="Cognome" name="cognome" maxlength="30" required>
        <input type="text" id="luogonascita" placeholder="Luogo di Nascita" name="luogoNascita" maxlength="30" required>
        <input type="date" id="datanascita" placeholder="Data di Nascita" name="dataNascita" required>
        <input type="password" id="password" placeholder="Password" name="password" maxlength="12" required>
        <input type="text" id="curriculum" placeholder="curriculum" name="curriculum" maxlength="30" required>
        <input type="text" id="nomedipartimento" placeholder="Nome Dipartimento" name="nomeDipartimento" maxlength="30" required>
        <input type="text" id="nomeuniversita" placeholder="Nome Universita" name="nomeUniversita" maxlength="40" required>
        <hr> UPLOAD FOTO </hr>
        <p> Aggiungi la foto personale</p>
        <table>
            <tr>
                <td> <b> </b> Seleziona file: </b></td>
                <td><input type='file' name="upload" id="upload">
                <td>
            </tr>
        </table>
        <input type="submit" name="submit" value="Registrati">
        <p><br><a href="../Login.php">Sei già iscritto? Accedi</a></p>
    </form>

    <?php

    // Evento invio form Registrati
    if (isset($_POST["submit"])) {
        return registrazioneUtente();
    }


    // Metodo per registrare un Presenter alla piattaforma
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
           
            ConnessioneMongoDB::connMongoDB($username, $sql1);
            return (registrazionePresenter());
        } catch (PDOException $e) {
            echo (" codice errore: " . $e->getMessage());
            exit();
        }
    }

    function registrazionePresenter()
    {
        $pdo = ConnDb::connToDb();
        try {
            $username = $_POST["username"];
            
            $uploadFile = $_FILES['upload']['tmp_name'];
             $foto = (file_get_contents($uploadFile));

            $sql2 = 'CALL registrazionePresenter(:username,:foto,:curriculum,:nomeDipartimento,:nomeUniversita)';
            $statement2 = $pdo->prepare($sql2);
            $statement2->bindParam(':username', $username, PDO::PARAM_STR);
            $statement2->bindParam(':foto', $foto, PDO::PARAM_LOB);
            $statement2->bindParam(':curriculum', $_POST["curriculum"], PDO::PARAM_STR);
            $statement2->bindParam(':nomeDipartimento', $_POST["nomeDipartimento"], PDO::PARAM_STR);
            $statement2->bindParam(':nomeUniversita', $_POST["nomeUniversita"], PDO::PARAM_STR);
            $statement2->execute();
            
            echo '<script language="javascript">';
            echo 'alert("REGISTRAZIONE UTENTE AVVENUTA CON SUCCESSO")';
            echo '</script>';
            ConnessioneMongoDB::connMongoDB($username, $sql2);
            
        } catch (PDOException $e) {
            echo (" codice errore: " . $e->getMessage());
            exit();
        }
    }


    ?>
</body>


</html>