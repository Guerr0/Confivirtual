DROP DATABASE IF EXISTS CONFVIRTUAL; 
CREATE DATABASE CONFVIRTUAL;
USE CONFVIRTUAL;

SET GLOBAL EVENT_SCHEDULER = ON;
SET SQL_SAFE_UPDATES=0;
SET @@global.event_scheduler =1;

/* -------------------------------------------------------------*/
/* Definizione delle tabelle SQL			        */

CREATE TABLE CONFERENZA(
	AnnoEdizione YEAR,
    Acronimo VARCHAR(20),
    Nome VARCHAR(30) NOT NULL,
    Logo BLOB,
    Svolgimento ENUM("ATTIVA" , "COMPLETATA") DEFAULT "ATTIVA",
    
	PRIMARY KEY(AnnoEdizione,Acronimo)
	) ENGINE = INNODB ;
    

CREATE TABLE SPONSOR(
	Nome VARCHAR(30) PRIMARY KEY,
	Logo BLOB
        
	) ENGINE = INNODB;
         
        
CREATE TABLE SPONSORIZZAZIONE(
	AnnoEdizioneConferenza YEAR,
    AcronimoConferenza VARCHAR(20),
    NomeSponsor VARCHAR(30) ,
    Importo FLOAT NOT NULL,
    
    PRIMARY KEY(AnnoEdizioneConferenza,AcronimoConferenza,NomeSponsor),
    FOREIGN KEY(AnnoEdizioneConferenza,AcronimoConferenza) REFERENCES CONFERENZA(AnnoEdizione,Acronimo) on delete cascade,
	FOREIGN KEY(NomeSponsor) REFERENCES SPONSOR(Nome) on delete cascade
    
    )ENGINE = INNODB;
    

CREATE TABLE PROGRAMMAGIORNALIERO(
	ID INT AUTO_INCREMENT ,
	Data DATE NOT NULL ,
    AnnoEdizioneConferenza YEAR NOT NULL ,
    AcronimoConferenza VARCHAR(20) NOT NULL,
    
    PRIMARY KEY(ID),
    UNIQUE KEY idUnico (Data,AnnoEdizioneConferenza,AcronimoConferenza) ,
	FOREIGN KEY(AnnoEdizioneConferenza,AcronimoConferenza) REFERENCES CONFERENZA(AnnoEdizione,Acronimo) on delete cascade

    )ENGINE = INNODB;
    
    
CREATE TABLE SESSIONE(
	Codice CHAR(8) PRIMARY KEY,
    Titolo VARCHAR(100), 
    LinkPartecipazione VARCHAR(100),
    OraInizio TIME,
    OraFine TIME,
    IdProgramma INT,
	NumeroPresentazioni INT DEFAULT 0,
    FOREIGN KEY(IdProgramma) REFERENCES PROGRAMMAGIORNALIERO(ID) on delete cascade
    )ENGINE = INNODB;
    

CREATE TABLE UTENTE(
	Username VARCHAR(30) PRIMARY KEY, 
    Nome VARCHAR(30), 
    Cognome VARCHAR(30), 
    Password VARCHAR(12), 
    LuogoNascita VARCHAR(30), 
    DataNascita DATE
)ENGINE = INNODB;
    

CREATE TABLE AMMINISTRATORE(
	UsernameAmministratore VARCHAR(30) PRIMARY KEY, 
    FOREIGN KEY(UsernameAmministratore) REFERENCES UTENTE(Username)
)ENGINE = INNODB;


CREATE TABLE PRESENTER(
	UsernamePresenter VARCHAR(30) PRIMARY KEY, 
    Foto BLOB, 
    Curriculum VARCHAR(30), 
    NomeDipartimento VARCHAR(30), 
    NomeUniversita VARCHAR(40), 
    FOREIGN KEY(UsernamePresenter) REFERENCES UTENTE(Username)
)ENGINE = INNODB;


