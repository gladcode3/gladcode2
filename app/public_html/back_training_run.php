<?php
    session_start();
    date_default_timezone_set('America/Sao_Paulo');
    include_once "connection.php";
    include("back_node_message.php");

    $user = $_SESSION['user'];
    $action = $_POST['action'];
    $output = array();

    if ($action == "GET"){
        $hash = $_POST['hash'];
        $round = $_POST['round'];

        $sql = "SELECT t.id, t.name, t.description, t.maxtime, t.hash_valid AS expire, t.deadline, now(3) AS 'now', t.manager, max(tg.round) AS maxround FROM training t INNER JOIN gladiator_training gt ON gt.training = t.id INNER JOIN training_groups tg ON tg.id = gt.groupid WHERE hash = '$hash'";
        $result = runQuery($sql);
        $row = $result->fetch();
        
        $trainid = $row['id'];
        $output['name'] = $row['name'];
        $output['description'] = $row['description'];
        $output['maxtime'] = $row['maxtime'];
        $output['hash'] = $hash;
        $output['now'] = $row['now'];
        $maxround = $row['maxround'];

        if (!is_null($row['deadline']))
            $output['train_deadline'] = $row['deadline'];

        $deadline = (new DateTime($row['deadline']))->getTimestamp();
        $now = (new DateTime($row['now']))->getTimestamp();
        if ($now >= $deadline)
            $output['end'] = true;

        // train is ended
        if ($now >= $deadline && $round == $maxround){
            // need to calc avg time manually, because we need to subtract 1000 when the user won and ignore time 0 
            $manualtime = "SELECT avg(IF(gt2.lasttime > 1000, gt2.lasttime - 1000, gt2.lasttime)) FROM gladiator_training gt2 WHERE gt2.training = $trainid AND gt2.lasttime > 0 AND gt2.gladiator = gt.gladiator";
            
            $sql = "SELECT g.name, u.apelido, sum(gt.score) AS score, ($manualtime) AS 'time' FROM gladiator_training gt INNER JOIN gladiators g ON g.cod = gt.gladiator INNER JOIN usuarios u ON u.id = g.master WHERE gt.training = $trainid GROUP BY gt.gladiator ORDER BY score DESC, time DESC";
            $result = runQuery($sql);

            $output['ranking'] = array();
            while($row = $result->fetch()){
                array_push($output['ranking'], $row);
            }

            $output['maxround'] = $maxround;
            $output['status'] = "END";
        }
        elseif ($round > $maxround || $round < 1){
            $output['round'] = $maxround;
            $output['status'] = "REDIRECT";
        }
        else{
            if ($row['manager'] == $user)
                $output['manager'] = true;

            $sql = "SELECT gt.id, u.apelido AS master, g.name AS gladiator, g.cod AS gladid, tg.id AS 'group', u.id AS user, tg.deadline FROM gladiator_training gt INNER JOIN training_groups tg ON tg.id = gt.groupid INNER JOIN gladiators g ON gt.gladiator = g.cod INNER JOIN usuarios u ON u.id = g.master WHERE gt.training = $trainid AND tg.round = $round ORDER BY gt.groupid";
            $result = runQuery($sql);
            
            $groups = array();
            while($row = $result->fetch()){
                if (!isset($output['round'])){
                    $output['round'] = $round;
                    $output['deadline'] = $row['deadline'];
                }

                $team = array();
                $team['master'] = $row['master'];
                $team['gladiator'] = $row['gladiator'];

                if ($row['user'] == $user)
                    $team['myteam'] = true;

                $gid = $row['group'];
                $tid = $row['id'];
                $gladid = $row['gladid'];

                // get summed score
                $sql = "SELECT sum(gt.score) AS score FROM gladiator_training gt INNER JOIN training_groups tg ON tg.id = gt.groupid WHERE gt.gladiator = $gladid AND gt.training = $trainid AND tg.round < $round";
                $result2 = runQuery($sql);
                $row = $result2->fetch();

                if (is_null($row['score']))
                    $team['score'] = 0;
                else{
                    $team['score'] = $row['score'];
                }

                if (!isset($groups[$gid]))
                    $groups[$gid] = array();
                $groups[$gid][$tid] = $team;            
            }

            $output['groups'] = $groups;
            $output['status'] = "SUCCESS";
        }
    }
    elseif ($action == "DEADLINE"){
        $hash = $_POST['hash'];
        $time = $_POST['time'];
        $round = $_POST['round'];

        $sql = "SELECT manager, id FROM training WHERE hash = '$hash'";
        $result = runQuery($sql);
        $nrows = $result->rowCount();

        if ($nrows == 0)
            $output['status'] = "NOTFOUND";
        else{
            $row = $result->fetch();
            if ($row['manager'] != $user)
                $output['status'] = "NOTALLOWED";
            // end training
            elseif (!isset($_POST['round']) && !isset($_POST['time'])){
                $trainid = $row['id'];
                $sql = "UPDATE training SET deadline = now(3) WHERE id = $trainid";
                $result = runQuery($sql);

                $output['status'] = "SUCCESS";

                send_node_message(array('training end' => array(
                    'hash' => $hash
                )));
            }
            else{
                $trainid = $row['id'];

                $sql = "SELECT tg.id FROM training_groups tg INNER JOIN gladiator_training gt ON gt.groupid = tg.id WHERE gt.training = $trainid AND tg.round = $round";
                $result = runQuery($sql);
                $groups = array();
                while ($row = $result->fetch())
                    array_push($groups, $row['id']);
                $groups = implode(",", $groups);
                
                $sql = "UPDATE training_groups SET deadline = now(3) + INTERVAL $time MINUTE WHERE id IN ($groups)";
                $result = runQuery($sql);

                $group = explode(",",$groups)[0];
                $sql = "SELECT deadline FROM training_groups WHERE id = $group";
                $result = runQuery($sql);
                $row = $result->fetch();

                $output['deadline'] = $row['deadline'];
                $output['status'] = "SUCCESS";

                send_node_message(array('training refresh' => array(
                    'hash' => $hash
                )));

            }
        }
    }
    elseif ($action == "REFRESH"){
        $hash = $_POST['hash'];
        $round = $_POST['round'];

        $sql = "SELECT id, deadline FROM training WHERE hash = '$hash'";
        $result = runQuery($sql);
        $nrows = $result->rowCount();

        if ($nrows == 0)
            $output['status'] = "NOTFOUND";
        else{
            $row = $result->fetch();
            $trainid = $row['id'];
            $train_deadline = $row['deadline'];

            $sql = "SELECT gt.score, gt.id, tg.deadline, now(3) AS 'now', tg.id AS 'group', tg.locked, l.hash AS 'log', l.id AS 'logid' FROM gladiator_training gt INNER JOIN training_groups tg ON tg.id = gt.groupid LEFT JOIN logs l ON l.id = tg.log  WHERE gt.training = $trainid AND tg.round = $round";
            $result = runQuery($sql);

            $hasLog = false;
            $groups = array();
            while ($row = $result->fetch()){
                if (!isset($output['now'])){
                    $now = $row['now'];
                    $deadline = $row['deadline'];
                }
                $id = $row['id'];
                $gid = $row['group'];

                if (!isset($groups[$gid]))
                    $groups[$gid] = array();
                if (!isset($groups[$gid][$id]))
                    $groups[$gid][$id] = array();

                if (!is_null($row['locked']))
                    $groups[$gid]['locked'] = true;

                if (!is_null($row['log'])){
                    $groups[$gid]['log'] = $row['log'];
                    // put battle info into groups
                    $groups[$gid] = getScores($groups[$gid], $row['logid'], $trainid, $round);
                }
            }

            $output['groups'] = $groups;
            $output['deadline'] = $deadline;
            $output['train_deadline'] = $train_deadline;
            $output['now'] = $now;

            $train_deadline = (new DateTime($train_deadline))->getTimestamp();
            $now = (new DateTime($now))->getTimestamp();
            if ($now >= $train_deadline){
                $output['end'] = true;
                $endtrain = true;
            }
            else
                $endtrain = false;

            if (is_null($deadline))
                $output['status'] = "WAIT";
            else{
                // check if need to run again
                foreach($groups as $gid => $group){
                    $tolerance = 7;
                    $sql = "SELECT log FROM training_groups WHERE locked + INTERVAL $tolerance SECOND < now(3) AND id = $gid";
                    $result = runQuery($sql);
                    $nrows = $result->rowCount();
                    if ($nrows > 0){
                        $row = $result->fetch();
                        if (is_null($row['log'])){
                            unset($groups[$gid]['locked']);
                        }
                        if (isset($_SESSION['train-run']) && $_SESSION['train-run']['id'] == md5("train-$gid-id"))
                            unset($_SESSION['train-run']);
                    }
                }

                // is already running something
                // also check if 10 seconds have passed since the session lock
                if (isset($_SESSION['train-run']) && $_SESSION['train-run']['time'] + 10 > (new DateTime())->getTimestamp() ){
                    $output['status'] = "LOCK";
                }
                else{
                    // time is up
                    $deadline = (new DateTime($deadline))->getTimestamp();
                    if ($now >= $deadline){
                        foreach($groups as $gid => $group){
                            if (!isset($group['locked'])){
                                $_SESSION['train-run'] = array(
                                    'id' => md5("train-$gid-id"),
                                    'time' => (new DateTime())->getTimestamp()
                                );
                                $chosen = $gid;
                                break;
                            }
                        }
                        // nothing to be done in this round
                        if(!isset($chosen)){
                            $output['status'] = "DONE";

                            // check if new round already exists
                            $sql = "SELECT max(tg.round) AS maxround FROM training_groups tg INNER JOIN gladiator_training gt ON gt.groupid = tg.id WHERE gt.training = $trainid";
                            $result = runQuery($sql);
                            $row = $result->fetch();
                            $maxround = $row['maxround'];

                            if ($round == $maxround && !$endtrain){
                                $output['newround'] = true;
                            }
                        }
                        else if (!$endtrain){
                            $sql = "UPDATE training_groups SET locked = now(3) WHERE id = $chosen";
                            $result = runQuery($sql);
                            
                            $output['run'] = $chosen;
                            $output['status'] = "RUN";
                        }
                        else
                            $output['status'] = "END";
                    }
                    else
                        $output['status'] = "SUCCESS";
                }
            }
        }
    }
    elseif ($action == "NEW ROUND"){
        $round = $_POST['round'];
        $hash = $_POST['hash'];

        $sql = "SELECT players, id FROM training WHERE hash = '$hash'";
        $result = runQuery($sql);
        $row = $result->fetch();
        $maxplayers = $row['players'];
        $trainid = $row['id'];

        $sql = "SELECT gt.gladiator, tg.deadline, now(3) AS 'now' FROM gladiator_training gt INNER JOIN training_groups tg ON tg.id = gt.groupid WHERE gt.training = $trainid AND tg.round = $round ORDER BY gt.score DESC, gt.lasttime DESC";
        $result = runQuery($sql);

        $glads = array();
        while($row = $result->fetch()){
            array_push($glads, $row['gladiator']);
            if (!isset($deadline)){
                $deadline = $row['deadline'];
                $now = $row['now'];
            }
        }

        $now = (new DateTime($now))->getTimestamp();
        $deadline = (new DateTime($deadline))->getTimestamp();
        if ($now < $deadline){
            $output['TIME'];
        }
        else{
            $ngroups = ceil(count($glads) / $maxplayers);

            // iterate over every shuffled id
            $groups = array();
            $newround = $round + 1;

            $sql = "SELECT tg.id FROM training_groups tg INNER JOIN gladiator_training gt ON gt.groupid = tg.id WHERE tg.round = $newround AND gt.training = $trainid";
            $result = runQuery($sql);
            $nrows = $result->rowCount();

            if ($nrows > 0){
                $output['status'] = "EXISTS";
            }
            else{
                foreach($glads as $i => $id){
                    // create group if not every one needed is created
                    if (count($groups) < $ngroups){
                        $sql = "INSERT INTO training_groups (round) VALUES ($newround)";
                        $result = runQuery($sql);

                        $group = array(
                            'id' => $conn->lastInsertId(),
                            'nglads' => 1
                        );
                        array_push($groups, $group);
                    }
                    else{
                        $groups[$i % $ngroups]['nglads']++;
                    }
                }

                $fields = array();

                // `groups` has nglads, which indicates how many glads in each group
                foreach($groups as $group){
                    for($i=0 ; $i<$group['nglads'] ; $i++){
                        if (!isset($groups[$i]['glads'])){
                            $groups[$i]['glads'] = array();
                        }

                        // shift glad id from $glads and place in field array
                        array_push($fields, "(". implode(",", array(
                            array_shift($glads), $group['id'], $trainid
                        )) .")");
                    }
                }

                $fields = implode(",", $fields);
                $sql = "INSERT INTO gladiator_training (gladiator, groupid, training) VALUES $fields";
                $result = runQuery($sql);

                $output['status'] = "SUCCESS";
            }
        }
    }
    elseif ($action == "RERUN"){
        $hash = $_POST['hash'];
        $group = $_POST['group'];

        $sql = "SELECT manager, id FROM training WHERE hash = '$hash'";
        $result = runQuery($sql);
        $nrows = $result->rowCount();

        if ($nrows == 0)
            $output['status'] = "NOTFOUND";
        else{
            $row = $result->fetch();
            if ($row['manager'] != $user)
                $output['status'] = "NOTALLOWED";
            else{
                $trainid = $row['id'];
                
                $sql = "UPDATE training_groups SET deadline = now(3), locked = NULL, log = NULL WHERE id = $group";
                $result = runQuery($sql);

                $output['status'] = "SUCCESS";

                send_node_message(array('training refresh' => array(
                    'hash' => $hash
                )));

            }
        }
    }

    echo json_encode($output);

    function getScores($group, $logid, $trainid, $round){
        $ids = array_keys($group);
        $retrieve = true;
        foreach ($ids as $id){
            if (is_int($id)){
                $sql = "SELECT score, lasttime FROM gladiator_training WHERE id = $id";
                $result = runQuery($sql);
                $row = $result->fetch();
                if ($row['lasttime'] != 0){
                    $group[$id]['score'] = $row['score'];
                    $group[$id]['time'] = $row['lasttime'];
                    $retrieve = false;
                }            
            }
        }

        if ($retrieve){
            $log = get_battle(file_get_contents("logs/$logid"));

            //get glad id and death times in each log
            $teams = array();
            foreach( array_reverse($log) as $step){
                foreach($step['glads'] as $i => $glad){
                    if (count($teams) < count($step['glads'])){
                        $name = preg_replace('/#/', " ", $glad['name']);
                        $nick = preg_replace('/#/', " ", $glad['user']);
                        
                        // find point in time when every glad was killed
                        $sql = "SELECT gt.id FROM gladiators g INNER JOIN usuarios u ON u.id = g.master INNER JOIN gladiator_training gt ON gt.gladiator = g.cod INNER JOIN training_groups tg ON tg.id = gt.groupid WHERE gt.training = $trainid AND g.name = '$name' AND u.apelido = '$nick' AND tg.round = $round";                            
                        $result = runQuery($sql);
                        $row = $result->fetch();

                        $teams[$i] = array();
                        $teams[$i]['id'] = $row['id'];;
                        $teams[$i]['time'] = 0;

                        if ($glad['hp'] > 0){
                            $teams[$i]['winner'] = true;
                            $teams[$i]['time'] = $step['simtime'];
                        }
                    }
                    if ($teams[$i]['time'] == 0 && $glad['hp'] > 0){
                        $teams[$i]['time'] = $step['simtime'];
                        $teams[$i]['hp'] = $glad['hp'];
                    }
                }
                // when all glads are alive, break
                $count = 0;
                foreach ($teams as $team){
                    if ($team['time'] == 0)
                        $count++;
                }
                if ($count == 0)
                    break;

            }

            usort($teams, 'death_sort');

            $rewards = calcReward($teams);

            foreach($teams as $i => $team){
                $id = $team['id'];
                $group[$id]['score'] = $rewards[$i];
                if (isset($team['winner'])){
                    $group[$id]['winner'] = $team['winner'];
                    $team['time'] += 1000;
                }
                $group[$id]['time'] = $team['time'];

                $score = $rewards[$i];
                $time = $team['time'];
                $sql = "UPDATE gladiator_training SET score = $score, lasttime = $time WHERE id = $id";
                $result = runQuery($sql);
            }
        }

        return $group;
    }

    function get_battle($log){
        $log = json_decode($log, true);
        $merged = array();
        $battle = array();

        foreach($log as $step){
            $merged = array_merge_recursive_distinct($merged, $step);
            array_push($battle, $merged);
        }

        return $battle;
    }

    function array_merge_recursive_distinct ( array &$array1, array &$array2 ){
        $merged = $array1;

        foreach ( $array2 as $key => &$value ){
            if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) )
                $merged [$key] = array_merge_recursive_distinct ( $merged [$key], $value );            
            else
                $merged [$key] = $value;
        }
        return $merged;
    }

    function death_sort($a,$b) {
        if (isset($b['winner']) && !isset($a['winner']))
            return 1;
        elseif (isset($a['winner']) && !isset($b['winner']))
            return -1;
        elseif (isset($b['dead']) && !isset($a['dead']))
            return -1;
        elseif (isset($a['dead']) && !isset($b['dead']))
            return 1;
        else{
            $c = $b['time'] - $a['time'];
            if ($c == 0)
                return $b['hp'] - $a['hp'];
            else
                return $c;
        }
    }

    function calcReward($glads){
        $maxReward = 10;
        $timeWeight = 1.5;
        $winWeight = 1;
        $nglad = count($glads);
        
        $rewards = array();
        // calc time between the last and first to die
        $timeDiff = $glads[1]['time'] - $glads[$nglad-1]['time'];
        
        foreach($glads as $i => $glad){
            $diff = $glad['time'] - $glads[$nglad-1]['time'];
            if ($timeDiff == 0)
                $timeNorm = 1;
            else
                $timeNorm = $diff/$timeDiff;

            $win = 0;
            if ($glads[$i]['winner']){
                $win = 1;
                $timeNorm = 1;
            }
            $rewards[$i] = ($timeNorm * $timeWeight) + ($win * $winWeight);
            if (!isset($topRawReward))
                $topRawReward = $rewards[0];
            $rewards[$i] = $rewards[$i] / $topRawReward * $maxReward;
        }

        return $rewards;
    }
?>