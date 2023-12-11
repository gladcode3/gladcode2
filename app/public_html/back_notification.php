<?php
    session_start();
    include_once "connection.php";
    $user = $_SESSION['user'];
    $output = array();
    $action = $_POST['action'];

    if ($action == "GET"){
        //user info
        $sql = "SELECT lvl, xp, silver FROM usuarios WHERE id = '$user'";
        $result = runQuery($sql);

        $row = $result->fetch();
        $lvl = $row['lvl'];
        $xp = $row['xp'];
        
        $output['user'] = array();
        $output['user']['lvl'] = $lvl;
        $output['user']['xp'] = $xp;
        $output['user']['silver'] = $row['silver'];
        
        //set active time
        $sql = "UPDATE usuarios SET ativo = now() WHERE id = '$user'";
        $result = runQuery($sql);

        //message
        $sql = "SELECT u.id FROM messages m INNER JOIN usuarios u ON u.id = m.sender WHERE receiver = '$user' AND isread = '0'";
        $result = runQuery($sql);
        
        $output['messages'] = $result->rowCount();
        
        //pending friend requests
        $sql = "SELECT u.id FROM amizade a INNER JOIN usuarios u ON u.id = a.usuario1 WHERE usuario2 = '$user' AND pendente = 1";
        $result = runQuery($sql);
        
        $output['friends'] = $result->rowCount();
        
        //gladiators remaining
        $sql = "SELECT master FROM gladiators WHERE master = '$user'";
        $result = runQuery($sql);

        $nglads = $result->rowCount();

        //calc max glads according to master lvl
        $initglad = 1;
        $gladinterval = 10;
        $maxglads = 6;
        $limit = min($maxglads, $initglad + floor($lvl/$gladinterval));
        
        $output['glads'] = array();
        $output['glads']['remaining'] = $limit - $nglads;

        //gladiators in need of update
        $version = file_get_contents("version");

        $sql = "SELECT master FROM gladiators WHERE master = '$user' AND version != '$version'";
        $result = runQuery($sql);
        
        $output['glads']['obsolete'] = $result->rowCount();
        
        //reports
        $output['reports'] = array();
        $sql = "SELECT r.id FROM reports r INNER JOIN gladiators g ON g.cod = r.gladiator WHERE gladiator IN (SELECT cod FROM gladiators WHERE master = '$user') AND isread = '0'";
        $result = runQuery($sql);
        $output['reports']['ranked'] = $result->rowCount();

        $sql = "SELECT d.id FROM duels d WHERE d.isread = 0 AND d.log IS NOT NULL AND (d.user1 = $user OR d.user2 = $user)";
        $result = runQuery($sql);
        $output['reports']['duel'] = $result->rowCount();

        //duels
        $sql = "SELECT d.id FROM duels d WHERE d.log IS NULL AND d.user2 = '$user'";
        $result = runQuery($sql);
        $output['duels'] = $result->rowCount();
        
        //news
        $sql = "SELECT id FROM news WHERE time > (SELECT read_news FROM usuarios WHERE id = $user)";
        $result = runQuery($sql);
        $output['news'] = $result->rowCount();

        $output['status'] = "SUCCESS";
    }
    elseif ($action == "SUMMARY"){
        $hash = $_POST['hash'];

        $sql = "SELECT u.lvl, u.xp, u.silver, g.mmr, g.name, g.skin, g.vstr, g.vint, g.vagi, g.cod AS 'id' FROM usuarios u INNER JOIN gladiators g ON g.master = u.id INNER JOIN reports r ON r.gladiator = g.cod INNER JOIN logs l ON l.id = r.log WHERE u.id = $user AND l.hash = '$hash'";
        $result = runQuery($sql);
        $row = $result->fetch();

        $output['lvl'] = $row['lvl'];
        $output['xp'] = $row['xp'];
        $output['silver'] = $row['silver'];
        $output['glad'] = array(
            'id' => $row['id'],
            'name' => $row['name'],
            'skin' => $row['skin'],
            'vstr' => $row['vstr'],
            'vagi' => $row['vagi'],
            'vint' => $row['vint'],
            'mmr' => $row['mmr']
        );
        $output['status'] = "SUCCESS";
    }

    echo json_encode($output);
?>