CREATE TABLE SPEAKER(
	UsernameSpeaker VARCHAR(30) PRIMARY KEY, 
    Foto BLOB, 
    Curriculum VARCHAR(30), 
    NomeDipartimento VARCHAR(30), 
    NomeUniversita VARCHAR(40), 
    FOREIGN KEY (UsernameSpeaker) REFERENCES UTENTE(Username)
)ENGINE = INNODB;



CREATE TABLE PRESENTAZIONE(
	Codice CHAR(7) PRIMARY KEY,
    NumeroSequenza INT, 
    CodiceSessione CHAR(8),
    OraInizio TIME,
    OraFine TIME,
    
    FOREIGN KEY(CodiceSessione) REFERENCES SESSIONE(Codice) on delete cascade,
    UNIQUE KEY presentNonSovrapposta (CodiceSessione,NumeroSequenza)
    
    )ENGINE = INNODB;
    
    
    
CREATE TABLE ARTICOLO(
	CodiceArticolo CHAR(7) PRIMARY KEY ,
    Titolo VARCHAR(100),
    StatoSvolgimento ENUM("COPERTO" , "NON COPERTO") DEFAULT "NON COPERTO",
    FilePDF BLOB,
    NumeroPagine INT,
    UsernamePresenter VARCHAR(30),
    
    FOREIGN KEY(CodiceArticolo) REFERENCES PRESENTAZIONE(Codice) on delete cascade,
    FOREIGN KEY(UsernamePresenter) REFERENCES PRESENTER(UsernamePresenter)
    ) ENGINE = INNODB;
    
    
CREATE TABLE TUTORIAL(
	CodiceTutorial CHAR(7) PRIMARY KEY,
    Titolo VARCHAR(200), 
    Abstract VARCHAR(500),
	
    FOREIGN KEY(CodiceTutorial) REFERENCES PRESENTAZIONE(Codice) on delete cascade
) ENGINE = INNODB;



CREATE TABLE PRESENTAZIONE_TUTORIAL(
	CodiceTutorial CHAR(7) ,
    UsernameSpeaker VARCHAR(30),
    
    PRIMARY KEY(CodiceTutorial,Usernamespeaker),
    FOREIGN KEY(CodiceTutorial) REFERENCES TUTORIAL(CodiceTutorial ) on delete cascade,
    FOREIGN KEY(UsernameSpeaker) REFERENCES SPEAKER(UsernameSpeaker)
    ) ENGINE = INNODB;
    
    
CREATE TABLE AUTORE(
	Nome VARCHAR(30), 
    Cognome VARCHAR(30),
    PRIMARY KEY(Nome, Cognome)
)ENGINE = INNODB;


CREATE TABLE LISTA_PUBBLICAZIONI(
	NomeAutore VARCHAR(30), 
    CognomeAutore VARCHAR(30),
    CodiceArticolo CHAR(7), 
    PRIMARY KEY(NomeAutore, CognomeAutore, CodiceArticolo) ,
    FOREIGN KEY(NomeAutore,CognomeAutore) REFERENCES AUTORE(Nome,Cognome) on delete cascade , 
    FOREIGN KEY(CodiceArticolo) REFERENCES ARTICOLO(CodiceArticolo) on delete cascade
)ENGINE = INNODB;


CREATE TABLE PAROLACHIAVE(
	Parola VARCHAR(20) PRIMARY KEY
    )ENGINE = INNODB;
    

CREATE TABLE LISTA_PAROLE_CHIAVE(
	CodiceArticolo CHAR(7), 
    ParolaChiave VARCHAR(20),
    PRIMARY KEY(CodiceArticolo, ParolaChiave), 
    FOREIGN KEY(CodiceArticolo) REFERENCES ARTICOLO(CodiceArticolo) on delete cascade, 
    FOREIGN KEY(ParolaChiave) REFERENCES PAROLACHIAVE(Parola)
)ENGINE = INNODB;


CREATE TABLE CREAZIONECONFERENZA(
	UsernameAmministratore VARCHAR(30), 
    AnnoEdizConf YEAR, 
    AcronimoConf VARCHAR(20), 
    PRIMARY KEY(UsernameAmministratore,AnnoEdizConf,AcronimoConf),
    FOREIGN KEY(UsernameAmministratore) REFERENCES AMMINISTRATORE(UsernameAmministratore) , 
    FOREIGN KEY (AnnoEdizConf,AcronimoConf) REFERENCES CONFERENZA(AnnoEdizione,Acronimo) on delete cascade
    )ENGINE = INNODB;
    
    
