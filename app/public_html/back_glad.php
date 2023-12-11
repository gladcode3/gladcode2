<?php
    $version = file_get_contents("version");
    include_once "connection.php";
    session_start();
    include("back_node_message.php");

    if ($_POST['action'] == "FILE"){
        if (isset($_POST['filename']))
            $code = file_get_contents($_POST['filename']);
        elseif (isset($_POST['code']))
            $code = $_POST['code'];
            
        $hash = getSpriteHash($code);
        
        $sql = "SELECT skin FROM skins WHERE hash = '$hash'";
        $result = runQuery($sql);

        $info = array();

        if ($result->rowCount() > 0){
            $row = $result->fetch();
            $info['skin'] = json_decode($row['skin']);
        }
        else
            $info['skin'] = null;

        $info['name'] = getSpriteName($code);
        $info['vstr'] = getSpriteSTR($code);
        $info['vagi'] = getSpriteAGI($code);
        $info['vint'] = getSpriteINT($code);
        $info['code'] = $code;
        
        echo json_encode($info);
    }
    elseif(isset($_SESSION['user'])) {
        $user = $_SESSION['user'];
        
        $sql = "SELECT * FROM usuarios WHERE id = '$user'";
        $result = runQuery($sql);
        $row = $result->fetch();
        $lvl = $row['lvl'];
        
        $initglads = 1;		
        $gladinterval = 10;
        $maxglads = 6;
        $limit = min($maxglads, $initglads + floor($lvl/$gladinterval));
        $nick = $row['apelido'];
        
        if ($_POST['action'] == "GET"){
            $sql = "SELECT * FROM gladiators WHERE master = '$user'";
            $result = runQuery($sql);

            $i = 0;
            $info = array();
            while($row = $result->fetch()){
                $info[$i] = array();
                $info[$i]['id'] = $row['cod'];
                $info[$i]['name'] = $row['name'];
                $info[$i]['vstr'] = $row['vstr'];
                $info[$i]['vagi'] = $row['vagi'];
                $info[$i]['vint'] = $row['vint'];
                $info[$i]['code'] = htmlspecialchars($row['code']);
                $info[$i]['blocks'] = htmlspecialchars($row['blocks']);
                $info[$i]['skin'] = $row['skin'];
                $info[$i]['mmr'] = $row['mmr'];
                $info[$i]['user'] = $nick;
                if ($row['version'] != $version)
                    $info[$i]['oldversion'] = 'old';
                $i++;
            }
            echo json_encode($info);

            send_node_message(array(
                'profile notification' => array('user' => array($user))
            ));
        }
        elseif ($_POST['action'] == "DELETE"){
            $id = $_POST['id'];
            $sql = "DELETE FROM gladiators WHERE cod = '$id' AND master = '$user'";
            $result = runQuery($sql);

            send_node_message(array(
                'profile notification' => array('user' => array($user))
            ));
        }
        else{
            if (isset($_POST['id']))
                $id = $_POST['id'];
            else
                $id = '';
            $skin = $_POST['skin'];
            $name = $_POST['nome'];
            preg_match ( '/^[\w À-ú]+?$/' , $name , $name_match );
            $vstr = $_POST['vstr'];
            $vagi = $_POST['vagi'];
            $vint = $_POST['vint'];
            $code = $_SESSION['code'];

            $blocks = "";
            if (isset($_POST['blocks']))
                $blocks = $_POST['blocks'];

            if (validate_attr($vstr,$vagi,$vint) && count($name_match) == 1 && isset($_SESSION['code'])){
                $sql = "SELECT cod FROM gladiators WHERE name = '$name' AND cod != '$id'";
                $result = runQuery($sql);
                if ($result->rowCount() == 0){
                    if ($_POST['action'] == "INSERT"){
                        $sql = "SELECT * FROM gladiators WHERE master = '$user'";
                        $result = runQuery($sql);
                        if ($result->rowCount() >= $limit)
                            echo "{\"LIMIT\":$limit}";
                        else{
                            $sql = "INSERT INTO gladiators (master, skin, name, vstr, vagi, vint, lvl, xp, code, blocks, version) VALUES ('$user', '$skin', '$name', '$vstr', '$vagi', '$vint', '1', '0', '$code', '$blocks', '$version')";
                            $result = runQuery($sql);
                            echo "{\"ID\":". $conn->lastInsertId() ."}";

                            send_node_message(array(
                                'profile notification' => array('user' => array($user))
                            ));
                        }
                    }
                    elseif ($_POST['action'] == "UPDATE"){
                        $sql = "UPDATE gladiators SET skin = '$skin', name = '$name', vstr = '$vstr', vagi = '$vagi', vint = '$vint', code = '$code', blocks = '$blocks', version = '$version' WHERE cod = '$id' AND master = '$user'";
                        $result = runQuery($sql);
                        echo "{\"ID\":". $id ."}";
                    }
                }
                else
                    echo "EXISTS";
            }
            else {
                echo "INVALID";
            }
        }		
    }
    else{
        echo "No user logged";
    }
    
    function getSpriteHash($subject) {
        $pattern = '/setSpritesheet\("([\d\w]*)"\);/';
        return codeMatch($subject, $pattern);
    }
    
    function getSpriteName($subject) {
        $pattern = '/setName\("([\d\w ]*)"\);/';
        return codeMatch($subject, $pattern);
    }
    
    function getSpriteSTR($subject) {
        $pattern = '/setSTR\(([\d]{1,2})\);/';
        return codeMatch($subject, $pattern);
    }
    
    function getSpriteAGI($subject) {
        $pattern = '/setAGI\(([\d]{1,2})\);/';
        return codeMatch($subject, $pattern);
    }

    function getSpriteINT($subject) {
        $pattern = '/setINT\(([\d]{1,2})\);/';
        return codeMatch($subject, $pattern);
    }

    function codeMatch($subject, $pattern){
        preg_match ( $pattern , $subject , $matches );
        if (count($matches) < 2)
            return false;
        else
            return $matches[1];
    }

    function validate_attr($vstr,$vagi,$vint){
        $soma = calcAttrValue($vstr) + calcAttrValue($vagi) + calcAttrValue($vint);
        if ($soma == 50)
            return true;
        else {
            return false;
        }
    }

    function calcAttrValue($attr){
        if ($attr == 0)
            return 0;
        return calcAttrValue($attr - 1) + ceil($attr/6);
    }
?>