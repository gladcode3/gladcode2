<?php
    $version = file_get_contents("version");
    include_once "connection.php";
    session_start();
    
    if(isset($_SESSION['user'])) {
        if ($_POST['action'] == "MATCH"){
            $user = $_SESSION['user'];
            $output = array();

            $id = $_POST['id'];
            $pool = 10;
            
            $sql = "SELECT version FROM gladiators WHERE cod = $id";
            $result = runQuery($sql);
            $row = $result->fetch();

            if ($row['version'] == $version){
                $mymmr = "SELECT mmr FROM gladiators WHERE cod = '$id' AND master = '$user'";
                $mypos = "SELECT count(*) FROM gladiators WHERE mmr < ($mymmr)";
                $pos = "SELECT count(*) FROM gladiators g2 WHERE g2.mmr < g.mmr";
                $gladsclose = "SELECT g.cod FROM gladiators g WHERE g.master != '$user' AND g.version = '$version' ORDER BY ABS(($pos) - ($mypos)) LIMIT $pool";
                $sql = "SELECT * FROM gladiators g INNER JOIN usuarios u ON g.master = u.id INNER JOIN ($gladsclose) s ON g.cod = s.cod ORDER BY rand() LIMIT 4";
                $result = runQuery($sql);

                $i = 0;
                $ids = array();
                while($row = $result->fetch()){
                    $output[$i] = array();
                    $output[$i]['name'] = $row['name']; 
                    $output[$i]['user'] = $row['apelido']; 
                    $output[$i]['skin'] = $row['skin']; 
                    $output[$i]['vstr'] = $row['vstr']; 
                    $output[$i]['vagi'] = $row['vagi']; 
                    $output[$i]['vint'] = $row['vint']; 
                    $output[$i]['mmr'] = $row['mmr']; 
                    array_push($ids, $row['cod']);
                    $i++;
                }		
                array_push($ids, $id);
                
                $_SESSION['match'] = $ids;
            }
            else{
                $output['status'] = "OLD";
            }

            echo json_encode($output);
        }
        
    }
?>