CREATE TABLE REGISTRAZIONE(
	UsernameUtente VARCHAR(30), 
    AcronimoConf VARCHAR(20), 
    AnnoEdiziConf YEAR, 
    PRIMARY KEY(UsernameUtente, AcronimoConf, AnnoEdiziConf),
    FOREIGN KEY(UsernameUtente) REFERENCES UTENTE(Username),
    FOREIGN KEY(AnnoEdiziConf,AcronimoConf) REFERENCES CONFERENZA(AnnoEdizione,Acronimo) on delete cascade
)ENGINE = INNODB;

    
CREATE TABLE LISTA_PRESENTAZIONI_PREFERITE(
	CodicePresentazione CHAR(7), 
    UsernameUtente VARCHAR(30), 
    PRIMARY KEY(CodicePresentazione, UsernameUtente),
    FOREIGN KEY(CodicePresentazione) REFERENCES PRESENTAZIONE(Codice) on delete cascade,
    FOREIGN KEY(UsernameUtente) REFERENCES UTENTE(Username)
)ENGINE = INNODB;


CREATE TABLE VALUTAZIONE(
	UsernameAmministratore VARCHAR (30), 
    CodicePresentazione CHAR(7), 
    Voto INT CHECK(Voto>=0 and Voto<=10), 
    Note VARCHAR(50), 
    PRIMARY KEY(UsernameAmministratore, CodicePresentazione), 
    FOREIGN KEY (CodicePresentazione) REFERENCES PRESENTAZIONE(Codice) on delete cascade, 
    FOREIGN KEY (UsernameAmministratore) REFERENCES AMMINISTRATORE(UsernameAmministratore)
)ENGINE = INNODB;


CREATE TABLE INFORMAZIONEAGGIUNTIVA(
	Link VARCHAR(100) PRIMARY KEY,
    Descrizione VARCHAR(255), 
    CodiceTutorial CHAR (7), 
    UsernameSpeaker VARCHAR(30),
    FOREIGN KEY(CodiceTutorial) REFERENCES TUTORIAL(CodiceTutorial) on delete cascade, 
    FOREIGN KEY(UsernameSpeaker) REFERENCES SPEAKER(UsernameSpeaker)
)ENGINE = INNODB;

CREATE TABLE MESSAGGIO(
	DataInserimento DATETIME, 
    UsernameUtente VARCHAR(30), 
    Testo VARCHAR(255), 
    CodiceSessione CHAR(8), 
    PRIMARY KEY(DataInserimento, UsernameUtente), 
    FOREIGN KEY(CodiceSessione) REFERENCES SESSIONE(Codice) on delete cascade, 
    FOREIGN KEY(UsernameUtente) REFERENCES UTENTE(Username)
)ENGINE = INNODB;



/*--------------------------------------------------------------*/
						#STORED PROCEDURE

#OPERAZIONI CHE RIGUARDANO TUTTI GLI UTENTI
#Registrare alla piattaforma un utente

START TRANSACTION
DELIMITER |
CREATE PROCEDURE registrazioneUtente(IN Username VARCHAR(30), Nome varchar(30), Cognome VARCHAR(30),Password VARCHAR(12), LuogoNascita VARCHAR(30), DataNascita DATE)
BEGIN 
INSERT INTO UTENTE() VALUES (Username, Nome, Cognome, Password, LuogoNascita, DataNascita);
END |
DELIMITER ;
COMMIT;
			

#Registrare alla piattaforma un Amministratore
START TRANSACTION 
DELIMITER | 
CREATE PROCEDURE registrazioneAmministratore(IN UsernameAmministratore VARCHAR (30))
BEGIN
INSERT INTO AMMINISTRATORE() VALUES(UsernameAmministratore);
END |
DELIMITER ;
COMMIT;

