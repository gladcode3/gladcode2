<?php
    include_once "connection.php";
    session_start();
    include("back_node_message.php");
    $user = $_SESSION['user'];
    $action = $_POST['action'];
    $output = array();

    if ($action == "GET"){
        $offset = $_POST['offset'];
        $limit = 10;

        $fav = "";
        if (isset($_POST['favorites'])){
            $fav = "AND favorite = 1";
        }

        $unread = "";
        if(isset($_POST['unread_only']) && $_POST['unread_only'] === "true"){
            $unread = "AND isread = 0";
        }

        $sql = "SELECT id FROM reports r INNER JOIN gladiators g ON g.cod = r.gladiator WHERE gladiator IN (SELECT cod FROM gladiators WHERE master = '$user') $fav $unread";
        $result = runQuery($sql);
        $total = $result->rowCount();
        $output['total'] = $total;

        if (!isset($offset)){
            $offset = 1;
            $limit = $total;
        }
        
        if (!isset($_POST['offset'])){
            $offset = 0;
            $limit = $total;
        }
        if ($offset < 0){
            $offset = 0;
        }
        
        $sql = "SELECT r.id, time, name, isread, hash, reward, favorite, comment, expired FROM reports r INNER JOIN gladiators g ON g.cod = r.gladiator INNER JOIN logs l ON l.id = r.log WHERE gladiator IN (SELECT cod FROM gladiators WHERE master = '$user') $fav $unread ORDER BY time DESC LIMIT $limit OFFSET $offset";
        $result = runQuery($sql);
        
        $infos = array();
        $ids = array();
        while($row = $result->fetch()){
            $info = array();
            $info['id'] = $row['id'];
            $info['time'] = $row['time'];
            $info['gladiator'] = $row['name'];
            $info['isread'] = $row['isread'];
            $info['reward'] = $row['reward'];
            $info['favorite'] = boolval($row['favorite']);
            $info['comment'] = $row['comment'];

            if ($row['expired'] == 1){
                $info['expired'] = true;
            }
            else{
                $info['hash'] = $row['hash'];
            }

            array_push($infos, $info);
            array_push($ids, $row['id']);
        }

        $output['reports'] = $infos;
        $output['status'] = "SUCCESS";
        
        if (isset($_POST['read'])){
            if (count($ids) > 0){
                $ids = implode(",", $ids);

                $sql = "UPDATE reports SET isread = '1' WHERE id IN ($ids)";
                $result = runQuery($sql);

                send_node_message(array(
                    'profile notification' => array('user' => array($user))
                ));
            }
        }
    }
    elseif ($action == "DELETE"){
        $id = $_POST['id'];
        $sql = "DELETE FROM reports WHERE id = '$id' AND gladiator IN (SELECT cod FROM gladiators WHERE master = '$user')";
        $result = runQuery($sql);

        $output['status'] = "SUCCESS";
    }
    else if ($action == "FAVORITE"){
        $favorite = $_POST['favorite'];

        if ($favorite === "true" || $favorite === "false"){
            $id = $_POST['id'];
            $comment = $_POST['comment'];
            
            $sql = "UPDATE reports SET favorite = $favorite, comment = '$comment' WHERE id = $id";
            $result = runQuery($sql);
            $output['status'] = "SUCCESS";
        }
        else{
            $output['status'] = "ERROR";
            $output['post'] = $_POST;
        }
    }

    echo json_encode($output);
?>