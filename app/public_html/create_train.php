<?php
    include_once "connection.php";

    if (isset($_GET['h'])){
        $hash = $_GET['h'];
        $nplayers = $_GET['p'];

        $sql = "SELECT id FROM training WHERE hash = '$hash'";
        $result = runQuery($sql);
        $row = $result->fetch();
        $trainid = $row['id'];
        
        $sql = "SELECT g.cod FROM gladiators g GROUP BY g.master";
        $result = runQuery($sql);

        $glads = array();
        while ($row = $result->fetch()){
            array_push($glads, $row['cod']);
        }
        shuffle($glads);

        $sqlvalue = array();
        for ($i=0 ; $i<$nplayers ; $i++){
            $glad = array_pop($glads);
            array_push($sqlvalue, "($glad, $trainid)");
        }
        $sqlvalue = implode(", ", $sqlvalue);

        $sql = "INSERT INTO gladiator_training (gladiator, training) VALUES $sqlvalue";
        $result = runQuery($sql);
    }
    if (isset($_GET['d'])){
        $hash = $_GET['d'];

        $sql = "SELECT id FROM training WHERE hash = '$hash'";
        $result = runQuery($sql);
        $row = $result->fetch();
        $trainid = $row['id'];

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
    }
    echo "DONE";

?>