#Registrare alla piattaforma uno Speaker
START TRANSACTION
DELIMITER |
CREATE PROCEDURE registrazioneSpeaker(IN UsernameSpeaker VARCHAR(30), Foto BLOB, Curriculum VARCHAR(30), NomeDipartimento VARCHAR(30), NomeUniversita VARCHAR(40))
BEGIN 
INSERT INTO SPEAKER() VALUES(UsernameSpeaker, Foto, Curriculum, NomeDipartimento, NomeUniversita);
END |
DELIMITER ;
COMMIT;

#Registrare alla piattaforma un Presenter
START TRANSACTION
DELIMITER |
CREATE PROCEDURE registrazionePresenter(IN UsernamePresenter VARCHAR(30), Foto BLOB, Curriculum VARCHAR(30), NomeDipartimento VARCHAR(30), NomeUniversita VARCHAR(40))
BEGIN
INSERT INTO PRESENTER() VALUES(UsernamePresenter, Foto, Curriculum, NomeDipartimento, NomeUniversita);
END | 
DELIMITER ;
COMMIT;

#Registrazione ad una conferenza 
START TRANSACTION 
DELIMITER | 
CREATE PROCEDURE registrazioneConferenza(IN UsernameUtente VARCHAR(30) , AcronimoConf VARCHAR(20), AnnoEdiziConf YEAR)
BEGIN 
INSERT INTO REGISTRAZIONE() VALUES (UsernameUtente, AcronimoConf, AnnoEdiziConf);
END | 
DELIMITER ;
COMMIT;

#Inserimento messaggi 
START TRANSACTION 
DELIMITER | 
CREATE PROCEDURE inserimentoMessaggio(IN DataInserimento DATETIME, UsernameUtente VARCHAR(30), Testo VARCHAR(255), CodiceSessione CHAR(8))
BEGIN 
INSERT INTO MESSAGGIO() VALUES(DataInserimento, UsernameUtente, Testo, CodiceSessione);
END | 
DELIMITER ;
COMMIT;

#Inserimento lista presentazioni favorite
START TRANSACTION 
DELIMITER |
CREATE PROCEDURE inserimentoPresentazioniFavorite(IN CodicePresentazione CHAR(7), UsernameUtente VARCHAR(30))
BEGIN 
INSERT INTO LISTA_PRESENTAZIONI_PREFERITE() VALUES (CodicePresentazione, UsernameUtente);
END | 
DELIMITER ;
COMMIT;

#Modifica info personali di un utente
START TRANSACTION 
DELIMITER | 
CREATE PROCEDURE modificaInfoPersonali(IN ThisUsername VARCHAR(30) ,NewNome VARCHAR(30), NewCognome VARCHAR(30), NewPassword VARCHAR(30), NewLuogoNascita VARCHAR(40) , NewDataNascita VARCHAR(40))
BEGIN 
UPDATE UTENTE
SET 
	Nome=NewNome, 
    Cognome=NewCognome, 
    Password=NewPassword,
    LuogoNascita=NewLuogoNascita,
    DataNascita=NewDataNascita
WHERE Username=ThisUsername;
END |
DELIMITER ;
COMMIT;


#OPERAZIONI CHE RIGUARDANO SOLO GLI UTENTI AMMINISTRATORI

#Creazione di una nuova conferenza + inserimento in CREAZIONECONFERENZA() + registrazione alla conferenza
START TRANSACTION 
DELIMITER | 
CREATE PROCEDURE creaConferenza(IN UsernameAmministratore VARCHAR(30), AnnoEdizConf YEAR, AcronimoConf VARCHAR(20), Nome VARCHAR(30), Logo BLOB)
BEGIN 
INSERT INTO CONFERENZA(AnnoEdizione,Acronimo,Nome,Logo) VALUES (AnnoEdizConf, AcronimoConf,Nome,Logo);
INSERT INTO CREAZIONECONFERENZA() VALUES (UsernameAmministratore, AnnoEdizConf, AcronimoConf);
END | 
DELIMITER ;
COMMIT;

