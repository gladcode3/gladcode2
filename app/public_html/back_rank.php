<?php
    session_start();
    date_default_timezone_set('America/Sao_Paulo');
    include_once "connection.php";

    $user = $_SESSION['user'];
    $action = $_POST['action'];
    $output = array();
    
    if ($action == "GET"){
        $offset = $_POST['offset'];

        $search = "";
        if (isset($_POST['search'])){
            $search = $_POST['search'];
            if ($search != ""){
                $search = " WHERE g.name LIKE '%$search%' OR u.apelido LIKE '%$search%'";
            }
        }
        
        $sql = "SELECT cod FROM gladiators g INNER JOIN usuarios u ON g.master = u.id $search";
        $result = runQuery($sql);
        $total = $result->rowCount();

        $limit = 10;
        // when requesting all rows
        if (!isset($_POST['offset'])){
            $offset = 0;
            $limit = $total;
        }
        if ($offset < 0){
            $offset = 0;
        }
        
        $sumreward = "SELECT sum(r.reward) FROM reports r INNER JOIN logs l ON l.id = r.log WHERE g.cod = r.gladiator AND l.time > CURRENT_TIME() - INTERVAL 1 DAY";
        $from = "";
        $position = "SELECT count(*) FROM gladiators g2 WHERE g2.mmr >= g.mmr";
        $sql = "SELECT g.name, g.mmr, u.apelido, ($sumreward) AS sumreward, ($position) AS position FROM gladiators g INNER JOIN usuarios u ON g.master = u.id $search ORDER BY g.mmr DESC LIMIT $limit OFFSET $offset";
        $result = runQuery($sql);
        
        $output['total'] = $total;

        $output['ranking'] = array();
        while($row = $result->fetch()){
            array_push($output['ranking'], array(
                'glad' => $row['name'],
                'mmr' => $row['mmr'],
                'master' => $row['apelido'],
                'change24' => $row['sumreward'],
                'position' => $row['position']
            ));
        }
        $output['status'] = "SUCCESS";
    }
    elseif ($action == "WATCH TAB"){
        $name = trim($_POST['name']);
        if (isset($_POST['add'])){
            $watch = 1;
        }
        elseif (isset($_POST['remove'])){
            $watch = 0;
        }

        if (!isset($watch)){
            $output['status'] = "NOACTION";
        }
        else{
            $sql = "SELECT premium, credits FROM usuarios WHERE id = $user";
            $result = runQuery($sql);
            $row = $result->fetch();

            if (is_null($row['premium'])){
                $output['status'] = "NOPREMIUM";
            }
            elseif ($row['credits'] < 0){
                $output['status'] = "NOCREDITS";
            }
            else{
                $sql = "SELECT id FROM user_tabs WHERE name = '$name' AND owner = $user";
                $result = runQuery($sql);
                $nrows = $result->rowCount();

                if ($nrows > 0){
                    $row = $result->fetch();
                    $id = $row['id'];

                    $sql = "UPDATE user_tabs SET watch = $watch WHERE id = $id";
                    $result = runQuery($sql);

                    $output['status'] = "SUCCESS";
                }
                else{
                    $sql = "INSERT INTO user_tabs (name, owner, watch) VALUES ('$name', $user, $watch)";
                    $result = runQuery($sql);
        
                    $output['id'] = $conn->lastInsertId();
                    $output['status'] = "SUCCESS";
                }
            }
        }
    }
    elseif ($action == "GET TABS"){
        $tags = array();

        // get tags from training I own
        $sql = "SELECT DISTINCT t.description FROM training t WHERE t.manager = $user";
        $result = runQuery($sql);
        while($row = $result->fetch()){
            preg_match_all("/#(\w+)/", $row['description'], $matches);
            if (is_array($matches) && count($matches) > 1){
                foreach($matches[1] as $match){
                    array_push($tags, strtolower($match));
                }
            }
        }
        
        // get tags from training I am participating
        $sql = "SELECT DISTINCT t.description FROM gladiator_training gt INNER JOIN gladiators g ON gt.gladiator = g.cod INNER JOIN training t ON t.id = gt.training WHERE g.master = $user";
        $result = runQuery($sql);
        while($row = $result->fetch()){
            preg_match_all("/#(\w+)/", $row['description'], $matches);
            if (is_array($matches) && count($matches) > 1){
                foreach($matches[1] as $match){
                    array_push($tags, strtolower($match));
                }
            }
        }

        $tags = array_unique($tags);
        sort($tags);

        // see if the tags are blocked to not be watched
        $sql = "SELECT name, watch FROM user_tabs WHERE owner = $user";
        $result = runQuery($sql);
        while($row = $result->fetch()){
            if ($row['watch'] == 0 && in_array($row['name'], $tags)){
                array_splice($tags, array_search($row['name'], $tags), 1);
            }
            if ($row['watch'] == 1 && !in_array($row['name'], $tags)){
                array_push($tags, $row['name']);
            }
        }

        sort($tags);

        $output['tags'] = $tags;
        $output['status'] = "SUCCESS";
    }
    elseif ($action == "FETCH"){
        $tab = $_POST['tab'];
        $search = strtolower($_POST['search']);

        $sql = "SELECT t.id, t.name, t.weight FROM training t WHERE t.description LIKE '%#$tab%'";
        $result = runQuery($sql);

        $training = array();
        while($row = $result->fetch()){
            $train = array();
            $train['id'] = $row['id'];
            $train['weight'] = $row['weight'];
            array_push($training, $train);
        }

        $prize = array(10, 6, 4, 3, 2);
        $ranking = array();
        // get rank from a given training ordered by higher summed score, and average lasttime as tiebreaker
        foreach ($training as $train){
            $trainid = $train['id'];
            $weight = $train['weight'];
            // need to calc avg time manually, because we need to subtract 1000 when the user won and ignore time 0 
            $manualtime = "SELECT avg(IF(gt2.lasttime > 1000, gt2.lasttime - 1000, gt2.lasttime)) FROM gladiator_training gt2 INNER JOIN gladiators g2 ON g2.cod = gt2.gladiator WHERE gt2.training = gt.training AND g2.master = g.master AND gt2.lasttime > 0";
            $sql = "SELECT sum(gt.score) AS score, ($manualtime) AS time, g.master, u.apelido FROM gladiator_training gt INNER JOIN gladiators g ON g.cod = gt.gladiator INNER JOIN usuarios u ON u.id = g.master WHERE gt.training = $trainid GROUP BY g.master ORDER BY score DESC, time DESC";
            $result = runQuery($sql);
            $i = 0;
            while($row = $result->fetch()){
                // if the training was not started, time is null
                if (!is_null($row['time'])){
                    $id = $row['master'];
                    if (!isset($ranking[$id])){
                        $ranking[$id] = array(
                            'score' => 0,
                            'time' => 0,
                            'fights' => 0
                        );
                    }
                    $ranking[$id]['score'] += (isset($prize[$i]) ? $prize[$i] : 0) * $weight;
                    $ranking[$id]['time'] += $row['time'] > 1000 ? $row['time'] - 1000 : $row['time'];
                    $ranking[$id]['fights']++;
                    $ranking[$id]['nick'] = $row['apelido'];
                    $i++;
                }
            }
        }

        // get the avg time
        foreach($ranking as $id => $val){
            $ranking[$id]['time'] /= $val['fights'];
            $ranking[$id]['id'] = $id;
            unset($ranking[$id]['fights']);
        }

        usort($ranking, function($a, $b){
            if ($a['score'] > $b['score']){
                return -1;
            }
            elseif ($b['score'] > $a['score']){
                return 1;
            }
            elseif ($a['time'] > $b['time']){
                return -1;
            }
            elseif ($b['time'] > $a['time']){
                return 1;
            }
            return 0;
        });

        // set the position in the array and filter search string
        $i = 0;
        $filtered = array();
        foreach($ranking as $id => $val){
            $ranking[$id]['position'] = $i + 1;
            $i++;

            if ($search == "" || strpos(strtolower($ranking[$id]['nick']), $search) !== false){
                array_push($filtered, $ranking[$id]);
            }
        }

        $output['ranking'] = $filtered;
        $output['status'] = "SUCCESS";

    }
    elseif ($action == 'MAXMINE'){
        $sql = "SELECT count(*) AS 'offset' FROM gladiators WHERE mmr > (SELECT max(mmr) FROM gladiators WHERE master = $user)";
        $result = runQuery($sql);
        $row = $result->fetch();
        $output['offset'] = $row['offset'];
        $output['status'] = "SUCCESS";
    }
    
    echo json_encode($output);
?>