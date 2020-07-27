<?php
    include "db.config.php";

    function initDB($conn) {
        // sql to create table
        $sql = "CREATE TABLE municipi_stats (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            municipi VARCHAR(30) NOT NULL,
            nom VARCHAR(30) NOT NULL,
            pop_total INT NOT NULL,
            covid_neg INT,
            covid_pos INT,
            covid_pos_100k_raw FLOAT,
            covid_pos_100k_std FLOAT,
            reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        if ($conn->query($sql) === TRUE) {
            echo "<br>Table comarca_stats created successfully";
        } else {
            echo "<br>Error creating table: " . $conn->error;
        }

        // sql to create table
        $sql = "CREATE TABLE comarca_stats (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            comarca VARCHAR(30) NOT NULL,
            nom VARCHAR(30) NOT NULL,
            pop_total INT,
            covid_neg INT,
            covid_pos INT,
            covid_pos_100k_raw FLOAT,
            covid_pos_100k_std FLOAT,
            reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        if ($conn->query($sql) === TRUE) {
            echo "<br>Table comarca_stats created successfully";
        } else {
            echo "<br>Error creating table: " . $conn->error;
        }
    }

    function getMuncipiData($municipi, $conn) {
        echo "<br>Get Data for municipi: ".$municipi;
        $xml = file_get_contents("https://arcgis.aquas.cat/server/rest/services/Public/Positius___WA1___Taxa_estand_positius_10k_hab_ext/FeatureServer/14/query?f=json&objectIds=".$municipi."&outFields=POB_TOTAL%2Ccovidnegatiu%2Ccovidpositiu%2Ccovidpositiu_c100k_hab%2Ccovidpositiu_s100k_hab%2Cnommunicipi%2COBJECTID&outSR=102100&returnM=true&returnZ=true&spatialRel=esriSpatialRelIntersects&where=1%3D1");

        $result = json_decode($xml, true);

        if ($result['features'][0]["attributes"]) {
            $pop = $result['features'][0]["attributes"]["POB_TOTAL"];
            $covNeg = $result['features'][0]["attributes"]["covidnegatiu"];
            $covPos = $result['features'][0]["attributes"]["covidpositiu"];
            $cov100KRaw = $result['features'][0]["attributes"]["covidpositiu_c100k_hab"];
            $cov100KStd = $result['features'][0]["attributes"]["covidpositiu_s100k_hab"];
            $nomMunicipi = $result['features'][0]["attributes"]["nommunicipi"];
            $nom = utf8_decode(str_replace("'", "''", $nomMunicipi));

            $sql = "INSERT INTO municipi_stats (municipi, nom, pop_total, covid_neg, covid_pos, covid_pos_100k_raw, covid_pos_100k_std)
            VALUES ('".$municipi."', '".$nom."', '".$pop."', '".$covNeg."', '".$covPos."', '".$cov100KRaw."', '".$cov100KStd."')";

            if ($conn->query($sql) === TRUE) {
                echo "<br> New record added successfully for ". $nomMunicipi . "<br>";
            } else {
                echo "<br>Error adding: " . $sql . "<br>" . $conn->error;
            }
        }
    }

    function getComarcaData($comarca, $conn) {
        echo "<br>Get Data for comarca: ".$comarca;
        $xml = file_get_contents("https://arcgis.aquas.cat/server/rest/services/Public/Positius___WA1___Taxa_estand_positius_10k_hab_ext/FeatureServer/13/query?f=json&objectIds=".$comarca."&outFields=POB_TOTAL%2Ccovidnegatiu%2Ccovidpositiu%2Ccovidpositiu_c100k_hab%2Ccovidpositiu_s100k_hab%2Cnomcomarca%2COBJECTID&outSR=102100&returnM=true&returnZ=true&spatialRel=esriSpatialRelIntersects&where=1%3D1");

        $result = json_decode($xml, true);

        if ($result['features'][0]["attributes"]) {
            $pop = $result['features'][0]["attributes"]["POB_TOTAL"];
            $covNeg = $result['features'][0]["attributes"]["covidnegatiu"];
            $covPos = $result['features'][0]["attributes"]["covidpositiu"];
            $cov100KRaw = $result['features'][0]["attributes"]["covidpositiu_c100k_hab"];
            $cov100KStd = $result['features'][0]["attributes"]["covidpositiu_s100k_hab"];
            $nomComarca = $result['features'][0]["attributes"]["nomcomarca"];
            $nom = utf8_decode(str_replace("'", "''", $nomComarca));

            $sql = "INSERT INTO comarca_stats (comarca, nom, pop_total, covid_neg, covid_pos, covid_pos_100k_raw, covid_pos_100k_std)
            VALUES ('".$comarca."', '".$nom."', '".$pop."', '".$covNeg."', '".$covPos."', '".$cov100KRaw."', '".$cov100KStd."')";

            if ($conn->query($sql) === TRUE) {
                echo "<br> New record added successfully for ". $nomComarca . "<br>";
            } else {
                echo "<br>Error adding: " . $sql . "<br>" . $conn->error;
            }
        }
    }

    // Create connection
    $conn = new mysqli(DB_SERVERNAME, DB_USERNAME, DB_PASSWORD);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    echo "Connected successfully<br>";

    $conn->select_db(DB_NAME);

    initDB($conn);

    $timeout = 6;

    for ($i=1; $i < 43; $i++) {
        getComarcaData($i, $conn);
        sleep($timeout);
    }

    for ($i=1; $i < 948; $i++) {
        getMuncipiData($i, $conn);
        sleep($timeout);
    }

    $conn->close();
?>
