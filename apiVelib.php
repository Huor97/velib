<?php
//Connexion à la base de données
try{    
    $dsn = 'mysql:dbname=velib;host=127.0.0.1:3306;charset=UTF8';
    $user = 'rouh';
    $password = 'huor';

    $bdd = new PDO($dsn, $user, $password);
}catch (PDOException $e){
     die ('Problème de connexion à la base de données');

}
echo "bien lu\n";
//ECRIRE LE CODE ICI
$selectStation = (getAllCodeVelibStationFromBDD($bdd));

foreach ($selectStation as $key => $value){
    $data = getJsonFromAPI($value['code_station']);
    //print_r( $data );
    // echo $value['code_station'], "<br>";
    (setVelibData($bdd, $value['code_station'], $data));
};
//Insertion de données

//Récupération de la donnée
// $var3 =  getOneVelibStationFromBDD($pdo, $codeStation)


//FONCTIONS
/*
    Récupération de tous les codes stations de Vélib
    @pdo object : variable où l'on a initialisé la base de données
*/
function getAllCodeVelibStationFromBDD($pdo){
    /**
     * requete ajouter les codes stations
     * SELECT `code_station` FROM `stations` WHERE 1
     */ 
    $requete = "SELECT code_station FROM stations WHERE 1";
    $sql = $pdo->prepare($requete);
    $sql->execute();
    if($sql->errorInfo()[0] != 00000 ){
        rint_r($sql->errorInfo());
    }
    return $sql->fetchAll(PDO::FETCH_ASSOC);
}

/*
    Récupération d'une station de Vélib avec les données de disponnibilités
    @pdo object : variable où l'on a initialisé la base de données
*/
function getOneVelibStationFromBDD($pdo, $codeStation){
    /**
     * requete d'une stations de vélib
     * SELECT * FROM `stations` LEFT JOIN `dispo` ON `code_station` = `codeStation_dispo` LIMIT 1 
     */
    $requete = "SELECT * FROM stations LEFT JOIN dispo ON code_station = :codeStation";
    $sql = $pdo->prepare($requete);
    $sql->bindValue(':codeStation', $codeStation, PDO::PARAM_INT);
    $sql->execute();
    if($sql->errorInfo()[0] != 00000 ){
        print_r($sql->errorInfo());
    }
    return $sql->fetch(PDO::FETCH_ASSOC);
}
/*
    Récupération de toutes les stations vélib avec les données de disponnibilités
    @pdo object : variable où l'on a initialisé la base de données
*/
function getAllVelibStationFromBDD($pdo, $codeStation){
    /**
     * requet join pour assambler tout les stations et velib dispo
     * SELECT * FROM `stations` LEFT JOIN `dispo` ON `code_station` = `codeStation_dispo` 
     */
    $requete = "SELECT * FROM stations LEFT JOIN dispo ON code_station = :codeStation";
    $sql = $pdo->prepare($requete);
    $sql->execute();
    if($sql->errorInfo()[0] != 00000 ){
        print_r($sql->errorInfo());
    }
    return $sql->fetchAll(PDO::FETCH_ASSOC);
}

/*
    Ajout ou modification des informations pour une station de Vélib
    @pdo object : variable où l'on a initialisé la base de données
*/
function setVelibData($pdo, $codeStation, $data){

    /**
     * requet mettre à jour les données
     * exemple1: UPDATE `dispo` SET `code_station_dispo` = 4444 WHERE `code_station` = 3242
     * exemple2: UPDATE `dispo` SET `code_station` = 5551 WHERE `nom_station` = 'test2'  
     */
//var_dump(getOneVelibStationFromBDD($pdo, $codeStation));

	$requeteCount = "SELECT COUNT(*) as c FROM dispo where codeStation_dispo= :codeStation";
	$sqlUpdate = $pdo->prepare($requeteCount);
	$sqlUpdate->bindValue(':codeStation', $codeStation,  PDO::PARAM_INT);
	$sqlUpdate->execute();
	//var_dump($sqlUpdate->fetchAll(PDO::FETCH_ASSOC)[0]['c']);
	$nb=$sqlUpdate->fetchAll(PDO::FETCH_ASSOC)[0]['c'];


    if($nb){
        $requeteUpdate = "UPDATE dispo SET ouvert_dispo = :ouvert_dispo, evelo_dispo = :evelo_dispo, velo_dispo = :velo_dispo, total_dispo = :total_dispo, capacite_dispo = :capacite_dispo WHERE codeStation_dispo = :codeStation ";
        $sqlUpdate = $pdo->prepare($requeteUpdate);
        $sqlUpdate->bindValue(':ouvert_dispo', checkIfOpenStation($data->is_renting), PDO::PARAM_INT);
        $sqlUpdate->bindValue(':evelo_dispo', $data->ebike, PDO::PARAM_INT);
        $sqlUpdate->bindValue(':velo_dispo', $data->mechanical, PDO::PARAM_INT);
        $sqlUpdate->bindValue(':total_dispo', $data->numbikesavailable, PDO::PARAM_INT);
        $sqlUpdate->bindValue(':capacite_dispo', $data->capacity, PDO::PARAM_INT);
        $sqlUpdate->bindValue(':codeStation', $codeStation, PDO::PARAM_INT);
        $sqlUpdate->execute();
        if($sqlUpdate->errorInfo()[0] != 00000 ){
            print_r($sqlUpdate->errorInfo());
        }
echo "update\n";    } else {
        /**
         * requet ajouter une stations à la table dispo
         * INSERT INTO `dispo`(`code_station`)
         * VALUES
         * (3242,'blalba')
         */
        $requeteInsert = "INSERT INTO dispo(codeStation_dispo, ouvert_dispo, evelo_dispo, velo_dispo, total_dispo, capacite_dispo)
        VALUE(:codeStation, :ouvert_dispo, :evelo_dispo, :velo_dispo, :total_dispo, :capacite_dispo)
        ";
        $sqlInsert = $pdo->prepare($requeteInsert);
        $sqlInsert->bindValue(':ouvert_dispo', checkIfOpenStation($data->is_renting), PDO::PARAM_INT);
        $sqlInsert->bindValue(':evelo_dispo', $data->ebike, PDO::PARAM_INT);
        $sqlInsert->bindValue(':velo_dispo', $data->mechanical, PDO::PARAM_INT);
        $sqlInsert->bindValue(':total_dispo', $data->numbikesavailable, PDO::PARAM_INT);
        $sqlInsert->bindValue(':capacite_dispo', $data->capacity, PDO::PARAM_INT);
        $sqlInsert->bindValue(':codeStation', $codeStation, PDO::PARAM_INT);
        $sqlInsert->execute();

        if($sqlInsert->errorInfo()[0] != 00000 ){
            print_r($sqlInsert->errorInfo());
        }
   
        echo "insert\n";
    }

}

/*
    Vérification qu'une station est ouverte
    @data string : valeur OUI/NON de l'ouverture d'une station
*/
function checkIfOpenStation($data){
    return $data == "OUI" ? 1 : 0;
}


/* 
    Récupération des données de l'API VELIB
    @codeArret int : code de l'arrêt de vélib
*/
function getJsonFromAPI($codeArret){
    $source = "http://opendata.paris.fr/api/records/1.0/search/?dataset=velib-disponibilite-en-temps-reel&q=&facet=stationcode&refine.stationcode=".$codeArret;
    $ch = curl_init($source);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec ($ch);
    $error = curl_error($ch); 
    curl_close ($ch);

    if($error){
        error_log($error);
        die('Problème pour la récupération de données');
    }
    return json_decode($data)->records[0]->fields;
}
