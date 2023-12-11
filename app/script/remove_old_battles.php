<?php
    include_once "/home/gladcode/public_html/connection.php";

    $modes = array(
        "editor" => "1 WEEK",
        "ranked" => "1 YEAR"
    );

    foreach($modes as $mode => $period){
        $fav = $mode == "ranked" ? " AND id NOT IN (SELECT DISTINCT log FROM reports WHERE favorite = 1)" : "";
        $sql = "SELECT id FROM logs WHERE origin = '$mode' AND time < now() - INTERVAL $period AND expired = 0 $fav";
        $result = runQuery($sql);
        
        if ($result->rowCount() > 0){
            $ids = array();
            while($row = $result->fetch()){
                $id = $row['id'];
                unlink("/home/gladcode/public_html/logs/$id");
                array_push($ids, $id);
            }		
            $ids = implode(",", $ids);
            $sql = "UPDATE logs SET expired = 1 WHERE id IN ($ids)";
            $result = runQuery($sql);
        }
    }
?>