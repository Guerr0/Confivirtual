<?php 

//La classe ConnessioneMongoDB permette grazie al suo metodo statico di inserire un log all'interno di MongoDB  
 class ConnessioneMongoDB
 {
     public static function connMongoDB( $nome,$sql1) {
        $bulk = new MongoDB\Driver\BulkWrite;
                $DateAndTime = date('m-d-Y h:i:s a', time());
                $document1 = ['Utente' =>$nome, 'Call' => $sql1, 'Orario' => $DateAndTime];
                //inserisco i documenti JSON nel writer pronti per andare su mongo
                $_id1 = $bulk->insert($document1);
                //scrivo i documenti nella COLLECTIONPROVA dentro al DATABASETEST
                $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');
                $result = $manager->executeBulkWrite('LogDB.Log', $bulk);
     }
 }
?>
