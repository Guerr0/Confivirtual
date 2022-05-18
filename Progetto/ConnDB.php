<?php

//La classe ConnDB permette grazie al suo metodo statico di restituire un oggetto PDO e stabilire una connessione al DB MySql
class ConnDb
   {
 
      public static function connToDb(){
        try {
            $pdo=new PDO('mysql:host=localhost;dbname=confvirtual','root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
         }
         catch(PDOException $e) {
            echo("[ERRORE] Connessione al DB non riuscita. Errore: ".$e->getMessage());
            exit();
         }
         return $pdo;
      }
   }

?>
