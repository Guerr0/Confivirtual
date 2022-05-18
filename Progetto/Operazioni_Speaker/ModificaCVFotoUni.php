<?php require_once "../ConnDb.php"; ?>
<?php require_once "../ConnessioneMongoDB.php"; ?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../Operazioni_Speaker/OperazioniSpeakerStyle.css">
    <title>Inserimento/Modifica informazioni personali</title>
</head>

<body>
    <?php
    ob_start();
    session_Start();
    $nome = $_SESSION['username'];
    ?>

    <p><br><a href="../Dashboard_Utenti/Speaker.php"> TORNA ALLA DASHBOARD </a></p>
    <h1> INSERIMENTO E MODIFICA DEL CV , FOTO E AFFILIZIAZIONE UNIVERSITARIA </h1>

    <!--Inserimento dati  -->
    <form class="form" action="" method="post" style="margin: 150px;" enctype='multipart/form-data'>
        <?php
        #RECUPERO INFORMAZIONI PERSONALI
        $pdo = ConnDb::connToDb();
        try {
            $selectInfoSpeaker = "SELECT Curriculum,NomeDipartimento,NomeUniversita,Foto FROM SPEAKER WHERE UsernameSpeaker='$nome'";
            $arrayInfo = $pdo->query($selectInfoSpeaker);

            $row = $arrayInfo->fetch();

            $nomeUniversità = $row["NomeUniversita"];
            $nomeDipartimento = $row["NomeDipartimento"];
            $curriculumCV = $row["Curriculum"];
            $fotoBIN = $row["Foto"];
            $foto = base64_encode($row["Foto"]);
            echo ('<img src="data:image/jpeg;base64,' . $foto . '"height="100" width="100" />');


            echo ("<table border='1'>"
                . "<caption>LE MIE INFO</caption>"
                . "<tr>"
                . "<th> Username</th>"
                . "<th> Curriculum </th>"
                . "<th> Nome Università </th>"
                . "<th> Nome Dipartimento </th>"
                . "</tr>");
            echo ("<tr>"
                . "<td>" . $nome . "</td>"
                . "<td>" . $curriculumCV . "</td>"
                . "<td>" . $nomeUniversità . "</td>"
                . "<td>" . $nomeDipartimento . "</td>"
                
                . "</tr>");

            echo ("</table>");
        } catch (PDOException $e) {
            echo ("[ERRORE] Query SQL non riuscita. Errore: " . $e->getMessage());
            exit();
        }

        ?>

        <h2>Modifica informazioni </h2>
        <input type="text" id="curriculum" placeholder="Curriculum" name="curriculum" maxlength="30" value='<?php echo ($curriculumCV) ?>' required>
        <input type="text" id="nomeDipartimento" placeholder="Nome Dipartimento" name="nomeDipartimento" maxlength="30" value='<?php echo ($nomeDipartimento) ?>' required>
        <input type="text" id="nomeUni" placeholder="Nome università" name="nomeUni" maxlength="40" value='<?php echo ($nomeUniversità) ?>' required>
        <hr> UPLOAD FOTO </hr>
        <p> Aggiungi la foto personale</p>
        <table>
            <tr>
                <td> <b> </b> Seleziona file: </b></td>
                <td><input type='file' name="upload" id="upload">
                <td>
            </tr>
        </table>
        <input type="submit" name="submit" value="INSERISCI" >

    </form>




    <?php
    if (isset($_POST["submit"])) {
        if ($_POST["submit"] == "INSERISCI" && $_FILES["upload"]["size"]>0 ) {
            return InserisciInfoConFoto();
        }
        else InserisciInfoSenzaFoto();
        
    }

    // Funzione che inserisce le informazioni E AGGIORNA FOTO 
    function InserisciInfoConFoto()
    {
        $pdo = ConnDb::connToDb();
        try {

            $curriculum = $_POST['curriculum'];
            $nomeDip = $_POST['nomeDipartimento'];
            $nomeUni = $_POST['nomeUni'];
            $nome = $_SESSION['username'];

            $uploadFile = $_FILES['upload']['tmp_name'];
            $foto = (file_get_contents($uploadFile));

            $sql1 = 'CALL modificaCVFotoUniSpeaker(:nome, :foto, :curriculum, :nomeDipartimento, :nomeUni )';
            $statement1 = $pdo->prepare($sql1);
            $statement1->bindParam(':curriculum', $curriculum, PDO::PARAM_STR);
            $statement1->bindParam(':nomeDipartimento', $nomeDip, PDO::PARAM_STR);
            $statement1->bindParam(':nomeUni', $nomeUni, PDO::PARAM_STR);
            $statement1->bindParam(':nome', $nome, PDO::PARAM_STR);
            $statement1->bindParam(':foto', $foto, PDO::PARAM_LOB);
            $statement1->execute();
            echo '<script language="javascript">';
            echo 'alert("INSERIMENTO AVVENUTO CON SUCCESSO")';
            echo '</script>';
            header("Refresh:0");
            ConnessioneMongoDB::connMongoDB($nome,$sql1);
            ob_end_flush();


        } catch (PDOException $e) {
            echo ("[ERRORE] Query SQL non riuscita. Errore: " . $e->getMessage());
            exit();
        }
    }
    // Funzione che inserisce le informazioni SENZA FOTO
    function InserisciInfoSenzaFoto()
    {
        $pdo = ConnDb::connToDb();
        try {

            $curriculum = $_POST['curriculum'];
            $nomeDip = $_POST['nomeDipartimento'];
            $nomeUni = $_POST['nomeUni'];
            $nome = $_SESSION['username'];

            $sql1 = 'CALL modificaCVUniSpeaker(:nome,:curriculum, :nomeDipartimento, :nomeUni )';
            $statement1 = $pdo->prepare($sql1);
            $statement1->bindParam(':curriculum', $curriculum, PDO::PARAM_STR);
            $statement1->bindParam(':nomeDipartimento', $nomeDip, PDO::PARAM_STR);
            $statement1->bindParam(':nomeUni', $nomeUni, PDO::PARAM_STR);
            $statement1->bindParam(':nome', $nome, PDO::PARAM_STR);
            $statement1->execute();
            echo '<script language="javascript">';
            echo 'alert("INSERIMENTO AVVENUTO CON SUCCESSO")';
            echo '</script>';
            header("Refresh:0");
            ConnessioneMongoDB::connMongoDB($nome,$sql1);
            ob_end_flush();


        } catch (PDOException $e) {
            echo ("[ERRORE] Query SQL non riuscita. Errore: " . $e->getMessage());
            exit();
        }
    }

    ?>


</body>

</html>