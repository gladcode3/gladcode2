<?php
	session_start();
    include_once "connection.php";
    $user = $_SESSION['user'];
    session_write_close();
    $action = $_POST['action'];
    $output = array();
    date_default_timezone_set('America/Sao_Paulo');
    include("back_node_message.php");

    if ($action == "ROOMS"){
        $sql = "SELECT cr.id, cr.name, cu.visited FROM chat_rooms cr INNER JOIN chat_users cu ON cr.id = cu.room INNER JOIN chat_messages cm ON cm.room = cr.id WHERE cu.user = '$user' ORDER BY cm.time DESC";
        $result = runQuery($sql);
        
        $output['room'] = array();
        while ($row = $result->fetch()){
            array_push($output['room'], $row);
        }

        $output['status'] = "SUCCESS";

    }
    else if ($action == "MESSAGES"){
        $id = $_POST['id'];
        $first = $_POST['first'];
        $sync = $_POST['sync'];
        
        if (isset($_POST['visited']))
            $visited = $_POST['visited'];
        else
            $visited = '';

        //check if user is in the room and not banned
        $sql = "SELECT cu.id FROM chat_users cu WHERE cu.user = '$user' AND cu.room = $id";
        $result = runQuery($sql);
        $nrows = $result->rowCount();

        if ($nrows == 0)
            $output['status'] = "NOTALLOWED";
        else{
            //check if banned
            $sql = "SELECT cre.time FROM chat_restrictions cre WHERE cre.ban = 1 AND cre.user = '$user' AND cre.room = $id";
            $result = runQuery($sql);
            $nrows = $result->rowCount();

            if ($nrows == 0)
                $bantime = "";
            else{
                $row = $result->fetch();
                $bantime = "AND cm.time <= '". $row['time'] ."'";
            }

            $limit = 30;

            if ($sync == 'true' && $first == 0)
                $sync = "AND UNIX_TIMESTAMP(cm.time) > $visited";
            else{
                $sync = '';
            }

            if ($first == 0)
                $first = '';
            else{
                $first = "AND cm.id < $first";
            }


            //get message info
            $sql = "SET lc_time_names = 'pt_BR'";
            $result = runQuery($sql);
            $sql = "SELECT UNIX_TIMESTAMP(now(3)) AS visited, UNIX_TIMESTAMP(cm.time) AS realtime, cm.id, cm.message, DATE_FORMAT(cm.time, '%e %b %k:%i') AS time, um.apelido, cm.system, um.id AS userid, um.foto FROM chat_messages cm INNER JOIN chat_rooms cr ON cr.id = cm.room INNER JOIN chat_users cu ON cu.room = cr.id INNER JOIN usuarios um ON um.id = cm.sender WHERE cr.id = $id AND cu.user = '$user' $bantime $first $sync ORDER BY cm.time DESC, cm.id DESC LIMIT $limit";
            $result = runQuery($sql);
            $output['sql'] = $sql;

            $output['messages'] = array();
            while ($row = $result->fetch()){
                if ($row['userid'] == $user)
                    $row['me'] = true;
                $row['message'] = htmlspecialchars($row['message']);

                if (!isset($output['visited']))
                    $output['visited'] = $row['visited'];

                unset($row['visited']);
                array_push($output['messages'], $row);
            }

            $sql = "UPDATE chat_users SET visited = now(3) WHERE room = $id AND user = '$user'";
            $result = runQuery($sql);

            $output['status'] = "SUCCESS";

        }
    }
    else if ($action == "SEND"){
        $message = trim($_POST['message']);

        if (isset($_POST['room']))
            $room = $_POST['room'];
        else
            $room = '';

        if (isset($_POST['emoji']) && is_array($_POST['emoji']))
            $emojis = json_encode(array_slice($_POST['emoji'], 0, 30), JSON_UNESCAPED_UNICODE);
        else
            $emojis = "";
        
        $sql = "UPDATE usuarios SET emoji = '$emojis' WHERE id = $user";
        $result = runQuery($sql);

        if ($message == '')
            $output['status'] = "EMPTY";
        else{
            $command = parseCommand($message);
            if ($command){
                $output = execCommand($conn, $command, $user, $room);
            }
            else if ($room == '')
                $output['status'] = "NOROOM";
            else{
                //if user can post
                $sql = "SELECT id FROM chat_users cu WHERE cu.user = '$user' AND cu.room = $room";
                $result = runQuery($sql);
                $nrows = $result->rowCount();

                if ($nrows == 0)
                    $output['status'] = "NOTJOINED";
                else{
                    //check if user have any restrictions
                    $sql = "SELECT ban, time FROM chat_restrictions WHERE user = '$user' AND room = $room";
                    $result = runQuery($sql);
                    $row = $result->fetch();
                    $nrows = $result->rowCount();
                    if ($nrows > 0 && $row['ban'] == 0){
                        $output['status'] = "SILENCED";
                        $output['time'] = $row['time'];
                    }
                    else if ($nrows > 0 && $row['ban'] == 1)
                        $output['status'] = "BANNED";
                    else{
                        //send message
                        $sql = "INSERT INTO chat_messages (room, time, sender, message) VALUES ($room, now(3), '$user', '$message')";
                        $result = runQuery($sql);

                        $output['status'] = "SENT";
                    }
                }
            }

            send_node_message(array(
                'chat notification' => array(
                    'room' => $room
                )
            ));
        }

    }
    else if ($action == "NOTIFICATIONS"){
        $visited = json_decode($_POST['visited'], true);

        //all rooms I am in and not banned
        $sql = "SELECT cr.id FROM chat_rooms cr INNER JOIN chat_users cu ON cu.room = cr.id WHERE cu.user = '$user' AND cr.id NOT IN (SELECT room FROM chat_restrictions WHERE ban = 1 AND user = '$user')";
        $result = runQuery($sql);
        $nrows = $result->rowCount();

        if ($nrows == 0){
            $output['status'] = "NOROOM";
        }
        else{
            $output['notifications'] = array();

            while ($row = $result->fetch()){
                $id = $row['id'];
                $vis_room = $visited[$id];
                $sql = "SELECT count(*) AS notif FROM chat_messages cm INNER JOIN chat_users cu ON cu.room = cm.room WHERE UNIX_TIMESTAMP(cm.time) > '$vis_room' AND cm.room = $id AND cu.user = '$user'";
                if(!$result2 = $conn->query($sql)){ die('There was an error running the query [' . $conn->error . ']. SQL: ['. $sql .']'); }
                
                $row2 = $result2->fetch();
                $output['notifications'][$id] = $row2['notif'];
            }

            $output['status'] = "SUCCESS";
        }
        
    }
    else if ($action == "EMOJI"){
        if (isset($user)){
            $sql = "SELECT emoji FROM usuarios WHERE id = $user";
            $result = runQuery($sql);
            $row = $result->fetch();

            $output['emoji'] = $row['emoji'];
            $output['status'] = "SUCCESS";
        }
        else{
            $output['status'] = "NOTLOGGED";
        }
    }

    echo json_encode($output, JSON_UNESCAPED_UNICODE);

    function parseCommand($message){
        if ($message[0] != '/')
            return false;
        else{
            $message = substr($message, 1);
            $message = explode(" ", $message);
            $args = array();
            foreach ($message as $i => $val){
                if ($i == 0)
                    $command = $val;
                else
                    array_push($args, $val);
            }

            return array(
                'command' => $command,
                'args' => $args
            );
        }
    }

    function execCommand($conn, $command, $user, $room){    
        $args = $command['args'];
        $command = $command['command'];
        $output = array();

        if ($command == 'join'){
            $room = implode(" ", $args);
            //search for the room
            $sql = "SELECT id FROM chat_rooms WHERE name = '$room'";
            $result = runQuery($sql);
            $nrows = $result->rowCount();

            if ($nrows == 0)
                $output['status'] = "NOTFOUND";
            else{
                $row = $result->fetch();
                $id = $row['id'];
                $desc = $row['description'];

                $sql = "SELECT count(*) FROM chat_users WHERE user = '$user' AND room = $id";
                $result = runQuery($sql);

                $row = $result->fetch();
                if ($row['count(*)'] > 0)
                    $output['status'] = "ALREADYJOINED";
                else{
                    $sql = "SELECT apelido from usuarios WHERE id = '$user'";
                    $result = runQuery($sql);
                    $row = $result->fetch();
                    $nick = $row['apelido'];

                    $sql = "INSERT INTO chat_users (room, user, joined, visited) VALUES ($id, '$user', now(3), now(3))";
                    $result = runQuery($sql);
                    $output['status'] = "JOINED";
                    $output['id'] = $id;
                    $output['name'] = $room;

                    $sql = "INSERT INTO chat_messages (room, time, sender, message, `system`) VALUES ($id, now(3), '$user', '$nick entrou na sala', 1)";
                    $result = runQuery($sql);

                    //if there is no user in the room, I am the new master
                    $sql = "SELECT count(*) FROM chat_users WHERE room = $id";
                    $result = runQuery($sql);
                    $row = $result->fetch();
                    if ($row['count(*)'] == 1){
                        $sql = "UPDATE chat_users SET privilege = 0 WHERE room = $id AND user = '$user'";
                        $result = runQuery($sql);

                        $output['owner'] = true;
                    }
                }
            }
        }
        else if ($command == 'leave'){
            $argstring = implode(" ", $args);
            if ($room != '' && $argstring == ''){
                $sql = "SELECT name FROM chat_rooms WHERE id = $room";
                $result = runQuery($sql);
                $row = $result->fetch();
                $room = $row['name'];
            }
            else
                $room = $argstring;
            
            //search for the room
            $sql = "SELECT cr.id, u.apelido FROM chat_rooms cr INNER JOIN chat_users cu ON cu.room = cr.id INNER JOIN usuarios u ON u.id = cu.user WHERE cr.name = '$room' AND cu.user = '$user'";
            $result = runQuery($sql);
            $nrows = $result->rowCount();
            if ($nrows == 0)
                $output['status'] = "NOTFOUND";
            else{
                $row = $result->fetch();
                $id = $row['id'];
                $nick = $row['apelido'];

                $sql = "INSERT INTO chat_messages (room, time, sender, message, `system`) VALUES ($id, now(3), '$user', '$nick saiu da sala', 1)";
                $result = runQuery($sql);

                $sql = "DELETE FROM chat_users WHERE user = '$user' AND room = $id";
                $result = runQuery($sql);

                $output['status'] = "LEFT";
                $output['name'] = $room;

                //check if there are anyone left in the room
                $sql = "SELECT count(*) FROM chat_users WHERE room = $id";
                $result = runQuery($sql);
                $row = $result->fetch();

                if ($row['count(*)'] == 0){
                    //if there is not, delete the room
                    $sql = "DELETE FROM chat_messages WHERE room = $id";
                    $result = runQuery($sql);
                    $sql = "DELETE FROM chat_rooms WHERE id = $id";
                    $result = runQuery($sql);
                    $sql = "DELETE FROM chat_restrictions WHERE room = $id";
                    $result = runQuery($sql);
                }
            }
        }
        else if ($command == 'list' || $command == 'show'){
            if ($args[0] == 'rooms'){
                if ($room == '')
                    $output['status'] = "NOROOM";
                else{
                    $sql = "SELECT cr.name, cr.description, (SELECT count(*) FROM chat_users WHERE room = cr.id) AS members FROM chat_rooms cr WHERE cr.public = 1";
                    $result = runQuery($sql);
                    
                    $output['room'] = array();
                    while($row = $result->fetch()){
                        array_push($output['room'], $row);  
                    }
                    $output['status'] = "LIST";
                }
            }
            else if ($args[0] == 'users' ){
                if ($room == '')
                    $output['status'] = "NOROOM";
                else{
                    $sql = "SET lc_time_names = 'pt_BR'";
                    $result = runQuery($sql);
                    $sql = "SELECT u.apelido, cu.privilege, DATE_FORMAT(cu.joined, '%e %b %Y') AS since, TIMESTAMPDIFF(SECOND, u.ativo, now()) AS login FROM usuarios u INNER JOIN chat_users cu ON u.id = cu.user WHERE cu.room = $room ORDER BY cu.privilege, login, u.apelido";
                    $result = runQuery($sql);
                    
                    $output['user'] = array();
                    while($row = $result->fetch()){
                        if ($row['privilege'] == 0)
                            $row['privilege'] = "Líder";
                        else
                            $row['privilege'] = "Membro";

                        if ($row['login'] < 3600){
                            $n = floor($row['login'] / 60) ." minuto";
                        }
                        else if ($row['login'] < 86400){
                            $n = floor($row['login'] / 3600) ." hora";
                        }
                        else if ($row['login'] < 2592000){
                            $n = floor($row['login'] / 86400) ." dia";
                        }
                        else{
                            $n = floor($row['login'] / 2592000) ." mes";
                            if ($n > 1)
                                $n .= 'e';
                        }

                        if ($n > 1)
                            $n .= "s";
                        $row['login'] = $n;

                        array_push($output['user'], $row);  
                    }
                    $output['status'] = "LIST";
                }

            }
        }
        else if ($command == 'create'){
            $str = implode(" ", $args);

            $public = 1;
            if (strpos($str, '-pvt') !== false){
                $public = 0;
                $str = implode(" ", explode("-pvt", $str));
            }

            preg_match('/ -d ([áàâãéêíóõôúç\w\s]+)/', $str, $m);
            if (isset($m) && is_array($m) && count($m) > 1)
                $description = trim($m[1]);
            else
                $description = '';

            preg_match('/([áàâãéêíóõôúç\w\s]+)/', $str, $m);
            if (isset($m) && is_array($m) && count($m) > 1)
                $name = trim($m[1]);
            else
                $name = '';

            if ($name == '')
                $output['status'] = "BLANK";
            else{
                //check if room exists
                $sql = "SELECT id FROM chat_rooms WHERE name = '$name'";
                $result = runQuery($sql);
                $nrows = $result->rowCount();

                $row = $result->fetch();
                if ($nrows > 0){
                    $output['status'] = "EXISTS";
                    $output['name'] = $name;
                }
                else{
                    $sql = "SELECT apelido FROM usuarios WHERE id = '$user'";
                    $result = runQuery($sql);
                    $row = $result->fetch();
                    $nick = $row['apelido'];

                    $sql = "INSERT INTO chat_rooms(name, creation, description, public) VALUES ('$name', now(3), '$description', $public)";
                    $result = runQuery($sql);
                    $id = $conn->lastInsertId();

                    $sql = "INSERT INTO chat_users (room, user, joined, visited, privilege) VALUES ($id, '$user', now(3), now(3), 0)";
                    $result = runQuery($sql);

                    $sql = "INSERT INTO chat_messages (room, time, sender, message, `system`) VALUES ($id, now(3), '$user', '$nick criou a sala $name', 1)";
                    $result = runQuery($sql);

                    $output['status'] = "CREATED";
                    $output['name'] = $name;
                }

            }
        }
        else if ($command == "promote"){
            if ($room == '')
                $output['status'] = "NOROOM";
            else{
                $sql = "SELECT cu.privilege, u.apelido FROM chat_users cu INNER JOIN usuarios u ON u.id = cu.user WHERE cu.user = '$user' AND cu.room = $room";
                $result = runQuery($sql);
                $nrows = $result->rowCount();

                if ($nrows == 0)
                    $output['status'] == "NOUSER";
                else{
                    $row = $result->fetch();
                    $priv = $row['privilege'];
                    $nick = $row['apelido'];
                    if ($priv != 0)
                        $output['status'] = "NOPERMISSION";
                    else{
                        $target = implode(" ", $args);

                        $sql = "SELECT cu.privilege, cu.user FROM chat_users cu INNER JOIN usuarios u ON u.id = cu.user WHERE u.apelido = '$target' AND cu.room = $room";
                        $result = runQuery($sql);
                        $nrows = $result->rowCount();

                        if ($nrows == 0){
                            $output['status'] = "NOTARGET";
                            $output['command'] = "promote";
                        }
                        else{
                            $row = $result->fetch();
                            $tuser = $row['user'];
                            $tpriv = $row['privilege'];

                            if ($tpriv == 1){
                                $sql = "UPDATE chat_users SET privilege = 0 WHERE user = '$tuser' AND room = $room";
                                $result = runQuery($sql);

                                $output['status'] = "PROMOTED";

                                $sql = "INSERT INTO chat_messages (room, time, sender, message, `system`) VALUES ($room, now(3), '$user', '$target foi promovido por $nick', 1)";
                                $result = runQuery($sql);

                            }
                            else
                                $output['status'] = "MAXPROMOTION";
                        }
                        $output['target'] = $target;

                    }
                }
            }
        }
        else if ($command == "ban" || $command == "unban" || $command == "kick"){
            // set room arg if trying to kick from outside 
            if ($command == "kick" && $room == ''){
                if ($command == "kick"){
                    $str = implode(" ", $args);
                    $args = explode(" ", explode(" -r", $str)[0]);
                    preg_match('/ -r ([\wáàâãéêíóõôúç\d\s]+)/', " $str", $d);
                    if (isset($d) && is_array($d) && count($d) > 1){
                        $r = trim($d[1]);
                        $sql = "SELECT id FROM chat_rooms WHERE name = '$r'";
                        $result = runQuery($sql);
                        $nrows = $result->rowCount();
                        if ($nrows > 0){
                            $row = $result->fetch();
                            $room = $row['id'];
                        }
                        else
                            $room = '';
                    }
                    else   
                        $room = '';
                }
            }

            if ($room == ''){
                $output['status'] = "NOROOM";
            }
            else{
                $sql = "SELECT cu.privilege, u.apelido, cr.name FROM chat_users cu INNER JOIN usuarios u ON u.id = cu.user INNER JOIN chat_rooms cr ON cr.id = cu.room WHERE cu.user = '$user' AND cu.room = $room";
                $result = runQuery($sql);
                $nrows = $result->rowCount();

                if ($nrows == 0)
                    $output['status'] == "NOUSER";
                else{
                    $row = $result->fetch();
                    $priv = $row['privilege'];
                    $nick = $row['apelido'];
                    $room_name = $row['name'];
                    if ($priv == 1)
                        $output['status'] = "NOPERMISSION";
                    else{
                        $target = implode(" ", $args);

                        if ($command == "ban" || $command == "kick")
                            $sql = "SELECT cu.privilege, cu.user FROM chat_users cu INNER JOIN usuarios u ON u.id = cu.user WHERE u.apelido = '$target' AND cu.room = $room";
                        elseif ($command == "unban")
                            $sql = "SELECT cre.user, 2 AS privilege FROM usuarios u INNER JOIN chat_restrictions cre ON cre.user = u.id WHERE u.apelido = '$target' AND cre.room = $room";
                        
                        $result = runQuery($sql);
                        $nrows = $result->rowCount();
                        
                        if ($nrows == 0){
                            $output['status'] = "NOTARGET";
                            $output['command'] = $command;
                        }
                        else{
                            $row = $result->fetch();
                            $tuser = $row['user'];
                            $tpriv = $row['privilege'];

                            //check if user is already banned
                            $sql = "SELECT id FROM chat_restrictions WHERE user = '$tuser' AND room = $room AND ban = 1";
                            $result = runQuery($sql);
                            $banned = $result->rowCount();
                            if ($banned > 0 && $command == 'ban'){
                                $output['status'] = "ALREADYBANNED";
                                $output['target'] = $target;
                            }
                            else if ($tpriv > $priv){
                                if ($command == 'ban'){
                                    $msg = "$target foi banido da sala por $nick";
                                    $sql = "INSERT INTO chat_restrictions (user, room, ban, time) VALUES ('$tuser', $room, 1, now(3))";
                                    $output['status'] = "BAN";
                                }
                                elseif ($command == 'kick'){
                                    $msg = "$target foi removido da sala por $nick";
                                    $sql = "DELETE FROM chat_users WHERE user = '$tuser' AND room = $room";
                                    $output['status'] = "KICK";
                                }
                                else{
                                    $msg = "$nick removeu o banimento de $target";
                                    $sql = "DELETE FROM chat_restrictions WHERE user = '$tuser' AND room = $room";
                                    $output['status'] = "UNBAN";
                                }

                                send_node_message(array(
                                    'chat personal' => array(
                                        'user' => $tuser,
                                        'status' => $output['status'],
                                        'room_name' => $room_name 
                                    )
                                ));

                                $result = runQuery($sql);

                                $sql = "INSERT INTO chat_messages (room, time, sender, message, `system`) VALUES ($room, now(3), '$user', '$msg', 1)";
                                $result = runQuery($sql);

                            }
                            else
                                $output['status'] = "NOPERMISSION";
                        }
                        $output['target'] = $target;

                    }
                }
            }
        }
        else if ($command == "claim"){
            if ($room == '')
                $output['status'] = "NOROOM";
            else{
                $sql = "SELECT cu.user, u.ativo FROM chat_users cu INNER JOIN usuarios u ON u.id = cu.user WHERE cu.privilege = 0 AND cu.room = $room AND u.ativo >= (CURRENT_TIME() - INTERVAL 30 DAY) ORDER BY u.ativo DESC";
                $result = runQuery($sql);
                $nrows = $result->rowCount();
    
                if ($nrows == 0){
                    $sql = "SELECT u.apelido FROM usuarios u WHERE u.id = '$user' AND u.id NOT IN (SELECT user FROM chat_restrictions WHERE user = '$user' AND room = $room)";
                    $result = runQuery($sql);
                    $nrows = $result->rowCount();

                    if ($nrows == 0)
                        $output['status'] = "RESTRICTED";
                    else{
                        $row = $result->fetch();
                        $nick = $row['apelido'];
    
                        $sql = "UPDATE chat_users SET privilege = 1 WHERE privilege = 0 AND room = $room";
                        $result = runQuery($sql);
    
                        $sql = "UPDATE chat_users SET privilege = 0 WHERE user = '$user'";
                        $result = runQuery($sql);
    
                        $sql = "INSERT INTO chat_messages (room, time, sender, message, `system`) VALUES ($room, now(3), '$user', '$nick se autoproclamou o novo líder da sala', 1)";
                        $result = runQuery($sql);
    
                        $output['status'] = "CLAIMED";
                    }
                }
                else{
                    $output['user'] = array();
                    while ($row = $result->fetch()){
                        array_push($output['user'], $row);
                    }
        
                    $output['status'] = "ACTIVE";
                }
            }
        }
        else if ($command == "edit"){            
            if ($room == ''){
                $str = implode(" ", $args);
                preg_match('/ -r ([\wáàâãéêíóõôúç\d\s]+)/', " $str", $r);
                if (isset($r) && is_array($r) && count($r) > 1){
                    $r = trim($r[1]);
                    $sql = "SELECT id FROM chat_rooms WHERE name = '$r'";
                    $result = runQuery($sql);
                    $nrows = $result->rowCount();
                    if ($nrows > 0){
                        $row = $result->fetch();
                        $room = $row['id'];
                    }
                    else
                        $room = '';
                }
                else   
                    $room = '';
            }

            if ($room == '')
                $output['status'] = "NOROOM";
            else{
                $sql = "SELECT cu.privilege, u.apelido FROM chat_users cu INNER JOIN usuarios u ON u.id = cu.user WHERE cu.user = '$user' AND cu.room = $room";
                $result = runQuery($sql);
                $nrows = $result->rowCount();

                if ($nrows == 0)
                    $output['status'] == "NOUSER";
                else{
                    $row = $result->fetch();
                    $priv = $row['privilege'];
                    $nick = $row['apelido'];

                    if ($priv == 1)
                        $output['status'] = "NOPERMISSION";
                    else{
                        $str = implode(" ", $args);

                        $public = '';
                        if (strpos($str, '-pvt') !== false){
                            $public = 0;
                            $str = trim(implode(" ", explode("-pvt", $str)));
                        }
                        else if (strpos($str, '-pub') !== false){
                            $public = 1;
                            $str = trim(implode(" ", explode("-pub", $str)));
                        }
                        
                        preg_match('/ -d ([\wáàâãéêíóõôúç\d\s]+)/', " $str", $d);
                        if (isset($d) && is_array($d) && count($d) > 1)
                            $d = trim($d[1]);
                        else   
                            $d = '';

                        preg_match('/ -n ([\wáàâãéêíóõôúç\d\s]+)/', " $str", $n);
                        if (isset($n) && is_array($n) && count($n) > 1)
                            $n = trim($n[1]);
                        else   
                            $n = '';

                        $fields = array();
                        $messages = array();
                        if ($n != ''){
                            array_push($fields, "name = '$n'");
                            array_push($messages, "$nick alterou o nome da sala para $n");
                        }
                        if ($d != ''){
                            array_push($fields, "description = '$d'");
                            array_push($messages, "$nick alterou a descrição da sala para: \"$d\"");
                        }
                        $output['messages'] = $public;

                        if ($public !== ''){
                            array_push($fields, "public = $public");
                            if ($public == 1)
                                array_push($messages, "$nick tornou a sala pública");
                            else
                                array_push($messages, "$nick tornou a sala privada");
                        }
                        
                        if (count($fields) == 0)
                            $output['status'] = "BLANK";
                        else{
                            $fields = implode(", ", $fields);

                            $sql = "UPDATE chat_rooms SET $fields WHERE id = $room";
                            $result = runQuery($sql);

                            foreach ($messages as $message){
                                $sql = "INSERT INTO chat_messages (room, time, sender, message, `system`) VALUES ($room, now(3), '$user', '$message', 1)";
                                $result = runQuery($sql);
                            }

                            $output['status'] = "EDITED";
                        }
                    }
                }
            }

        }
        else if ($command == "help"){

        }
        else{
            $output['status'] = "UNKNOWN";
        }

        return $output;
    }
?>