#Eliminazione di una conferenza 
START TRANSACTION 
DELIMITER | 
CREATE PROCEDURE eliminaConferenza(IN AnnoEdizione YEAR, Acronimo VARCHAR(20))
BEGIN 
DELETE FROM CONFERENZA WHERE(CONFERENZA.AnnoEdizione = AnnoEdizione AND CONFERENZA.Acronimo = Acronimo);
IF (ROW_COUNT() = 0) then SIGNAL SQLSTATE '45000';
END IF;
END | 
DELIMITER ;
COMMIT;

#Associazione conferenza amministratore 
#Ridondante ma necessario
START TRANSACTION 
DELIMITER | 
CREATE PROCEDURE conferenzaAmministratoreAssoc(IN UsernameAmministratore VARCHAR(30), AnnoEdizConf YEAR, AcronimoConf VARCHAR(20))
BEGIN 
INSERT INTO CREAZIONECONFERENZA(UsernameAmministratore, AnnoEdizConf, AcronimoConf) VALUES (UsernameAmministratore, AnnoEdizConf, AcronimoConf);
END | 
DELIMITER ;
COMMIT;

#Procedura per l'inserimento di un PROGRAMMAGIORNALIERO nella tabella 
START TRANSACTION 
DELIMITER |
CREATE PROCEDURE inserisciProgrammaGiornaliero (IN Data DATE, AnnoEdizioneConferenza YEAR, AcronimoConferenza VARCHAR(20))
BEGIN
INSERT INTO PROGRAMMAGIORNALIERO(Data, AnnoEdizioneConferenza, AcronimoConferenza) VALUES (Data, AnnoEdizioneConferenza, AcronimoConferenza);
END | 
DELIMITER ;
COMMIT;

#Creazione di una nuova sessione della conferenza
START TRANSACTION 
DELIMITER |
CREATE PROCEDURE creazioneSessione(IN Codice CHAR(8), Titolo VARCHAR(100), LinkPartecipazione VARCHAR(100),OraInizio TIME, OraFine TIME, IdProgramma INT)
BEGIN 
INSERT INTO SESSIONE(Codice,Titolo,LinkPartecipazione,OraInizio,OraFine,IdProgramma) VALUES(Codice, Titolo, LinkPartecipazione, OraInizio, OraFine, IdProgramma); 
END | 
DELIMITER ;
COMMIT;


#Eliminazione di una sessione
START TRANSACTION 
DELIMITER | 
CREATE PROCEDURE eliminaSessione(IN Codice CHAR(8))
BEGIN 
DELETE FROM SESSIONE WHERE (SESSIONE.Codice = Codice);
IF (ROW_COUNT() = 0) then SIGNAL SQLSTATE '45000';
END IF;
END | 
DELIMITER ;
COMMIT;

#Eliminazione di una presentazione
START TRANSACTION 
DELIMITER | 
CREATE PROCEDURE eliminaPresentazione(IN Codice CHAR(7))
BEGIN 
DELETE FROM PRESENTAZIONE WHERE (PRESENTAZIONE.Codice = Codice);
IF (ROW_COUNT() = 0) then SIGNAL SQLSTATE '45000';
END IF;
END | 
DELIMITER ;
COMMIT;

#Inserimento delle presentazioni in una sessione 
START TRANSACTION 
DELIMITER | 
CREATE PROCEDURE inserimentoPresentazione(IN Codice CHAR(7), NumeroSequenza INT, CodiceSessione CHAR(8), OraInizio TIME, OraFine TIME)
BEGIN 
INSERT INTO PRESENTAZIONE() VALUES(Codice, NumeroSequenza, CodiceSessione, OraInizio, OraFine);
END | 
DELIMITER ;
COMMIT;

#Inserimento di un tutorial 
START TRANSACTION 
DELIMITER | 
CREATE PROCEDURE inserimentoTutorial(IN CodiceTutorial CHAR(7), Titolo VARCHAR(200), Abstract VARCHAR(500))
BEGIN 
INSERT INTO TUTORIAL() VALUES (CodiceTutorial, Titolo, Abstract);
END | 
DELIMITER ;
COMMIT;

