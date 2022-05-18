<?php require_once "ConnDb.php"; ?>
<html lang="it">

<head>
  <title> Confvirtual </title>
</head>
<link rel="stylesheet" href="HomePageStyles.css">

<body>

  <h1> BENVENUTO IN CONFVIRTUAL!! </h1>

  <div class="buttonSection2">
    <!-- Bottone per passare alla pagina di login  --> 
    <button id="button1" onclick="location.href='Login.php'"> Accedi </button>
  </div>
  
  <div class="buttonSection">

    <!-- Form per scegliere il tipo di utente per la registrazione-->
    <form id="form" method="post" action="HomePage.php">
      <input type="submit" id="submit" value="Registrati"> </input>
      <select name="tipoUtente" id="tipoUtente">
        <option value="UtenteNormale">UtenteNormale</option>
        <option value="Speaker">Speaker</option>
        <option value="Presenter">Presenter</option>
        <option value="Amministratore">Amministratore</option>
      </select>
    </form>
  </div>

  <br>
  <h3> DESCRIZIONE </h3>
  <p> Si vuole realizzare la piattaforma CONFVIRTUAL per la gestione di conferenze online durante il
    periodo della pandemia COVID-19. La piattaforma supporta l’organizzazione di conferenze svolte mediante
    video-conferenze da remoto. In particolare, si consente agli utenti organizzatori la creazione di conferenze
    con sessioni di presentazioni di articoli/tutorial, e relativi link alle stanze Teams per la partecipazione alle stesse.
    Gli utenti possono registrarsi alle conferenze, aggiungere i propri dati nel caso di speaker/presenter, interagire con altri
    utenti mediante servizi di messaggistica interni.
  </p>


  <h3> STATISTICHE </h3>


  <?php

  //Submit della registrazione ,  controlla il tipo di utente selezionato per la registrazione e lo indirizza al giusto form.
  if (isset($_POST["tipoUtente"])) {
    $tipoUtente = $_POST['tipoUtente'];
    switch ($tipoUtente) {
      case "UtenteNormale":
        Header("Location:Registrazione/RegistratiUtente.php");
        break;

      case "Speaker":
        Header("Location:Registrazione/RegistratiSpeaker.php");
        break;

      case "Presenter":
        Header("Location:Registrazione/RegistratiPresenter.php");
        break;

      case "Amministratore":
        Header("Location:Registrazione/RegistratiAdmin.php");
        break;
    }
  }


  //STATISTICHE

  $pdo = ConnDb::connToDb();


  # Numero conferenze totali
  try {
    // Query SQL per contare le conferenze
    $countConferenze = "SELECT COUNT(*) AS Numero FROM CONFERENZA";
    $numeroConferenze = $pdo->query($countConferenze);
  } catch (PDOException $e) {
    echo ("[ERRORE] Query SQL non riuscita. Errore: " . $e->getMessage());
    exit();
  }


  //stampo numero conferenze
  echo ("<br>" . "NUMERO CONFERENZE: ");
  $row = $numeroConferenze->fetch();
  echo ($row["Numero"]);



  # Numero conferenze ATTIVE 
  try {
    // Query SQL per contare le conferenze attive
    $countConferenze = "SELECT COUNT(*) AS Numero FROM CONFERENZA WHERE Svolgimento='Attiva'";
    $numeroConferenze = $pdo->query($countConferenze);
  } catch (PDOException $e) {
    echo ("[ERRORE] Query SQL non riuscita. Errore: " . $e->getMessage());
    exit();
  }

  //stampo numero conferenze attive
  echo ("<br>" . "NUMERO CONFERENZE ATTIVE: ");
  $row = $numeroConferenze->fetch();
  echo ($row["Numero"]);



  # Numero UTENTI registrati
  try {
    // Query SQL per leggere gli Utenti registrati alla piattaforma 
    $countConferenze = "SELECT COUNT(*) AS Numero FROM UTENTE";
    $numeroConferenze = $pdo->query($countConferenze);
  } catch (PDOException $e) {
    echo ("[ERRORE] Query SQL non riuscita. Errore: " . $e->getMessage());
    exit();
  }

  //stampo numero degli Utenti
  echo ("<br>" . "NUMERO UTENTI PRESENTI: ");
  $row = $numeroConferenze->fetch();
  echo ($row["Numero"]);



  #Classifica Presenter in base alla valutazione media 
  try {

    $VotoMedioPresentazioni = ("SELECT Presenter.UsernamePresenter AS Nome, AVG(Valutazione.Voto) AS Media
                            FROM Valutazione,Presenter,Articolo
                            WHERE (Valutazione.CodicePresentazione = Articolo.CodiceArticolo)
                            AND (Articolo.UsernamePresenter =Presenter.UsernamePresenter)
                            AND (Presenter.UsernamePresenter = Articolo.UsernamePresenter)
                            GROUP BY Presenter.UsernamePresenter
                            ORDER BY Media DESC");
    $arrayValutazionePresentazioniMedio = $pdo->query($VotoMedioPresentazioni);
  } catch (PDOException $e) {
    echo ("[ERRORE] Query SQL non riuscita. Errore: " . $e->getMessage());
    exit();
  }

    //stampo la classifica dei presenter
    echo ("<br>" . "<u> CLASSIFICA PRESENTER IN BASE AL VOTO: </u>");
    $contatore = 1;
    foreach ($arrayValutazionePresentazioniMedio as $row) {
      echo (" <br>" . $contatore . ") " . $row["Nome"]);
      echo ("  " . $row["Media"]);
      $contatore = $contatore + 1;
    }

  # Classifica speaker in base alla valutazione media 
  try {

    $VotoMedioPresentazioni2 = ("SELECT Speaker.UsernameSpeaker AS Nome, AVG(Valutazione.Voto) AS Media
                            FROM Valutazione,Speaker,PRESENTAZIONE_TUTORIAL
                            WHERE (Valutazione.CodicePresentazione = PRESENTAZIONE_TUTORIAL.CodiceTutorial)
                            AND (PRESENTAZIONE_TUTORIAL.UsernameSpeaker =Speaker.UsernameSpeaker)
                            AND (SPEAKER.UsernameSpeaker = PRESENTAZIONE_TUTORIAL.UsernameSpeaker)
                            GROUP BY SPEAKER.UsernameSpeaker
                            ORDER BY Media DESC");
    $arrayValutazionePresentazioniMedio2 = $pdo->query($VotoMedioPresentazioni2);
  } catch (PDOException $e) {
    echo ("[ERRORE] Query SQL non riuscita. Errore: " . $e->getMessage());
    exit();
  }


  echo ("<br" . "-----------------------------------------------");
  //stampo la classifica degli speaker
  echo "<br>";
  echo ("<br><u>CLASSIFICA SPEAKER IN BASE AL VOTO: </u>");
  $contatore = 1;
  foreach ($arrayValutazionePresentazioniMedio2 as $row) {
    echo (" <br>" . $contatore . ") " . $row["Nome"]);
    echo ("  " . $row["Media"]);
    $contatore = $contatore + 1;
  }

  ?>

  </p>
</body>

</html>