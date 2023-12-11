<?php
	include_once "connection.php";

    if (isset($_GET['g'])){
        $remake = $_GET['g'];
    }
    
    if (!isset($_GET['r'])){
        $round = "0";
    }
    else{
        $round = $_GET['r'];
    }

    $tname = 'CharCode 2019';

    //tournament
    $sql = "SELECT id, maxtime FROM tournament WHERE name = '$tname'";
    $result = runQuery($sql);
    $row = $result->fetch();
    $tourn = $row['id'];
    $maxtime =$row['maxtime'];

    //teams
    if ($tourn != ''){
        $sql = "SELECT id FROM teams WHERE tournament = $tourn";
        $result = runQuery($sql);
        $teams = array();
        while ($row = $result->fetch()){
            array_push($teams, $row['id']);
        }
        $teams = implode(',', $teams);
    }

    //gladiator_teams
    if ($teams && $teams != ''){
        $sql = "SELECT id FROM gladiator_teams WHERE team IN ($teams)";
        $result = runQuery($sql);
        $glt = array();
        while ($row = $result->fetch()){
            array_push($glt, $row['id']);
        }
        $glt = implode(',', $glt);
    
        //group_teams
        $sql = "SELECT id FROM group_teams WHERE team IN ($teams)";
        $result = runQuery($sql);
        $grt = array();
        while ($row = $result->fetch()){
            array_push($grt, $row['id']);
        }
        $grt = implode(',', $grt);
    }

    //groups
    if ($grt && $grt != ''){
        $sql = "SELECT groupid FROM group_teams WHERE id IN ($grt)";
        $result = runQuery($sql);
        $groups = array();
        while ($row = $result->fetch()){
            array_push($groups, $row['groupid']);
        }
        $groups = implode(',', $groups);

        //delete and update
        if ($grt != ''){
            $sql = "DELETE FROM group_teams WHERE id IN ($grt) AND groupid IN (SELECT id FROM `groups` WHERE round > $round)";
            $result = runQuery($sql);

            if (isset($remake)){
                $sql = "UPDATE group_teams SET lasttime = NULL WHERE id IN ($grt) AND groupid = $remake";
                $result = runQuery($sql);
            }
            else if ($round > 0){
                $sql = "UPDATE group_teams SET gladiator = NULL, lasttime = NULL WHERE id IN ($grt) AND groupid IN (SELECT id FROM `groups` WHERE round = $round)";
                $result = runQuery($sql);
            }
        }
    }

    if (isset($groups) && $groups != ''){
        $sql = "DELETE FROM `groups` WHERE id IN ($groups) AND round > $round";
        $result = runQuery($sql);

        if (isset($remake)){
            $sql = "UPDATE `groups` SET log = NULL, locked = NULL WHERE id = $remake";
            $result = runQuery($sql);
        }
        else if ($round > 0){
            $sql = "UPDATE `groups` SET log = NULL, locked = NULL, deadline = ADDTIME(now(), TIME('$maxtime')) WHERE id IN ($groups) AND round = $round";
            $result = runQuery($sql);
        }
    }

    if ($glt && $glt != ''){
        $r = "";
        if (isset($remake))
            $r = "AND gladiator IN (SELECT gladiator FROM group_teams WHERE groupid = $remake)";

        $sql = "UPDATE gladiator_teams SET dead = 0 WHERE id IN ($glt) AND dead >= $round $r";
        $result = runQuery($sql);
    }

    if ($round == 0 && $tourn != ''){
        $sql = "UPDATE tournament SET hash = '' WHERE id = $tourn";
        $result = runQuery($sql);
    }

    if (isset($_GET['insert'])){
        if ($glt && $glt != ''){
            $sql = "DELETE FROM gladiator_teams WHERE id IN ($glt)";
            $result = runQuery($sql);
        }

        if ($teams && $teams != ''){
            $sql = "DELETE FROM teams WHERE id IN ($teams)";
            $result = runQuery($sql);
        }

        if ($tourn != ''){
            $sql = "DELETE FROM tournament WHERE id = $tourn";
            $result = runQuery($sql);
        }

        $sql = "INSERT INTO `tournament` (`id`, `hash`, `name`, `password`, `description`, `creation`, `maxteams`, `flex`, `manager`, `maxtime`) VALUES (59, '', 'CharCode 2019', 'ifsulcharq', 'Torneio CharCode 2019.', '2019-11-02 00:08:36', 50, 1, 277, '00:30:00');";
        $result = runQuery($sql);

        $sql = "INSERT INTO `teams` (`id`, `name`, `password`, `tournament`, `modified`) VALUES (430, 'sei lá', 'ryjiky', 59, '2019-11-20 09:28:45'), (431, 'CodeBrothers', 'macepa', 59, '2019-11-23 23:33:39'), (432, 'JGT', 'fakeni', 59, '2019-11-25 23:38:28'), (433, 'Gladiadores do Terceiro Milênio', 'cizady', 59, '2019-11-27 19:41:42'), (435, 'GL0', 'baramu', 59, '2019-11-25 22:53:26'), (436, 'Allies.. for now', 'deziso', 59, '2019-11-26 08:44:34'), (437, 'Super Alone', 'wulumi', 59, '2019-11-27 18:45:11'), (438, 'CharCoders', 'bydydo', 59, '2019-12-01 20:01:47'), (439, 'Cotangente De X', 'rawiho', 59, '2019-11-27 23:39:18'), (440, 'GladVengers', 'lelyto', 59, '2019-11-28 21:08:33'), (442, 'Polar', 'wycati', 59, '2019-12-01 23:24:35'), (444, 'DeepCode', 'zuzoxe', 59, '2019-11-30 22:48:46');";
        $result = runQuery($sql);

        $sql = "INSERT INTO `gladiator_teams` (`id`, `gladiator`, `team`, `visible`, `dead`) VALUES (1011, 151, 430, 0, 0), (1012, 150, 430, 0, 0), (1013, 152, 430, 0, 0), (1014, 197, 431, 1, 0), (1015, 141, 431, 0, 0), (1016, 215, 431, 1, 0), (1017, 180, 432, 1, 0), (1018, 185, 433, 1, 0), (1020, 175, 435, 1, 0), (1021, 169, 435, 0, 0), (1022, 156, 436, 0, 0), (1023, 218, 435, 1, 0), (1024, 198, 432, 1, 0), (1025, 204, 432, 1, 0), (1026, 158, 436, 0, 0), (1027, 161, 436, 0, 0), (1028, 135, 437, 0, 0), (1031, 131, 437, 0, 0), (1032, 132, 437, 0, 0), (1033, 191, 433, 0, 0), (1034, 144, 433, 1, 0), (1035, 124, 438, 0, 0), (1037, 219, 439, 1, 0), (1038, 223, 439, 1, 0), (1039, 216, 439, 1, 0), (1040, 173, 438, 0, 0), (1041, 222, 440, 0, 0), (1042, 226, 440, 0, 0), (1043, 225, 440, 0, 0), (1045, 214, 442, 0, 0), (1047, 202, 444, 0, 0), (1048, 119, 444, 1, 0), (1049, 230, 444, 1, 0), (1050, 233, 442, 1, 0), (1051, 224, 438, 1, 0), (1052, 235, 442, 1, 0);";
        $result = runQuery($sql);

        $sql = "DELETE FROM chat_users WHERE room = (SELECT id FROM chat_rooms WHERE name = '$tname')";
        $result = runQuery($sql);

        $sql = "DELETE FROM chat_messages WHERE room = (SELECT id FROM chat_rooms WHERE name = '$tname')";
        $result = runQuery($sql);

        $sql = "DELETE FROM chat_rooms WHERE name = '$tname'";
        $result = runQuery($sql);

    }

    echo "DONE";
?>