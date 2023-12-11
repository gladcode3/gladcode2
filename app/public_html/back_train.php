<?php
    session_start();
    date_default_timezone_set('America/Sao_Paulo');
    include_once "connection.php";
    include("back_node_message.php");

    $user = $_SESSION['user'];
    $action = $_POST['action'];
    $output = array();

    if ($action == "CREATE"){
        $name = $_POST['name'];
        $desc = $_POST['desc'];
        $maxtime = $_POST['maxtime'];
        $players = $_POST['players'];
        $weight = $_POST['weight'];
        $hash = newHash();

        $sql = "SELECT premium, credits FROM usuarios WHERE id = $user";
        $result = runQuery($sql);
        $row = $result->fetch();

        if (is_null($row['premium'])){
            $output['status'] = "NOPREMIUM";
        }
        elseif ($row['credits'] <= 0){
            $output['status'] = "NOCREDITS";
        }
        else{
            $sql = "INSERT INTO training (manager, name, description, creation, maxtime, players, weight, hash, hash_valid) VALUES ('$user', '$name', '$desc', now(3), $maxtime, $players, $weight, '$hash', now(3) + INTERVAL 1 HOUR);";
            $result = runQuery($sql);
            
            send_node_message(array('training list' => array()));
    
            $output['hash'] = $hash;
            $output['status'] = "SUCCESS";
        }

    }
    else if ($action == "LIST"){
        $moffset = $_POST['moffset'];
        $poffset = $_POST['poffset'];
        $limit = 10;

        if ($moffset < 0)
            $moffset = 0;
        if ($poffset < 0)
            $poffset = 0;

        $masters = "SELECT count(DISTINCT gladiator) FROM gladiator_training WHERE training = t.id";
        $select = "SELECT DISTINCT t.id, t.name, t.description, ($masters) AS masters";

        //show participating training
        $fromwhere = "FROM training t INNER JOIN gladiator_training gt ON gt.training = t.id INNER JOIN gladiators g ON g.cod = gt.gladiator WHERE g.master = $user";

        $sql = "SELECT t.id $fromwhere";
        $result = runQuery($sql);
        $npart = $result->rowCount();

        if ($npart > 0){
            $sql = "$select $fromwhere ORDER BY t.creation DESC LIMIT $limit OFFSET $poffset";
            $result = runQuery($sql);

            if ($poffset >= $npart)
                $poffset -= $limit;
            if ($poffset < 0)
                $poffset = 0;

            $part = array();
            if ($npart > 0){
                while ($row = $result->fetch()){
                    array_push($part, $row);
                }
            }
        }

        //show manager training
        $fromwhere = "FROM training t WHERE t.manager = $user";

        $sql = "SELECT t.id $fromwhere";
        $result = runQuery($sql);
        $nmanage = $result->rowCount();

        if ($nmanage > 0){
            $sql = "$select $fromwhere ORDER BY t.creation DESC LIMIT $limit OFFSET $moffset";
            $result = runQuery($sql);

            if ($moffset >= $nmanage)
                $moffset -= $limit;
            if ($moffset < 0)
                $moffset = 0;

            $manage = array();
            if ($nmanage > 0){
                while ($row = $result->fetch()){
                    array_push($manage, $row);
                }
            }
        }

        $output['pages'] = array();

        $output['pages']['manage'] = array();
        $output['pages']['manage']['offset'] = $moffset;
        $output['pages']['manage']['total'] = $nmanage;

        $output['pages']['part'] = array();
        $output['pages']['part']['offset'] = $poffset;
        $output['pages']['part']['total'] = $npart;

        $output['part'] = $part;
        $output['manage'] = $manage;

        $output['status'] = "SUCCESS";

        // check if I am listing rooms throught a redirect
        if (isset($_SESSION['redirect'])){
            $redirect = explode(":", $_SESSION['redirect']);
            if ($redirect[0] == "train_join"){
                $output['redirect'] = $redirect[1];
            }
        }
    }
    elseif ($action == "JOIN"){
        $hash = $_POST['hash'];

        // time since expired
        $sql = "SELECT *, TIME_TO_SEC(TIMEDIFF(now(), hash_valid)) as timediff FROM training WHERE hash = '$hash'";
        $result = runQuery($sql);
        $nrows = $result->rowCount();

        if ($nrows == 0)
            $output['status'] = "NOTFOUND";
        elseif ($nrows > 1)
            $output['status'] = "COLLISION";
        else{
            $row = $result->fetch();
            $trainid = $row['id'];
            $trainname = $row['name'];

            if (isStarted($trainid))
                $output['status'] = "STARTED";
            else{
                $sql = "SELECT g.cod FROM gladiators g INNER JOIN gladiator_training gt ON gt.gladiator = g.cod WHERE g.master = $user AND gt.training = $trainid";
                $result = runQuery($sql);
                $nrows = $result->rowCount();

                if ($nrows > 0){
                    $output['id'] = $trainid;
                    $output['status'] = "JOINED";
                }
                elseif ($row['timediff'] > 0)
                    $output['status'] = "EXPIRED";
                elseif (!isset($_POST['glad']))
                    $output['status'] = "ALLOWED";
                else{
                    $glad = $_POST['glad'];

                    // check if the glad is mine
                    $sql = "SELECT cod FROM gladiators WHERE master = $user AND cod = $glad";
                    $result = runQuery($sql);
                    $nrows = $result->rowCount();

                    if ($nrows == 0)
                        $output['status'] = "NOGLAD";
                    else{
                        // add glad into training
                        $sql = "INSERT INTO gladiator_training (gladiator, training, score) VALUES ($glad, $trainid, 0)";
                        $result = runQuery($sql);
                        $output['id'] = $trainid; 
                        $output['name'] = $trainname;
                        $output['status'] = "SUCCESS";    
                        send_node_message(array('training list' => array()));
                        send_node_message(array('training room' => array(
                            'id' => $trainid
                        )));

                        // check if I want to redirect to another page
                        $redirect = $_POST['redirect'];
                        if ($redirect == "true"){
                            $_SESSION['redirect'] = "train_join:$trainid";
                        }
                    }

                }
            }
        }

    }
    elseif ($action == "ROOM"){
        $trainid = $_POST['id'];

        $sql = "SELECT *, TIME_TO_SEC(TIMEDIFF(now(), hash_valid)) as timediff FROM training t WHERE id = $trainid";
        $result = runQuery($sql);
        $nrows = $result->rowCount();

        if ($nrows == 0)
            $output['status'] = "NOTFOUND";
        else{
            $row = $result->fetch();

            if (isStarted($trainid)){
                $output['status'] = "STARTED";
                $output['hash'] = $row['hash'];
            }
            else{
                if ($row['manager'] == $user){
                    // I am the manager
                    $output['name'] = $row['name'];
                    $output['description'] = $row['description'];
                    if ($row['timediff'] > 0)
                        $output['expired'] = true;
                    else
                        $output['expired'] = false;
                    $output['hash'] = $row['hash'];
                    $output['players'] = $row['players'];
                    $output['maxtime'] = $row['maxtime'];

                    // TODO mandar junto a lista de players/gladiadores pra compor a janela
                    $sql = "SELECT g.name AS gladiator, u.apelido AS master, g.cod as id FROM gladiators g INNER JOIN usuarios u ON u.id = g.master WHERE g.cod IN (SELECT gladiator FROM gladiator_training WHERE training = $trainid)";
                    $result = runQuery($sql);

                    $output['glads'] = array();
                    while ($row = $result->fetch()){
                        array_push($output['glads'], $row);
                    }

                    $output['status'] = "MANAGE";
                }
                else{
                    $sql = "SELECT t.name, t.description, t.maxtime, t.players, t.hash FROM training t INNER JOIN gladiator_training gt ON gt.training = t.id WHERE t.id = $trainid AND gt.gladiator IN (SELECT cod FROM gladiators WHERE master = $user)";
                    $result = runQuery($sql);
                    $nrows = $result->rowCount();

                    if ($nrows == 0)
                        $output['status'] = "NOTALLOWED";
                    else{
                        $row = $result->fetch();
                        $output = $row;

                        $sql = "SELECT g.name AS gladiator, u.apelido AS master, g.master AS masterid FROM usuarios u INNER JOIN gladiators g ON u.id = g.master INNER JOIN gladiator_training gt ON g.cod = gt.gladiator WHERE gt.training = $trainid";
                        $result = runQuery($sql);

                        $output['glads'] = array();
                        while ($row = $result->fetch()){
                            $glad = array();
                            $glad['gladiator'] = $row['gladiator'];
                            $glad['master'] = $row['master'];

                            if ($row['masterid'] == $user)
                                $glad['mine'] = true;

                            array_push($output['glads'], $glad);
                        }
                        
                        $output['status'] = "PARTICIPATE";
                    }
                }
            }

            // if I get here by redirect (same room) unset session 
            if (isset($_SESSION['redirect'])){
                $redirect = explode(":", $_SESSION['redirect']);
                if ($redirect[1] == $trainid)
                    unset($_SESSION['redirect']);
            }
        }

    }
    elseif ($action == "EDIT"){
        $trainid = $_POST['id'];
        $field = $_POST['field'];
        $value = trim($_POST['value']);

        if (isStarted($trainid))
            $output['status'] = "STARTED";
        else{
            if ($field == 'name' && strlen($value) < 6){
                $output['status'] = "ERROR";
                $output['message'] = "Nome precisa ter pelo menos 6 caracteres";
            }
            else{
                $sql = "SELECT id FROM training WHERE id = $trainid AND manager = $user";
                $result = runQuery($sql);
                $nrows = $result->rowCount();

                if ($nrows == 0)
                    $output['status'] = "NOTALLOWED";
                else{
                    $sql = "UPDATE training SET $field = '$value' WHERE id = $trainid";
                    $result = runQuery($sql);

                    $output['status'] = "SUCCESS";
                    send_node_message(array('training list' => array()));
                    send_node_message(array('training room' => array(
                        'id' => $trainid
                    )));
                }
            }
        }
    }
    elseif ($action == "RENEW"){
        $trainid = $_POST['id'];

        if (isStarted($trainid))
            $output['status'] = "STARTED";
        else{
            $sql = "SELECT id FROM training WHERE id = $trainid AND manager = $user";
            $result = runQuery($sql);
            $nrows = $result->rowCount();

            if ($nrows == 0)
                $output['status'] = "NOTALLOWED";
            else{
                $hash = newHash();

                $sql = "UPDATE training SET hash = '$hash', hash_valid = now() + INTERVAL 1 HOUR WHERE id = $trainid";
                $result = runQuery($sql);

                $output['status'] = "SUCCESS";
                $output['hash'] = $hash;

                send_node_message(array('training room' => array(
                    'id' => $trainid
                )));
            }
        }
    }
    elseif ($action == "KICK"){
        $trainid = $_POST['id'];
        $glad = $_POST['glad'];
        $myself = $_POST['myself'];

        if (isStarted($trainid))
            $output['status'] = "STARTED";
        else{
            if (isset($myself) && $myself == "true")
                $managersql = "id IN (SELECT gt.training FROM gladiator_training gt INNER JOIN gladiators g ON g.cod = gt.gladiator WHERE g.master = $user)";
            else
                $managersql = "manager = $user";

            $sql = "SELECT id FROM training WHERE id = $trainid AND $managersql";
            $result = runQuery($sql);
            $nrows = $result->rowCount();

            if ($nrows == 0)
                $output['status'] = "NOTALLOWED";
            else{
                if (isset($myself) && $myself == "true")
                    $gladsql = "gladiator IN (SELECT cod FROM gladiators WHERE master = $user)";
                else
                    $gladsql = "gladiator = $glad";

                $sql = "SELECT id FROM gladiator_training WHERE training = $trainid AND $gladsql";
                $result = runQuery($sql);
                $nrows = $result->rowCount();

                if ($nrows == 0)
                    $output['status'] = "NOGLAD";
                else{
                    $row = $result->fetch();
                    $id = $row['id'];

                    $sql = "DELETE FROM gladiator_training WHERE id = $id";
                    $result = runQuery($sql);
                    $output['status'] = "SUCCESS";
                    send_node_message(array('training list' => array()));
                    send_node_message(array('training room' => array(
                        'id' => $trainid
                    )));
                }
                
            }
        }
    }
    elseif ($action == "DELETE"){
        $trainid = $_POST['id'];

        $sql = "SELECT id FROM training WHERE id = $trainid AND manager = $user";
        $result = runQuery($sql);
        $nrows = $result->rowCount();

        if ($nrows == 0)
            $output['status'] = "NOTALLOWED";
        else{
            $sql = "SELECT id FROM gladiator_training WHERE training = $trainid";
            $result = runQuery($sql);
            $nrows = $result->rowCount();

            if ($nrows > 0)
                $output['status'] = "NOTEMPTY";
            else{
                $sql = "DELETE FROM training WHERE id = $trainid";
                $result = runQuery($sql);
                $output['status'] = "SUCCESS";

                send_node_message(array('training list' => array()));
                send_node_message(array('training room' => array(
                    'id' => $trainid
                )));
            }
        }
    }
    elseif ($action == "CHANGE"){
        $trainid = $_POST['id'];

        if (isStarted($trainid))
            $output['status'] = "STARTED";
        else{
            $sql = "SELECT gladiator FROM gladiator_training WHERE training = $trainid AND gladiator IN (SELECT cod FROM gladiators WHERE master = $user)";
            $result = runQuery($sql);
            $nrows = $result->rowCount();

            if ($nrows > 0){
                $row = $result->fetch();
                $oldglad = $row['gladiator'];

                $glad = $_POST['glad'];

                $sql = "SELECT master FROM gladiators WHERE cod = $glad";
                $result = runQuery($sql);
                $nrows = $result->rowCount();

                $row = $result->fetch();

                if ($row['master'] == $user){
                    $sql = "UPDATE gladiator_training SET gladiator = $glad WHERE training = $trainid AND gladiator = $oldglad";
                    $result = runQuery($sql);

                    $output['status'] = "SUCCESS";    
                    send_node_message(array('training room' => array(
                        'id' => $trainid
                    )));

                }
                else
                    $output['status'] = "NOTALLOWED";
            }
            else
                $output['status'] = "NOTFOUND";
        }

    }
    elseif ($action == "START"){
        $trainid = $_POST['id'];

        $sql = "SELECT manager, maxtime, players FROM training WHERE id = $trainid";
        $result = runQuery($sql);
        $nrows = $result->rowCount();

        if ($nrows == 0)
            $output['status'] = "NOTFOUND";
        elseif (isStarted($trainid))
            $output['status'] = "STARTED";
        else{
            $row = $result->fetch();
            $manager = $row['manager'];
            $maxtime = $row['maxtime'];
            $maxplayers = $row['players'];
            if ($manager != $user)
                $output['status'] = "NOTALLOWED";
            else{
                $sql = "SELECT gt.id, t.hash FROM gladiator_training gt INNER JOIN training t ON t.id = gt.training WHERE t.id = $trainid";
                $result = runQuery($sql);
                $nplayers = $result->rowCount();

                if ($nplayers < $maxplayers)
                    $output['status'] = "FEWPLAYERS";
                else{
                    // calculate number of `groups` and place ids from participants in array
                    $gtids = array(); 
                    while ($row = $result->fetch()){
                        if (!isset($ngroups)){
                            $hash = $row['hash'];
                            $ngroups = ceil($nplayers / $maxplayers);
                        }

                        array_push($gtids, $row['id']);
                    }

                    // shuffle ids form participants
                    shuffle($gtids);

                    $groups = array();
                    // iterate over every shuffled id
                    foreach($gtids as $i => $id){
                        // create group if not every one needed is created
                        if (count($groups) < $ngroups){
                            $sql = "INSERT INTO training_groups () VALUES ()";
                            $result = runQuery($sql);
                            array_push($groups, $conn->lastInsertId());
                        }

                        // cycle `groups` inserting it on every participant
                        $groupid = $groups[$i % $ngroups];
                        $sql = "UPDATE gladiator_training SET groupid = $groupid WHERE id = $id";
                        $result = runQuery($sql);
                    }

                    // set training deadline
                    $sql = "UPDATE training SET deadline = now(3) + INTERVAL $maxtime MINUTE WHERE id = $trainid";
                    $result = runQuery($sql);

                    send_node_message(array('training room' => array(
                        'id' => $trainid
                    )));
    
                    $output['hash'] = $hash;
                    $output['status'] = "SUCCESS";
                }
            }
        }
    }
    elseif ($action == "REMOVE"){
        $trainid = $_POST['id'];

        $sql = "SELECT manager FROM training WHERE id = $trainid";
        $result = runQuery($sql);
        $row = $result->fetch();

        if ($row['manager'] != $user){
            $output['status'] = "NOPERMISSION";
        }
        else{
            $sql = "SELECT groupid FROM gladiator_training WHERE training = $trainid";
            $result = runQuery($sql);
            $groups = array();
            while ($row = $result->fetch())
                array_push($groups, $row['groupid']);
            $groups = implode(",", $groups);

            $sql = "DELETE FROM gladiator_training WHERE training = $trainid";
            $result = runQuery($sql);

            if (strlen($groups) > 0){
                $sql = "DELETE FROM training_groups WHERE id IN ($groups)";
                $result = runQuery($sql);
            }

            $sql = "DELETE FROM training WHERE id = $trainid";
            $result = runQuery($sql);

            send_node_message(array('training list' => array()));
            send_node_message(array('training room' => array(
                'id' => $trainid
            )));
            
            $output['status'] = "SUCCESS";
        }

    }

    echo json_encode($output);

    function newHash(){
        $salt = "a60aa0e9034aee2a7d71fbe5d0910728";
        $size = 4;

        // check if there is already the same hash on a valid time. necessary because I want to use only 4 digits
        $collision = true;
        while($collision){
            $hash = strtoupper(base_convert(md5(md5(microtime(true)*mt_rand(0,1000000)) . $salt) ,16, 36));
            $subhash = substr($hash, 0, $size);
            $sql = "SELECT count(*) AS collision FROM training WHERE hash = '$subhash'";
            $result = runQuery($sql);
            $row = $result->fetch();
            if ($row['collision'] == 0)
                $collision = false;
        }

        return $subhash;
    }

    function isStarted($id){
        $sql = "SELECT groupid FROM gladiator_training WHERE training = $id";
        $result = runQuery($sql);
        while($row = $result->fetch()){
            if (!is_null($row['groupid']))
                return true;
        }
        return false;
    }
?>