#Associazione di uno speaker alla presentazione di un tutorial
START TRANSACTION 
DELIMITER |
CREATE PROCEDURE speakerTutorialAssoc(IN CodiceTutorial CHAR(7), UsernameSpeaker VARCHAR(30))
BEGIN
INSERT INTO PRESENTAZIONE_TUTORIAL() VALUES (CodiceTutorial, UsernameSpeaker);
END | 
DELIMITER ;
COMMIT;

#Inserimento articolo 
START TRANSACTION 
DELIMITER | 
CREATE PROCEDURE inserimentoArticolo(IN CodiceArticolo CHAR(7), Titolo VARCHAR(100), FilePDF BLOB, NumeroPagine INT)
BEGIN 
INSERT INTO ARTICOLO (CodiceArticolo, Titolo, FilePDF, NumeroPagine) VALUES (CodiceArticolo, Titolo, FilePDF, NumeroPagine);
END | 
DELIMITER ; 
COMMIT;

#Associazione di un presenter alla presentazione di un articolo 
START TRANSACTION 
DELIMITER | 
CREATE PROCEDURE presenterArticoloAssoc(IN CodiceArticolo CHAR(7), UsernamePresenter VARCHAR(30))
BEGIN
UPDATE ARTICOLO SET ARTICOLO.UsernamePresenter = UsernamePresenter WHERE (ARTICOLO.CodiceArticolo = CodiceArticolo);
END | 
DELIMITER ;
COMMIT;

#Inserimento delle valutazioni sulle presentazioni 
START TRANSACTION 
DELIMITER |
CREATE PROCEDURE inserisciValutazione(IN Note VARCHAR(50), Voto INT, UserAdmin VARCHAR(20), CodicePresentazione CHAR(7))
BEGIN
INSERT INTO VALUTAZIONE() VALUES (UserAdmin, CodicePresentazione,Voto, Note);
END | 
DELIMITER ;
COMMIT;

#Aggiunta di un amministratore per la gestione di una conferenza 
START TRANSACTION 
DELIMITER |
CREATE PROCEDURE aggiungiAmministratore(IN AcronimoConf VARCHAR(20), AnnoEdizioneConf YEAR, UsernameAmministratore VARCHAR(20))
BEGIN
INSERT INTO CREAZIONECONFERENZA() VALUES (UsernameAmministratore, AnnoEdizioneConf, AcronimoConf);
END | 
DELIMITER ;
COMMIT;


#Inserimento di uno sponsor
START TRANSACTION 
DELIMITER | 
CREATE PROCEDURE insertSponsor(IN Nome VARCHAR(30), Logo BLOB) 
BEGIN 
INSERT INTO SPONSOR() VALUES (Nome, Logo);
END |
DELIMITER ;
COMMIT;

#Inserimento di una sponsorizzazione
START TRANSACTION 
DELIMITER | 
CREATE PROCEDURE inserimentoSponsorizzazione (IN AnnoEdizioneConferenza YEAR, AcronimoConferenza VARCHAR(20), NomeSponsor VARCHAR(30), Importo FLOAT)
BEGIN 
INSERT INTO SPONSORIZZAZIONE() VALUES(AnnoEdizioneConferenza, AcronimoConferenza, NomeSponsor, Importo);
END |
DELIMITER ;
COMMIT;

#Inserimento parola chiave 
START TRANSACTION 
DELIMITER | 
CREATE PROCEDURE inserimentoParolaChiave(IN Parola VARCHAR(20)) 
BEGIN 
INSERT INTO PAROLACHIAVE() VALUES (Parola);
END |
DELIMITER ;
COMMIT;

#Articolo e parola chiave. Lista_Parola_chiave.
START TRANSACTION 
DELIMITER | 
CREATE PROCEDURE articoloParolaChiaveAssoc(IN CodiceArticolo CHAR(7), ParolaChiave VARCHAR(20)) 
BEGIN 
INSERT INTO LISTA_PAROLE_CHIAVE() VALUES(CodiceArticolo, ParolaChiave); 
END | 
DELIMITER ;
COMMIT;

