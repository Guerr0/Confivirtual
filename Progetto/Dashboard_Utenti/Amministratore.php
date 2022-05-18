<?php require_once "../ConnDb.php"; ?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="DashboardStyle.css">
    <title>Amministratore</title>
</head>

<body>
    <?php
    session_Start();
    ?>



    <h1> BENTORNATO <?php echo $_SESSION['username']; ?> !! <button id="button1" onclick="location.href='../Operazioni_Generali/VisualizzaInfoPersonali.php'">Le mie info</button></h1>

    <h1>Operazioni Utente </h1>
    <button id="button1" onclick="location.href='../Operazioni_Generali/Conferenze.php'">Registrazione Conferenza</button>
    <button id="button1" onclick="location.href='../Operazioni_Generali/ProgrammiGiornalieri.php'"> Calendario Conferenze</button>
    <button id="button1" onclick="location.href='../Operazioni_Generali/VisualizzazioneSessioniPresentazioni.php'"> Visualizzazione Sessioni/presentazioni</button>
    <button id="button1" onclick="location.href='../Operazioni_Generali/ListaPresentazionePreferite.php'"> Lista presentazione preferite</button>

    <br>
    <br>
    <br>


    <h1>Operazioni Amministratore</h1>

    <h3> GESTIONE CONFERENZE / SESSIONI / PRESENTAZIONI</h3>

    <button id="button1" onclick="location.href='../Operazioni_Amministratore/GestioneConferenze.php'">Gestione Conferenze</button>
    <button id="button1" onclick="location.href='../Operazioni_Amministratore/GestioneSessioni.php'">Gestione Sessioni</button>
    <button id="button1" onclick="location.href='../Operazioni_Amministratore/GestionePresentazioni.php'">Gestione Presentazioni</button>
    <button id="button1" onclick="location.href='../Operazioni_Amministratore/ValutazionePresentazioni.php'">Valutazione presentazione</button>
    <button id="button1" onclick="location.href='../Operazioni_Amministratore/AggiungiAmministratore.php'">Aggiungi Amministratore</button>

    <h3> ASSOCIA ARTICOLO / TUTORIAL</h3>
    <button id="button1" onclick="location.href='../Operazioni_Amministratore/AssociazioneSpeakerTutorial.php'">Associazione Tutorial-Speaker</button>
    <button id="button1" onclick="location.href='../Operazioni_Amministratore/AssociazionePresenterArticolo.php'">Associazione Articolo-Presenter</button>
    <button id="button1" onclick="location.href='../Operazioni_Amministratore/AssociazioneAutoreArticolo.php'">Associazione Autore-Articolo</button>
    <button id="button1" onclick="location.href='../Operazioni_Amministratore/AssociazioneArticoloParoleChiave.php'">Associazione Articolo-ParoleChiave</button>

    <h3>CHAT</h3>
    <button id="button1" onclick="location.href='../Chat/ListaChat.php'">Chat </button>


    <h3> SPONSOR </h3>
    <button id="button1" onclick="location.href='../Operazioni_Amministratore/Sponsorizzazione.php'">Gestione sponsor</button>


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