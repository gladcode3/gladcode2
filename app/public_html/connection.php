<?php
    $config = json_decode(file_get_contents('config.json'), true);
    $host = $config["mysql"]["host"];
    $port = $config["mysql"]["port"];
    $user = $config["mysql"]["user"];
    $password = $config["mysql"]["password"];
    $database = $config["mysql"]["database"];

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET sql_mode='TRADITIONAL'"
    ];

    $conn = new PDO(
        "mysql:host=$host:$port;dbname=$database",
        $user,
        $password,
        $options
    );


    function runQuery($sql){
        global $conn;
        try {
            if(!$result = $conn->query($sql)){
                $error = array(
                    'status' => "SQLERROR",
                    'message' => $conn->error,
                    'sql' => $sql
                );
                die(json_encode($error));
            }
        }
        catch(PDOException $e){
            $error = array(
                'status' => "SQLERROR",
                'message' => $e->getMessage(),
                'sql' => $sql
            );
            die(json_encode($error));
        }
        return $result;
    }
?>