#presentazione_articolo (chiedere a Franci perchè non l'abbiamo modellato come una tabella) 

#Inserimento autore
START TRANSACTION 
DELIMITER | 
CREATE PROCEDURE inserimentoAutore(IN Nome VARCHAR(30), Cognome VARCHAR(30))
BEGIN 
INSERT INTO AUTORE() VALUES (Nome, Cognome);
END | 
DELIMITER ;
COMMIT;

#Inserimento autore
START TRANSACTION 
DELIMITER | 
CREATE PROCEDURE autoreArticoloAssoc(IN NomeAutore VARCHAR(30), CognomeAutore VARCHAR(30) , CodiceArticolo CHAR(7))
BEGIN 
INSERT INTO LISTA_PUBBLICAZIONI() VALUES (NomeAutore, CognomeAutore , CodiceArticolo);
END | 
DELIMITER ;
COMMIT;


#OPERAZIONI CHE RIGUARDANO SOLO GLI UTENTI PRESENTER

#Modifica del CV/foto ed affiliazione universitaria
START TRANSACTION 
DELIMITER | 
CREATE PROCEDURE modificaCVFotoUniPresenter(IN ThisUsernamePresenter VARCHAR(30), NewPresenterFoto BLOB, NewPresenterCurriculum VARCHAR(30), NewPresenterNomeDipartimento VARCHAR(30), NewPresenterNomeUniversita VARCHAR(40))
BEGIN 
UPDATE PRESENTER 
SET Foto=NewPresenterFoto, 
	UsernamePresenter=ThisUsernamePresenter,
	Curriculum=NewPresenterCurriculum, 
    NomeDipartimento=NewPresenterNomeDipartimento, 
    NomeUniversita=NewPresenterNomeUniversita
WHERE ThisUsernamePresenter=UsernamePresenter;
END |
DELIMITER ;
COMMIT;

#Modifica del CV ed affiliazione universitaria
START TRANSACTION 
DELIMITER | 
CREATE PROCEDURE modificaCVUniPresenter(IN ThisUsernamePresenter VARCHAR(30), NewPresenterCurriculum VARCHAR(30), NewPresenterNomeDipartimento VARCHAR(30), NewPresenterNomeUniversita VARCHAR(40))
BEGIN 
UPDATE PRESENTER 
SET 
	UsernamePresenter=ThisUsernamePresenter,
	Curriculum=NewPresenterCurriculum, 
    NomeDipartimento=NewPresenterNomeDipartimento, 
    NomeUniversita=NewPresenterNomeUniversita
WHERE ThisUsernamePresenter=UsernamePresenter;
END |
DELIMITER ;
COMMIT;



#OPERAZIONI CHE RIGUARDANO SOLO GLI UTENTI SPEAKER 
#Modifica del CV/foto ed affiliazione universitaria
START TRANSACTION 
DELIMITER | 
CREATE PROCEDURE modificaCVFotoUniSpeaker(IN ThisUsernameSpeaker VARCHAR(30), NewSpeakerFoto BLOB, NewSpeakerCurriculum VARCHAR(30), NewSpeakerNomeDipartimento VARCHAR(30), NewSpeakerNomeUniversita VARCHAR(40))
BEGIN 
UPDATE SPEAKER
SET Foto=NewSpeakerFoto, 
	Curriculum=NewSpeakerCurriculum, 
    NomeDipartimento=NewSpeakerNomeDipartimento, 
    NomeUniversita=NewSpeakerNomeUniversita
WHERE UsernameSpeaker = ThisUsernameSpeaker;
END |
DELIMITER ;
COMMIT;

#Modifica del CV ed affiliazione universitaria
START TRANSACTION 
DELIMITER | 
CREATE PROCEDURE modificaCVUniSpeaker(IN ThisUsernameSpeaker VARCHAR(30), NewSpeakerCurriculum VARCHAR(30), NewSpeakerNomeDipartimento VARCHAR(30), NewSpeakerNomeUniversita VARCHAR(40))
BEGIN 
UPDATE SPEAKER
SET 
	Curriculum=NewSpeakerCurriculum, 
    NomeDipartimento=NewSpeakerNomeDipartimento, 
    NomeUniversita=NewSpeakerNomeUniversita
