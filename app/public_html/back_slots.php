<?php
    session_start();
    date_default_timezone_set('America/Sao_Paulo');
    include_once "connection.php";

    $user = $_SESSION['user'];
    $action = $_POST['action'];
    $output = array();

    $slotlvl = [5,15,25,35];
    $apot_times = array(2, 6, 12, 24, 48);

    if ($action == "ITEMS"){
        $sql = "SELECT * FROM items WHERE id NOT IN (11,12,13,14,15,16,17,18) ORDER BY price";
        $result = runQuery($sql);

        $output['potions'] = array();
        while($row = $result->fetch()){
            $id = $row['identifier'];
            $potion = array();
            $potion['id'] = $row['id'];
            $potion['price'] = $row['price'];
            $potion['icon'] = $row['icon'];
            $potion['name'] = $row['name'];
            $potion['lvl'] = $row['lvl'];
            $potion['description'] = $row['description'];

            $output['potions'][$id] = $potion;
        }

        $output['status'] = "SUCCESS";
    }
    elseif ($action == "SLOTS"){
        $sql = "SELECT s.id AS 'sid', i.identifier AS 'id', i.icon, i.name, TIME_TO_SEC(TIMEDIFF(s.expire, now())) AS 'time' FROM slots s INNER JOIN items i ON i.id = s.item WHERE s.user = $user AND expire > now() ORDER BY s.expire LIMIT 4";
        $result = runQuery($sql);

        $output['slots'] = array();
        while ($row = $result->fetch()){
            array_push($output['slots'], $row);
        }

        $sql = "SELECT lvl FROM usuarios WHERE id = $user";
        $result = runQuery($sql);
        $row = $result->fetch();
        $lvl = $row['lvl'];

        $nslots = 0;
        foreach($slotlvl as $sl){
            if ($lvl >= $sl){
                $nslots++;
            }
        }

        $output['nslots'] = $nslots;
        $output['lvl'] = $lvl;
        $output['slotlvl'] = $slotlvl;
        $output['status'] = "SUCCESS";
    }
    elseif ($action == "BUY"){
        $identifier = $_POST['id'];

        $sql = "SELECT silver, lvl, apothecary FROM usuarios WHERE id = $user";
        $result = runQuery($sql);
        $row = $result->fetch();

        $silver = $row['silver'];
        $lvl = $row['lvl'];
        $apot = $row['apothecary'];

        $nslots = 0;
        foreach($slotlvl as $sl){
            if ($lvl >= $sl){
                $nslots++;
            }
        }

        $used = "SELECT count(*) FROM slots s WHERE s.user = $user AND expire > now()";
        $sql = "SELECT id, price, ($used) AS 'used_slots', lvl FROM items WHERE identifier = '$identifier'";
        $result = runQuery($sql);
        $row = $result->fetch();

        $price = $row['price'];
        $lvl = $row['lvl'];
        $item = $row['id'];
        $used_slots = $row['used_slots'];

        if ($used_slots >= $nslots){
            $output['status'] = "NO SLOT";
        }
        else if ($silver < $price){
            $output['status'] = "NOT ENOUGH SILVER";
        }
        elseif ($apot < $lvl){
            $output['status'] = "APOT LVL";
        }
        else{
            $sql = "UPDATE usuarios SET silver = silver - $price WHERE id = $user";
            runQuery($sql);

            $hours = $apot_times[$apot - 1];
            $sql = "INSERT INTO slots (user, item, expire) VALUES ($user, $item, now() + INTERVAL $hours HOUR)";
            $result = runQuery($sql);

            $output['id'] = $conn->lastInsertId();
            $output['status'] = "SUCCESS";
        }

    }
    elseif ($action == "UPGRADE"){
        $command = $_POST['command'];
        $prices = array(1500,5000,15000,35000,0);
    
        if ($command == "COSTS"){
            $output['prices'] = $prices;
            $output['times'] = $apot_times;
        }
        elseif ($command == "APOT"){
            $sql = "SELECT apothecary,silver FROM usuarios WHERE id = $user";
            $result = runQuery($sql);

            $row = $result->fetch();
            $apot = $row['apothecary'];
            $silver = $row['silver'];
            $cost = $prices[$apot - 1];

            if ($apot == 5){
                $output['status'] = "MAX LVL";
            }  
            elseif ($silver < $cost){
                $output['status'] = "NO MONEY";
            }
            else{
                $sql = "UPDATE usuarios SET apothecary = apothecary + 1, silver = silver - $cost WHERE id = $user";
                $result = runQuery($sql);

                $output['silver'] = $silver - $cost;
                $output['apot'] = $apot + 1;
                $output['status'] = "SUCCESS";
            }

        }
    }
    elseif ($action == "EXPIRE") {
        $id = $_POST['id'];

        $sql = "SELECT user FROM slots WHERE id = $id";
        $result = runQuery($sql);

        $row = $result->fetch();
        if ($user == $row['user']){
            $sql = "UPDATE slots SET expire = now() WHERE id = $id";
            runQuery($sql);
            
            $output['status'] = "SUCCESS";
        }
        else{
            $output['status'] = "NOTFOUND";
        }

    }


    echo json_encode($output);
?>