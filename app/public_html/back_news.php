<?php
	session_start();
    include_once "connection.php";
    $user = $_SESSION['user'];
    $action = $_POST['action'];
    $output = array();
    date_default_timezone_set('America/Sao_Paulo');

    if ($action == "GET"){
        //todo infinite scrolling for news page
        $page = $_POST['page'];

        $id = "SUBSTR( md5(CONCAT(id, 'news-post-86')) , 1, 4)";
        $sql = "SELECT $id AS id, title, time, post FROM news ORDER BY time DESC LIMIT 5 OFFSET $page";
        $result = runQuery($sql);

        $output['posts'] = array();
        while ($row = $result->fetch()){
            array_push($output['posts'], $row);
        }

        $sql = "UPDATE usuarios SET read_news = now(3) WHERE id = $user";
        $result = runQuery($sql);

        $output['status'] = "SUCCESS";
    }
    else if ($action == "POST"){
        $hash = $_POST['hash'];

        $id = "SUBSTR( md5(CONCAT(id, 'news-post-86')) , 1, 4)";
        $sql = "SELECT title, time, post FROM news WHERE $id = '$hash'";
        $result = runQuery($sql);
        $output['sql'] = $sql;
        if ($result->rowCount() == 0)
            $output['status'] = "EMPTY";
        else{
            $row = $result->fetch();
            $output['post'] = array();

            $output['post']['title'] = $row['title'];
            $output['post']['time'] = $row['time'];
            $output['post']['body'] = $row['post'];

            $basetime = "SELECT time FROM news WHERE $id = '$hash'";

            $sql = "SELECT $id AS id FROM news WHERE time < ($basetime) ORDER BY time DESC LIMIT 1";
            $result = runQuery($sql);
            if ($result->rowCount() > 0){
                $row = $result->fetch();
                $output['prev'] = $row['id'];
            }
    
            $sql = "SELECT $id AS id FROM news WHERE time > ($basetime) ORDER BY time LIMIT 1";
            $result = runQuery($sql);
            if ($result->rowCount() > 0){
                $row = $result->fetch();
                $output['next'] = $row['id'];
            }
                
            $output['status'] = "SUCCESS";
        }        
    }

    echo json_encode($output);
?>