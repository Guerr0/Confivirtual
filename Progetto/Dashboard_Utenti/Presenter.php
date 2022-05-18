<?php require_once "../ConnDb.php"; ?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="DashboardStyle.css">
    <title>Presenter</title>
</head>

<body>
    <?php
    session_Start();
    ?>
    <h1> BENTORNATO <?php echo $_SESSION['username']; ?> !! <button id="button1" onclick="location.href='../Operazioni_Generali/VisualizzaInfoPersonali.php'">Le mie info</button> </h1>

    <h1>Operazione Utente</h1>

    <button id="button1" onclick="location.href='../Operazioni_Generali/Conferenze.php'"> Registrazione Conferenze</button>
    <button id="button1" onclick="location.href='../Operazioni_Generali/ProgrammiGiornalieri.php'"> Calendario Conferenze</button>
    <button id="button1" onclick="location.href='../Operazioni_Generali/VisualizzazioneSessioniPresentazioni.php'"> Visualizzazione Sessioni/presentazioni</button>
    <button id="button1" onclick="location.href='../Operazioni_Generali/ListaPresentazionePreferite.php'"> Lista presentazione preferite</button>
    
    
    <h1>Operazioni Presenter</h1>

    <h3>GESTIONE INFO / VISUALIZZAZIONE ARTICOLI</h3>
    <button id="button1" onclick="location.href='../Operazioni_Presenter/ModificaCVFotoUni.php'"> Modifica Cv / Foto / Università </button>
    <button id="button1" onclick="location.href='../Operazioni_Presenter/ArticoloDaPresentare.php'">Articoli da presentare </button>
    
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