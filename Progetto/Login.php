<?php require_once "ConnDb.php";

session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="LoginStyle.css">
    <title>Login</title>
</head>

<body>
    <form method="post" action="">
        <h1>Login</h1>
        <input type="text" id="username" placeholder="Username" name="username" maxlength="30" required>
        <input type="password" id="password" placeholder="Password" name="password" maxlength="12" required>
        <input type="submit" name="submit" value="Accedi">
        <p><br><a href="HomePage.php">Non sei ancora iscritto? Registrati</a></p>
    </form>
    <?php

    //Evento invio form Accedi
    if (isset($_POST["submit"])) {
        return accedi();
    }


    /*
    Metodo per accedere alla piattaforma
    Controllo che l'utente sia presente nella piattaforma , in questo caso identifico il tipo di utente.
    Se l'utente è presente allora viene settata anche la sessione salvando le informazioni riguardo l'username e il tipo di utente.
    */
    function accedi()
    {
        $pdo = ConnDb::connToDb();
        try {

            $username = $_POST["username"];
            $password = $_POST["password"];

            // Query SQL per controllare presenza Utente nel DB
            $queryUtentePresente = "SELECT COUNT(*) AS Numero FROM UTENTE WHERE Username='$username' AND Password='$password'";
            $numero = $pdo->query($queryUtentePresente);
        } catch (PDOException $e) {
            echo ("[ERRORE] Query SQL non riuscita. Errore: " . $e->getMessage());
            exit();
        }


        $row = $numero->fetch();
        $utentePresente = $row["Numero"];


        if ($utentePresente == 0) {
            echo '<script language="javascript">';
            echo 'alert("USERNAME O PASSWORD ERRATI")';
            echo '</script>';
        } else {
            $queryPresenterPresente = "SELECT COUNT(*) AS Numero FROM PRESENTER WHERE UsernamePresenter='$username'";
            $numero = $pdo->query($queryPresenterPresente);
            $row = $numero->fetch();
            $presenterPresente = $row["Numero"];

            $querySpeakerPresente = "SELECT COUNT(*) AS Numero FROM SPEAKER WHERE UsernameSpeaker='$username' ";
            $numero = $pdo->query($querySpeakerPresente);
            $row = $numero->fetch();
            $speakerPresente = $row["Numero"];

            $queryAmministratorePresente = "SELECT COUNT(*) AS Numero FROM AMMINISTRATORE WHERE UsernameAmministratore='$username'";
            $numero = $pdo->query($queryAmministratorePresente);
            $row = $numero->fetch();
            $amministratorePresente = $row["Numero"];



            if ($presenterPresente == 1) {
                Header("Location:Dashboard_Utenti/Presenter.php");
                session_Start();
                $_SESSION['username'] = $_POST["username"];
                $_SESSION['tipoUtente'] = "PRESENTER";
            } else if ($speakerPresente == 1) {
                Header("Location:Dashboard_Utenti/Speaker.php");
                session_Start();
                $_SESSION['username'] = $_POST["username"];
                $_SESSION['tipoUtente'] = "SPEAKER";
            } else if ($amministratorePresente == 1) {
                Header("Location:Dashboard_Utenti/Amministratore.php");
                session_Start();
                $_SESSION['username'] = $_POST["username"];
                $_SESSION['tipoUtente'] = "AMMINISTRATORE";
            } else //caso utente generico
            {
                Header("Location:Dashboard_Utenti/Generico.php");
                session_Start();
                $_SESSION['username'] = $_POST["username"];
                $_SESSION['tipoUtente'] = "GENERICO";
            }
        }
    }



    ?>
</body>

</html>