WHERE UsernameSpeaker = ThisUsernameSpeaker;
END |
DELIMITER ;
COMMIT;


#Inserimento/modifica delle risorse aggiuntive per il proprio tutorial 
START TRANSACTION
DELIMITER | 
CREATE PROCEDURE inserisciRisorsaAggiuntiva(IN Link VARCHAR(100), Descrizione VARCHAR(255), CodiceTutorial CHAR(7), UsernameSpeaker VARCHAR(30))
BEGIN 
INSERT INTO INFORMAZIONEAGGIUNTIVA() VALUES(Link, Descrizione, CodiceTutorial, UsernameSpeaker);
END | 
DELIMITER ;
COMMIT;


#Elimina risorse aggiuntive per il proprio tutorial
START TRANSACTION
DELIMITER |
CREATE PROCEDURE eliminaRisorsaAggiuntiva(IN ThisLink VARCHAR(100))
BEGIN
DELETE FROM INFORMAZIONEAGGIUNTIVA WHERE Link=ThisLink;
END |
DELIMITER ;
COMMIT;


#TRIGGER 
#Utilizzare un trigger per implementare l'operazione cambio di stato_svolgimento di una presentazione di articolo, portandolo 
#da "non coperto" a "coperto" quando si inserisce un presenter valido per quella presentazione
START TRANSACTION 
DELIMITER | 
CREATE TRIGGER CambiamentoStatoSvolgimento 
BEFORE UPDATE ON ARTICOLO 
FOR EACH ROW
BEGIN 
SET NEW.StatoSvolgimento = "COPERTO";
END | 
DELIMITER ;
COMMIT;

#Utilizzare un trigger per implementare l'operazione di aggiornamento del campo #numero_presentazioni ogni qualvolta si aggiunge
#una nuova presentazione ad una sessione della conferenza. 

START TRANSACTION
DELIMITER |
CREATE TRIGGER AggiornaNumeroPresentazioni
AFTER INSERT ON PRESENTAZIONE
FOR EACH ROW
BEGIN
	UPDATE SESSIONE
	SET SESSIONE.NumeroPresentazioni = SESSIONE.NumeroPresentazioni + 1
    WHERE (SESSIONE.Codice = NEW.CodiceSessione);
END |
DELIMITER ;
COMMIT;



#EVENTO
#Utilizzare un event per modificare il campo svolgimento di una conferenza. L'evento setta il campo a "Completata" non appena
#la data corrente eccede di un giorno l'ultima data di svolgimento di una conferenza

CREATE VIEW dataMax(AnnoEdizioneConferenza,AcronimoConferenza,DataUltima) AS (
	SELECT AnnoEdizioneConferenza, AcronimoConferenza, MAX(Data) AS DataUltima
    FROM PROGRAMMAGIORNALIERO, CONFERENZA 
    WHERE (PROGRAMMAGIORNALIERO.AcronimoConferenza = CONFERENZA.Acronimo)
    AND (PROGRAMMAGIORNALIERO.AnnoEdizioneConferenza = CONFERENZA.AnnoEdizione)
    GROUP BY PROGRAMMAGIORNALIERO.AnnoEdizioneConferenza,PROGRAMMAGIORNALIERO.AcronimoConferenza
);
SELECT * FROM dataMax;

DELIMITER $
CREATE EVENT CambioStatoSvolgimento
ON SCHEDULE EVERY 1 MINUTE
DO 
BEGIN
UPDATE CONFERENZA,dataMax
	SET CONFERENZA.Svolgimento = "COMPLETATA"
	WHERE(CONFERENZA.Acronimo = dataMax.AcronimoConferenza)
	AND (CONFERENZA.AnnoEdizione = dataMax.AnnoEdizioneConferenza)
	AND (dataMax.DataUltima < CURRENT_DATE());
END $
DELIMITER ; 

