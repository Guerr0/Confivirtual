<?php require_once "../ConnDb.php"; ?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="DashboardStyle.css">
    <title>Speaker</title>
</head>

<body>
    <?php
    session_Start();
    ?>
    <h1> BENTORNATO <?php echo $_SESSION['username']; ?> !! <button id="button1" onclick="location.href='../Operazioni_Generali/VisualizzaInfoPersonali.php'">Le mie info</button></h1>

    <h1>Operazione Utente</h1>

    <button id="button1" onclick="location.href='../Operazioni_Generali/Conferenze.php'"> Registrazione Conferenze</button>
    <button id="button1" onclick="location.href='../Operazioni_Generali/ProgrammiGiornalieri.php'"> Calendario Conferenze</button>
    <button id="button1" onclick="location.href='../Operazioni_Generali/VisualizzazioneSessioniPresentazioni.php'"> Visualizzazione Sessioni/presentazioni</button>
    <button id="button1" onclick="location.href='../Operazioni_Generali/ListaPresentazionePreferite.php'"> Lista presentazione preferite</button>
    <button id="button1" onclick="location.href='../Operazioni_Speaker/ModificaCVFotoUni.php'"> Modifica Cv / Foto / Università </button>
    
    <h1>Operazioni Speaker</h1>

    <h3>GESTIONE INFO / VISUALIZZAZIONE TUTORIAL</h3>
    <button id="button1" onclick="location.href='../Operazioni_Speaker/ModificaInserimentoRisorse.php'"> Modifica/Inserimento informazioni Aggiuntive </button>
    <button id="button1" onclick="location.href='../Operazioni_Speaker/TutorialDaPresentare.php'"> Visualizzazione tutorial da presentare </button>
    <h3>CHAT</h3>
    <button id="button1" onclick="location.href='../Chat/ListaChat.php'">Chat </button>

    <form method="post" action="">
        <input type="submit" name="LOG_OUT" value="LOG OUT">
    </form>

    
    <?php
    //Evento invio form LOG_OUT
    if (isset($_POST["LOG_OUT"])) {
        session_destroy();
        Header("Location:../HomePage.php");
    }
    ?>

    </div>

</body>

</html>