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
        $pass = $_POST['pass'];
        $desc = $_POST['desc'];
        $maxteams = $_POST['maxteams'];
        $maxtime = $_POST['maxtime'];
        $flex = $_POST['flex'];
        if ($flex == "true")
            $flex = 1;
        else
            $flex = 0;

        if ($maxteams < 2)
            $maxteams = 2;
        if ($maxteams > 50)
            $maxteams = 50;

        $maxtime = implode(":", explode("h", $maxtime));
        $maxtime = implode("", explode(" ", $maxtime));
        $maxtime = implode("", explode("m", $maxtime));
        
        if ($maxtime[strlen($maxtime)-1] == ':')
            $maxtime .= "00";
        elseif (count(explode(":", $maxtime)) == 1 )
        	$maxtime = "00:". $maxtime;

        $sql = "SELECT * FROM tournament WHERE name = '$name'";
        $result = runQuery($sql);
        $nrows = $result->rowCount();

        if ($nrows == 0){
            $sql = "INSERT INTO tournament (manager, name, password, description, creation, hash, maxteams, flex, maxtime) VALUES ('$user', '$name', '$pass', '$desc', now(), '', '$maxteams', '$flex', GREATEST(TIME('00:03'), TIME('$maxtime')));";
            $result = runQuery($sql);

            send_node_message(array('tournament list' => array()));
        }
        else
            echo "EXISTS";

    }
    else if ($action == "LIST"){
        $output = array();
        $moffset = $_POST['moffset'];
        $ooffset = $_POST['ooffset'];
        $limit = 10;

        if ($moffset < 0)
            $moffset = 0;
        if ($ooffset < 0)
            $ooffset = 0;

        //how many open 
        $sql = "SELECT t.id FROM tournament t WHERE t.password = '' AND (SELECT count(*) FROM teams te WHERE te.tournament = t.id) < t.maxteams AND hash = ''";
        $result = runQuery($sql);
        $nopen = $result->rowCount();

        if ($ooffset >= $nopen)
            $ooffset -= $limit;

        //show open tournaments not started and not filled
        $sql = "SELECT * FROM tournament t WHERE t.password = '' AND (SELECT count(*) FROM teams te WHERE te.tournament = t.id) < t.maxteams AND hash = '' ORDER BY t.creation DESC LIMIT $limit OFFSET $ooffset";
        $result = runQuery($sql);
        $nrows = $result->rowCount();
        
        $open = array();
		if ($nrows > 0){
            while ($row = $result->fetch()){
                $tournid = $row['id'];

                $sql = "SELECT * FROM teams WHERE tournament = '$tournid'";
                if(!$result2 = $conn->query($sql)){ die('There was an error running the query [' . $conn->error . ']. SQL: ['. $sql .']'); }
                $nrows = $result2->rowCount();
                $row['teams'] = $nrows;

                array_push($open, $row);
            }
        }

        //how many mine
        $sql = "SELECT DISTINCT t.id AS id, t.name AS name, t.description AS description, t.maxteams AS maxteams, t.flex AS flex FROM teams te INNER JOIN gladiator_teams gt ON gt.team = te.id INNER JOIN gladiators g ON g.cod = gt.gladiator RIGHT JOIN tournament t ON t.id = te.tournament WHERE (g.master = '$user' AND t.manager != '$user') OR t.manager = '$user'";
        $result = runQuery($sql);
        $nmine = $result->rowCount();

        if ($moffset >= $nmine)
            $moffset -= $limit;
        if ($moffset < 0)
            $moffset = 0;

        //show tournaments which I am the manager or I have joined
        $sql = "SELECT DISTINCT t.id AS id, t.name AS name, t.description AS description, t.maxteams AS maxteams, t.flex AS flex FROM teams te INNER JOIN gladiator_teams gt ON gt.team = te.id INNER JOIN gladiators g ON g.cod = gt.gladiator RIGHT JOIN tournament t ON t.id = te.tournament WHERE (g.master = '$user' AND t.manager != '$user') OR t.manager = '$user' ORDER BY t.creation DESC LIMIT $limit OFFSET $moffset";
        $result = runQuery($sql);
        $nrows = $result->rowCount();
        
        $mytourn = array();
		if ($nrows > 0){
            while ($row = $result->fetch()){
                $tournid = $row['id'];

                $sql = "SELECT * FROM teams WHERE tournament = '$tournid'";
                if(!$result2 = $conn->query($sql)){ die('There was an error running the query [' . $conn->error . ']. SQL: ['. $sql .']'); }
                $nrows = $result2->rowCount();
                $row['teams'] = $nrows;

                array_push($mytourn, $row);
            }
        }

        $output['pages'] = array();

        $output['pages']['mine'] = array();
        $output['pages']['mine']['offset'] = $moffset;
        $output['pages']['mine']['total'] = $nmine;

        $output['pages']['open'] = array();
        $output['pages']['open']['offset'] = $ooffset;
        $output['pages']['open']['total'] = $nopen;

        $output['open'] = $open;
        $output['mytourn'] = $mytourn;

        echo json_encode($output);
    }
    else if ($action == "JOIN"){
        $name = $_POST['name'];

        if (isset($_POST['pass']))
            $pass = $_POST['pass'];
        else{
            $sql = "SELECT password FROM tournament WHERE name = '$name' AND manager = '$user'";
            $result = runQuery($sql);
            $nrows = $result->rowCount();

            if ($nrows == 0){
                $sql = "SELECT t.password AS password FROM tournament t INNER JOIN teams te ON t.id = te.tournament INNER JOIN gladiator_teams gt ON gt.team = te.id INNER JOIN gladiators g ON g.cod = gt.gladiator WHERE g.master = '$user' AND t.name = '$name'";
                $result = runQuery($sql);
            }

            $row = $result->fetch();
            $pass = $row['password'];
        }

        $sql = "SELECT * FROM tournament WHERE name = '$name' AND password = '$pass'";
        $result = runQuery($sql);
        $nrows = $result->rowCount();

        if ($nrows == 0)
            $output['status'] = "NOTFOUND";
        else{
            $output = array();
            $row = $result->fetch();
            $output['id'] = $row['id'];
            $output['name'] = $row['name'];
            $output['description'] = $row['description'];
            $output['pass'] = $row['password'];
            $output['hash'] = $row['hash'];
            $tournid = $row['id'];
            $output['status'] = "DONE";
        }
            
        echo json_encode($output);
    }
    elseif ($action == "LIST_TEAMS"){
        $output = array();

        if (isset($_POST['tourn'])){
            $tournid = $_POST['tourn'];
            $sql = "SELECT * FROM tournament WHERE id = '$tournid'";
        }
        else{
            $name = $_POST['name'];
            $pass = $_POST['pass'];
            $sql = "SELECT * FROM tournament WHERE name = '$name' AND password = '$pass'";
        }

        $result = runQuery($sql);
        $row = $result->fetch();
        if ($row['hash'] == ''){
            $tournid = $row['id'];
            $output['maxteams'] = $row['maxteams'];

            if ($user == $row['manager'])
                $output['manager'] = true;
            else
                $output['manager'] = false;

            $output['teams'] = array();

            $joined = check_joined($tournid, $conn);
            if ($joined !== false)
                $output['joined'] = $joined;

            //list id and name from temns in a giver tournament
            $sql = "SELECT id, name FROM teams WHERE tournament = '$tournid'";
            $result = runQuery($sql);
            while ($row = $result->fetch()){
                $team = array();
                $team['name'] = $row['name'];
                $team['id'] = $row['id'];
                $teamid = $row['id'];

                $sql = "SELECT * FROM gladiator_teams WHERE team = '$teamid'";
                if(!$result2 = $conn->query($sql)){ die('There was an error running the query [' . $conn->error . ']. SQL: ['. $sql .']'); }
                $nrows = $result2->rowCount();
                $team['glads'] = $nrows;

                array_push($output['teams'], $team);
            }

            $sql = "SELECT te.id FROM teams te WHERE (SELECT count(*) FROM gladiator_teams WHERE team = te.id) < 3 AND te.tournament = '$tournid'";
            $result = runQuery($sql);
            $nrows = $result->rowCount();
            
            if ($nrows == 0)
                $output['filled'] = true;
            else
                $output['filled'] = false;

            $output['status'] = "SUCCESS";
        }
        else{
            $output['status'] = "STARTED";
            $output['hash'] = $row['hash'];
        }

        echo json_encode($output);
    }
    elseif ($action == "TEAM_CREATE"){
        $name = $_POST['name'];
        $tname = $_POST['tname'];
        $tpass = $_POST['tpass'];
        $glad = $_POST['glad'];
        $showcode = $_POST['showcode'];
        $tourn = "";

        $sql = "SELECT id, hash FROM tournament WHERE name = '$tname' AND password = '$tpass'";
        $result = runQuery($sql);
        $nrows = $result->rowCount();

        if ($nrows == 0)
            echo "NOTFOUND";
        else{
            $row = $result->fetch();
            $tourn = $row['id'];

            if ($row['hash'] != ''){
                echo "STARTED";
            }
            elseif (check_joined($tourn, $conn) !== false)
                echo "ALREADYIN";
            else{
                $sql = "SELECT t.maxteams AS maxteams FROM tournament t INNER JOIN teams te ON te.tournament = t.id WHERE t.id = '$tourn'";
                $result = runQuery($sql);
                $nrows = $result->rowCount();
                $row = $result->fetch();
                if ($nrows > 0 && $nrows >= $row['maxteams'])
                    echo "FULL";
                else{
                    $sql = "SELECT name FROM teams WHERE name = '$name' AND tournament = $tourn";
                    $result = runQuery($sql);
                    $nrows = $result->rowCount();
                    if ($nrows == 0){
                        $vow = "aeiouy";
                        $con = "bcdfghjklmnprstvwxz";
                        
                        $word = "";
                        for($i=0 ; $i<3 ; $i++){
                            $rv = rand(0, strlen($vow)-1);
                            $rc = rand(0, strlen($con)-1);
                            
                            $word .= $con[$rc] . $vow[$rv]; 
                        }            
            
                        $sql = "INSERT INTO teams (name, tournament, password, modified) VALUES ('$name', $tourn, '$word', now())";
                        $result = runQuery($sql);
                        // $teamid = $conn->lastInsertId();
                        $teamid = $conn->lastInsertId();
            
                        $output = array();
                        $output['word'] = $word;
                        $output['id'] = $teamid;
                        echo json_encode($output);
        
                        $sql = "SELECT cod FROM gladiators WHERE master = '$user'";
                        $result = runQuery($sql);
                        $nrows = $result->rowCount();
        
                        if ($nrows > 0){
                            if ($showcode == 'true')
                                $sql = "INSERT INTO gladiator_teams (gladiator, team, visible) VALUES ('$glad', '$teamid', '1')";
                            else
                                $sql = "INSERT INTO gladiator_teams (gladiator, team) VALUES ('$glad', '$teamid')";
                            $result = runQuery($sql);
                        }

                        send_node_message(array('tournament list' => array()));
                        send_node_message(array('tournament teams' => array(
                            'id' => $tourn,
                            'name' => $tname,
                            'pass' => $tpass
                        )));
                    }
                    else
                        echo "EXISTS";
                }
            }
        }

    }
    elseif ($action == "TEAM"){
        $teamid = $_POST['id'];
        $sync = $_POST['sync'];
        $output = array();

        $sql = "SELECT modified FROM teams WHERE id = '$teamid'";
        $result = runQuery($sql);
        $row = $result->fetch();

        if ($row['modified'] != $sync){
            $output['sync'] = $row['modified'];
            $output['name'] = "";
            $output['word'] = "";
            $output['glads'] = array();

            $sql = "SELECT gt.gladiator, t.name, t.password, t.tournament, tn.flex FROM teams t INNER JOIN gladiator_teams gt ON t.id = gt.team INNER JOIN gladiators g ON g.cod = gt.gladiator INNER JOIN tournament tn ON tn.id = t.tournament WHERE gt.team = '$teamid' AND g.master = '$user'";
            $result = runQuery($sql);
            $nrows = $result->rowCount();
            if ($nrows > 0){
                $row = $result->fetch();
                $output['name'] = $row['name'];
                $output['word'] = $row['password'];
                $output['tourn'] = $row['tournament'];
                $output['flex'] = $row['flex'];
            }
            
            $sql = "SELECT g.cod, g.name, g.vstr, g.vagi, g.vint, g.skin, u.apelido, master FROM gladiators g INNER JOIN usuarios u ON u.id = g.master WHERE g.cod IN (SELECT gladiator FROM gladiator_teams WHERE team = '$teamid')";
            $result = runQuery($sql);

            if ($nrows == 0){
                while ($row = $result->fetch())
                    array_push($output['glads'], $row['apelido']);
            }
            else{
                while ($row = $result->fetch()){
                    if ($row['master'] == $user)
                        $row['owner'] = true;
                    array_push($output['glads'], $row);
                }
            }

            $joined  = false;
            if (isset($output['tourn']))
                $joined = check_joined($output['tourn'], $conn);
            if ($joined !== false)
                $output['joined'] = $joined;

            $output['status'] = "DONE";
        }
        else
            $output['status'] = "SYNCED";

        echo json_encode($output);
    }
    elseif ($action == "LEAVE_TEAM"){
        $teamid = $_POST['id'];

        $output = array();
        $sql = "SELECT t.id, t.name, t.password FROM teams te INNER JOIN tournament t ON t.id = te.tournament WHERE te.id = '$teamid'";
        $result = runQuery($sql);
        $nrows = $result->rowCount();

        if ($nrows == 0)
            $output['status'] = "NOTFOUND";
        else{
            $row = $result->fetch();
            $tournid = $row['id'];
            $tname = $row['name'];
            $tpass = $row['password'];

            $sql = "SELECT hash FROM tournament WHERE id = $tournid";
            $result = runQuery($sql);
            $row = $result->fetch();
            
            if ($row['hash'] != '')
                $output['status'] = "STARTED";
            else{
                $output['tourn'] = $tournid;
    
                $sql = "SELECT gt.id AS id FROM gladiator_teams gt INNER JOIN gladiators g ON g.cod = gt.gladiator WHERE gt.team = '$teamid' AND g.master = '$user'";
                $result = runQuery($sql);
        
                $ids = array();
                while ($row = $result->fetch())
                    array_push($ids, $row['id']);
                $ids = implode(",", $ids);
        
                $sql = "DELETE FROM gladiator_teams WHERE id IN ($ids)";
                $result = runQuery($sql);
        
                $sql = "SELECT id FROM gladiator_teams WHERE team = '$teamid'";
                $result = runQuery($sql);
                $nrows = $result->rowCount();
        
                if ($nrows == 0){
                    $sql = "DELETE FROM teams WHERE id = '$teamid'";
                    $result = runQuery($sql);
                    $output['status'] = "REMOVED";

                    send_node_message(array('tournament list' => array()));

                    send_node_message(array('tournament glads' => array(
                        'team' => $teamid,
                        'remove' => true,
                        'user' => $user
                    )));
                }
                else{
                    $sql = "UPDATE teams SET modified = now() WHERE id = '$teamid'";
                    $result = runQuery($sql);
                    $output['status'] = "LEFT";

                    send_node_message(array('tournament glads' => array( 'team' => $teamid )));
                }

                send_node_message(array('tournament teams' => array(
                    'id' => $tournid,
                    'name' => $tname,
                    'pass' => $tpass
                )));

            }

        }

        echo json_encode($output);
    }
    elseif ($action == "JOIN_TEAM"){
        $word = $_POST['pass'];
        $team = $_POST['team'];
        $glad = $_POST['glad'];
        $showcode = $_POST['showcode'];
        $output = array();

        $sql = "SELECT t.id, t.name, t.password FROM teams te INNER JOIN tournament t ON t.id = te.tournament WHERE te.id = '$team'";
        $result = runQuery($sql);
        $row = $result->fetch();

        $tourn = $row['id'];
        $tname = $row['name'];
        $tpass = $row['password'];

        $sql = "SELECT t.id FROM gladiators g INNER JOIN gladiator_teams gt ON gt.gladiator = g.cod INNER JOIN teams te ON gt.team = te.id INNER JOIN tournament t ON te.tournament = t.id WHERE t.id = '$tourn' AND g.master = '$user'";
        $result = runQuery($sql);
        $nrows = $result->rowCount();

        if ($nrows > 0)
            $output['status'] = "SIGNED";
        else{
            $sql = "SELECT t.id FROM tournament t WHERE t.id = '$tourn' AND t.hash = ''";
            $result = runQuery($sql);
            $nrows = $result->rowCount();
            
            if ($nrows == 0)
                $output['status'] = "STARTED";
            else{
                $output['tourn'] = $tourn;

                $sql = "SELECT * FROM teams WHERE id = '$team' AND password = '$word'";
                $result = runQuery($sql);
                $nrows = $result->rowCount();

                if ($nrows > 0){
                    $sql = "SELECT cod FROM gladiators WHERE master = '$user' AND cod = '$glad'";
                    $result = runQuery($sql);
                    $nrows = $result->rowCount();

                    if ($nrows > 0 ){
                        if ($showcode == 'true')
                            $sql = "INSERT INTO gladiator_teams (gladiator, team, visible) VALUES ('$glad', '$team', '1')";
                        else
                            $sql = "INSERT INTO gladiator_teams (gladiator, team) VALUES ('$glad', '$team')";
                        $result = runQuery($sql);
                        $sql = "UPDATE teams SET modified = now() WHERE id = '$team'";
                        $result = runQuery($sql);
            
                        $output['status'] = "SUCCESS";

                        send_node_message(array('tournament teams' => array(
                            'id' => $tourn,
                            'name' => $tname,
                            'pass' => $tpass
                        )));

                        send_node_message(array('tournament glads' => array( 'team' => $team )));
                    }
                    else
                        $output['status'] = "FAIL";

                }
                else
                    $output['status'] = "FAIL";
            }
        }

        echo json_encode($output);
    }
    elseif ($action == "ADD_GLAD"){
        $glad = $_POST['glad'];
        $showcode = $_POST['showcode'];
        $team = $_POST['team'];
        $pass = $_POST['pass'];
           
        $nglads = "SELECT count(*) FROM gladiator_teams WHERE team = '$team'";
        $signed = "SELECT count(*) FROM gladiator_teams gt INNER JOIN gladiators g ON g.cod = gt.gladiator WHERE gt.team = '$team' AND g.master = '$user'";
        $sql = "SELECT t.id AS tournid, t.name AS tname, t.password AS tpass, te.password AS pass, ($nglads) AS nglads, t.flex AS flex, ($signed) AS signed, t.hash FROM gladiators g INNER JOIN gladiator_teams gt ON gt.gladiator = g.cod INNER JOIN teams te ON te.id = gt.team INNER JOIN tournament t ON t.id = te.tournament WHERE g.master = '$user' AND te.id = '$team'";
        $result = runQuery($sql);
        $nrows = $result->rowCount();

        $output = array();
        if ($nrows == 0)
            $output['status'] = "NOTJOINED";
        else{
            $row = $result->fetch();
            if ($row['pass'] != $pass)
                $output['status'] = "PASSWORD";
            elseif ($row['nglads'] >= 3)
                $output['status'] = "FULL";
            elseif ($row['flex'] == '0' && $row['signed'] > 0)
                $output['status'] = "SIGNED";
            elseif ($row['hash'] != '')
                $output['status'] = "STARTED";
            else{
                $tourn = $row['tournid'];
                $tname = $row['tname'];
                $tpass = $row['tpass'];

                $sql = "SELECT * FROM gladiator_teams WHERE gladiator = '$glad' && team = '$team'";
                $result = runQuery($sql);
                $nrows = $result->rowCount();
                if ($nrows > 0)
                    $output['status'] = "SAMEGLAD";
                else{
                    $sql = "SELECT master FROM gladiators WHERE cod = '$glad'";
                    $result = runQuery($sql);
                    $row = $result->fetch();
                    if ($row['master'] != $user)
                        $output['status'] = "PERMISSION";
                    else{
                        if ($showcode == 'true')
                            $sql = "INSERT INTO gladiator_teams(gladiator, team, visible) VALUES ('$glad','$team', '1')";
                        else
                            $sql = "INSERT INTO gladiator_teams(gladiator, team) VALUES ('$glad','$team')";
                        $result = runQuery($sql);
                        $sql = "UPDATE teams SET modified = now() WHERE id = '$team'";
                        $result = runQuery($sql);
                        $output['status'] = "DONE";

                        send_node_message(array('tournament teams' => array(
                            'id' => $tourn,
                            'name' => $tname,
                            'pass' => $tpass
                        )));

                        send_node_message(array('tournament glads' => array( 'team' => $team )));

                    }
                }
            }
            
        }
        echo json_encode($output);
    }
    elseif ($action == "DELETE"){
        if (isset($_POST['tourn'])){
            $tournid = $_POST['tourn'];
            $sql = "SELECT id, manager, hash FROM tournament WHERE id = '$tournid'";
        }
        else{
            $name = $_POST['name'];
            $pass = $_POST['pass'];
            $sql = "SELECT id, manager, hash FROM tournament WHERE name = '$name' AND password = '$pass'";
        }

        $result = runQuery($sql);
        $row = $result->fetch();
        $tournid = $row['id'];
        $manager = $row['manager'];

        if ($manager != $user)
            $output['status'] = "PERMISSION";
        elseif ($row['hash'] != '')
            $output['status'] = "STARTED";
        else{
            $sql = "SELECT id FROM teams WHERE tournament = '$tournid'";
            $result = runQuery($sql);
            $nrows = $result->rowCount();

            if ($nrows == 0){
                $sql = "DELETE FROM tournament WHERE id = '$tournid'";
                $result = runQuery($sql);
                $output['status'] = "DELETED";

                send_node_message(array('tournament list' => array()));

                send_node_message(array('tournament teams' => array(
                    'id' => $tournid,
                    'remove' => true
                )));

            }
            else {
                $output['status'] = "NOTEMPTY";
            }
        }
        echo json_encode($output);
    }
    elseif ($action == "REMOVE_GLAD"){
        $team = $_POST['team'];
        $glad = $_POST['glad'];

        $output = array();

        $sql = "SELECT t.id, t.name, t.password, t.hash FROM tournament t INNER JOIN teams te ON te.tournament = t.id WHERE te.id = $team";
        $result = runQuery($sql);
        $row = $result->fetch();

        if ($row['hash'] != '')
            $output['status'] = "STARTED";
        else{
            $tourn = $row['id'];
            $tname = $row['name'];
            $tpass = $row['password'];

            $sql = "SELECT * FROM gladiators g INNER JOIN gladiator_teams gt ON g.cod = gt.gladiator WHERE g.master = '$user' AND gt.team = '$team' AND gt.gladiator = '$glad'";
            $result = runQuery($sql);
            $nrows = $result->rowCount();

            if ($nrows == 0)
                $output['status'] = "NOTFOUND";
            else{
                $sql = "DELETE FROM gladiator_teams WHERE gladiator = '$glad' AND team = '$team'";
                $result = runQuery($sql);

                $sql = "SELECT t.id AS id FROM tournament t INNER JOIN teams te ON te.tournament = t.id WHERE te.id = '$team'";
                $result = runQuery($sql);
                $row = $result->fetch();
                $output['tournid'] = $row['id'];

                $sql = "SELECT * FROM gladiator_teams WHERE team = '$team'";
                $result = runQuery($sql);
                $nrows = $result->rowCount();
                
                if ($nrows > 0){
                    $sql = "UPDATE teams SET modified = now() WHERE id = '$team'";
                    $result = runQuery($sql);
                    $output['status'] = "DONE";

                    send_node_message(array('tournament glads' => array( 'team' => $team )));
                }
                else{
                    $sql = "DELETE FROM teams WHERE id = '$team'";
                    $result = runQuery($sql);
                    $output['status'] = "REMOVED";

                    send_node_message(array('tournament list' => array()));

                    send_node_message(array('tournament glads' => array(
                        'team' => $team,
                        'remove' => true,
                        'user' => $user
                    )));
                }

                send_node_message(array('tournament teams' => array(
                    'id' => $tourn,
                    'name' => $tname,
                    'pass' => $tpass
                )));

            }
        }
        
        echo json_encode($output);
    }
    elseif ($action == "KICK"){
        $team = $_POST['teamname'];
        $tname = $_POST['name'];
        $tpass = $_POST['pass'];

        $output = array();

        $sql = "SELECT te.tournament, t.name, t.password, te.id AS id FROM teams te INNER JOIN tournament t ON t.id = te.tournament WHERE t.manager = '$user' AND t.name = '$tname' AND t.password = '$tpass' AND te.name = '$team'";
        $result = runQuery($sql);
        $nrows = $result->rowCount();

        if ($nrows == 0)
            $output['status'] = "NOTFOUND";
        else{
            $row = $result->fetch();
            $team = $row['id'];
            $tourn = $row['tournament'];
            $tname = $row['name'];
            $tpass = $row['password'];

            $sql = "DELETE FROM gladiator_teams WHERE team = '$team'";
            $result = runQuery($sql);
            $sql = "DELETE FROM teams WHERE id = '$team'";
            $result = runQuery($sql);
            
            $output['status'] = "DONE";

            send_node_message(array('tournament list' => array()));

            send_node_message(array('tournament teams' => array(
                'id' => $tourn,
                'name' => $tname,
                'pass' => $tpass
            )));

            send_node_message(array('tournament glads' => array(
                'team' => $team,
                'remove' => true,
                'user' => $user
            )));
        }

        echo json_encode($output);
    }
    elseif ($action == "START"){
        $tname = $_POST['name'];
        $tpass = $_POST['pass'];

        $output = array();

        $sql = "SELECT t.id AS id, t.maxtime FROM tournament t WHERE t.name = '$tname' AND t.password = '$tpass' AND t.manager = '$user' AND t.hash = ''";
        $result = runQuery($sql);
        $nrows = $result->rowCount();
        $row = $result->fetch();
        
        if ($nrows == 0){
            $output['status'] = "NOTFOUND";
        }
        else{
            $tournid = $row['id'];
            $maxtime = $row['maxtime'];
            $sql = "SELECT id FROM teams WHERE tournament = '$tournid'";
            $result = runQuery($sql);
            $nteams = $result->rowCount();

            if ($nteams <= 1)
                $output['status'] = "FEWTEAMS";
            else{
                $teams = array();
                while($row = $result->fetch())
                    array_push($teams, $row['id']);
                shuffle($teams);

                $sql = "SELECT te.id FROM teams te WHERE (SELECT count(*) FROM gladiator_teams WHERE team = te.id) < 3 AND te.tournament = '$tournid'";
                $result = runQuery($sql);
                $nrows = $result->rowCount();
                
                if ($nrows > 0)
                    $output['status'] = "FEWGLADS";
                else{
                    $hash = substr(md5('tourn'.microtime(true)*rand()), 0,16);
                    $sql = "UPDATE tournament t SET t.hash = '$hash' WHERE t.id = '$tournid'";
                    $result = runQuery($sql);

                    create_chat($conn, $tournid);

                    send_node_message(array('tournament list' => array()));
                    
                    send_node_message(array('tournament teams' => array(
                        'id' => $tournid,
                        'start' => true 
                    )));

                    $ngroups = ceil($nteams / 5);
                    $groups = array();
                    for ($i=0 ; $i<$ngroups ; $i++){
                        $sql = "INSERT INTO `groups`(round, deadline) VALUES ('1', ADDTIME(now(), TIME('$maxtime')))";
                        $result = runQuery($sql);
                        array_push($groups, $conn->lastInsertId());
                    }

                    foreach($teams as $i => $team){
                        $group = $groups[$i % $ngroups];
                        $sql = "INSERT INTO group_teams (team, groupid) VALUES ('$team', '$group')";
                        $result = runQuery($sql);
                    }

                    $output['status'] = "DONE";
                    $output['hash'] = $hash;
                }
            }
        }

        echo json_encode($output);
    }
    function check_joined($tournid, $conn){
        $user = $_SESSION['user'];

        $sql = "SELECT t.id AS id FROM teams t INNER JOIN gladiator_teams gt ON gt.team = t.id INNER JOIN gladiators g ON g.cod = gt.gladiator WHERE t.tournament = '$tournid' AND g.master = '$user'";
        
        $result = runQuery($sql);
        $nrows = $result->rowCount();
        
        if ($nrows > 0){
            $row = $result->fetch();
            return $row['id'];
        }
        else
            return false;
            
    }

    function create_chat($conn, $tourn){
        //check if room exists
        $sql = "SELECT cr.id, t.name, t.manager FROM chat_rooms cr RIGHT JOIN tournament t ON t.name = cr.name WHERE t.id = $tourn";
        $result = runQuery($sql);

        $row = $result->fetch();
        $name = $row['name'];
        $manager = $row['manager'];

        if (!is_null($row['id']))
            $name .= "_". substr(md5(rand()), 0,4);

        //create chat room
        $sql = "INSERT INTO chat_rooms(name, creation, description, public) VALUES ('$name', now(3), 'Sala de discussão do torneio $name', 0)";
        $result = runQuery($sql);
        $room = $conn->lastInsertId();

        //get who is in the tournament
        $sql = "SELECT DISTINCT g.master FROM teams te INNER JOIN gladiator_teams glt ON glt.team = te.id INNER JOIN gladiators g ON g.cod = glt.gladiator WHERE te.tournament = $tourn";
        $result = runQuery($sql);

        $masters = array();
        while ($row = $result->fetch()){
            array_push($masters, $row['master']);
        }
        if (!in_array($manager, $masters))
            array_push($masters, $manager);

        foreach ($masters as $master){
            $privilege = 1;
            if ($master == $manager)
                $privilege = 0;
            //insert every user from the tournament in the new chat room
            $sql = "INSERT INTO chat_users (room, user, joined, visited, privilege) VALUES ($room, '$master', now(3), now(3), $privilege)";
            $result = runQuery($sql);
        }

        $sql = "INSERT INTO chat_messages (room, time, sender, message, `system`) VALUES ($room, now(3), '$manager', 'Sala $name criada', 1)";
        $result = runQuery($sql);

        send_node_message(array(
            'chat notification' => array(
                'room' => $room
            )
        ));      
    }
?>