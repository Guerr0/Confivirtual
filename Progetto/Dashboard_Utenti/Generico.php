<?php require_once "../ConnDb.php"; ?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="DashboardStyle.css">
    <title>Generico</title>
</head>

<body>
    <?php
    session_Start();
    ?>
    <h1> BENTORNATO <?php echo $_SESSION['username']; ?> !! <button id="button1" onclick="location.href='../Operazioni_Generali/VisualizzaInfoPersonali.php'">Le mie info</button> </h1>


    <h1>Seleziona tra le alternative</h1>


    <button id="button1" onclick="location.href='../Operazioni_Generali/Conferenze.php'"> Registrazione Conferenze</button>
    <button id="button1" onclick="location.href='../Operazioni_Generali/ProgrammiGiornalieri.php'"> Calendario Conferenze</button>
    <button id="button1" onclick="location.href='../Operazioni_Generali/VisualizzazioneSessioniPresentazioni.php'"> Visualizzazione Sessioni/presentazioni</button>
    <button id="button1" onclick="location.href='../Operazioni_Generali/ListaPresentazionePreferite.php'"> Lista presentazione preferite</button>

    <h3>CHAT</h3>
    <button id="button1" onclick="location.href='../Chat/ListaChat.php'">Chat </button>




    <form method="post" action="">
        <input type="submit" name="LOG_OUT" value="LOG OUT">
    </form>

    <?php


    if (isset($_POST["LOG_OUT"])) {
        session_destroy();
        Header("Location:../HomePage.php");
    }
    ?>

    </div>

</body>

</html>