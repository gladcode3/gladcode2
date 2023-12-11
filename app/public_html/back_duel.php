<?php
	session_start();
    include_once "connection.php";
    include("back_node_message.php");
    $user = $_SESSION['user'];
    $action = $_POST['action'];
    $output = array();
        
    if ($action == "GET"){
        $sql = "SELECT d.id, d.time, u.apelido, u.foto, u.lvl FROM duels d INNER JOIN usuarios u ON u.id = d.user1 WHERE d.user2 = '$user' AND d.log IS NULL";
        $result = runQuery($sql);

        $info = array();
        while($row = $result->fetch()){
            $duel = array();
            $duel['id'] = $row['id'];
            $duel['time'] = $row['time'];
            $duel['nick'] = $row['apelido'];
            $duel['lvl'] = $row['lvl'];
            $duel['picture'] = $row['foto'];
            array_push($info, $duel);
        }

        $output['duels'] = $info;
        $output['status'] = "SUCCESS";
    }
    elseif ($action == "CHALLENGE"){
        $friend = $_POST['friend'];
        $glad = $_POST['glad'];
        $sql = "SELECT cod FROM amizade WHERE (usuario1 = '$user' AND usuario2 = '$friend') OR (usuario2 = '$user' AND usuario1 = '$friend')";
        $result = runQuery($sql);
        if ($result->rowCount() == 0)
            $output['status'] = "NOT_FRIEND";
        else{
            $sql = "SELECT cod FROM gladiators g INNER JOIN usuarios u ON g.master = u.id WHERE g.cod = '$glad' AND g.master = '$user'";
            $result = runQuery($sql);
            if ($result->rowCount() == 0)
                $output['status'] = "NOT_GLAD";
            else{
                $sql = "SELECT id FROM duels WHERE user2 = '$friend' AND gladiator1 = '$glad' AND log IS NULL";
                $result = runQuery($sql);
                if ($result->rowCount() > 0)
                    $output['status'] = "EXISTS";
                else{
                    $sql = "INSERT INTO duels (user1, gladiator1, user2, time) VALUES ('$user', '$glad', '$friend', now())";
                    $result = runQuery($sql);
                    $output['status'] = "OK";
                    
                    send_node_message(array(
                        'profile notification' => array('user' => array($friend))
                    ));

                }

            }
            
        }
    }
    elseif ($action == "DELETE"){
        $id = $_POST['id'];

        $sql = "SELECT user1, user2 FROM duels WHERE id = $id";
        $result = runQuery($sql);
        $row = $result->fetch();
        
        send_node_message(array(
            'profile notification' => array('user' => array($row['user1'], $row['user2']))
        ));

        $sql = "DELETE FROM duels WHERE id = '$id' AND (user1 = '$user' OR user2 = '$user')";
        $result = runQuery($sql);

        $output['status'] = "OK";
    }
    elseif ($action == "REPORT"){
        $offset = $_POST['offset'];
        $limit = 10;

        $sql = "SELECT d.id FROM duels d WHERE ((d.user1 = '$user' OR d.user2 = '$user') AND d.log IS NOT NULL) OR (d.user1 = '$user' AND d.log IS NULL)";
        $result = runQuery($sql);
        $total = $result->rowCount();
        $output['total'] = $total;

        if (!isset($_POST['offset'])){
            $offset = 0;
            $limit = $total;
        }
        
        if ($offset < 0){
            $offset = 0;
        }
        
        $sql = "SELECT d.id, d.time, d.log, d.isread, g1.name AS glad1, g2.name AS glad2, u1.apelido AS nick1, u2.apelido AS nick2, u1.id AS user1, u2.id AS user2 FROM duels d LEFT JOIN gladiators g1 ON g1.cod = d.gladiator1 LEFT JOIN gladiators g2 ON g2.cod = d.gladiator2 INNER JOIN usuarios u1 ON u1.id = d.user1 INNER JOIN usuarios u2 ON u2.id = d.user2 WHERE ((d.user1 = '$user' OR d.user2 = '$user') AND d.log IS NOT NULL) OR (d.user1 = '$user' AND d.log IS NULL) ORDER BY d.time DESC LIMIT $limit OFFSET $offset";
        $result = runQuery($sql);

        $info = array();
        while($row = $result->fetch()){
            $duel = array();
            if ($row['user1'] == $user){
                $duel['glad'] = $row['glad1'];
                $duel['user'] = $row['nick2'];
                $duel['enemy'] = $row['glad2'];
            }
            elseif ($row['user2'] == $user){
                $duel['glad'] = $row['glad2'];
                $duel['user'] = $row['nick1'];
                $duel['enemy'] = $row['glad1'];
            }
            $duel['id'] = $row['id'];
            $duel['time'] = $row['time'];
            $duel['log'] = $row['log'];
            $duel['isread'] = $row['isread'];
            array_push($info, $duel);
        }

        $output['status'] = "SUCCESS";
        $output['duels'] = $info;

        $sql = "UPDATE duels SET isread = '1' WHERE user1 = '$user' AND log IS NOT NULL";
        $result = runQuery($sql);
        
        send_node_message(array(
            'profile notification' => array('user' => array($user))
        ));

    }

    echo json_encode($output);
?>