<?php
    include_once "connection.php";
    session_start();
    $output = array();

    if ($_POST['action'] == "GET"){
        $loghash = $_POST['loghash'];
        $sql = "SELECT * FROM logs WHERE hash = '$loghash'";
        $result = runQuery($sql);
        $nrows = $result->rowCount();

        if ($nrows > 0){
            $row = $result->fetch();

            if ($row['expired'] == 1){
                $output['status'] = "EXPIRED";
            }
            else {
                $id = $row['id'];

                $log = file_get_contents("logs/$id");

                if (strlen($log) > 0){
                    $output['log'] = $log;
                    $output['status'] = "SUCCESS";
                    header('Content-Length: ' . strlen(json_encode($output)));
                }
                else{
                    $output['status'] = "ERROR";
                }
            }
        }
        else{
            $output['status'] = "NOTFOUND";
        }
    }
    elseif ($_POST['action'] == "DELETE"){
        $hash = $_POST['hash'];
        $sql = "SELECT id FROM logs WHERE hash = '$hash'";
        $result = runQuery($sql);
        $row = $result->fetch();
        $id = $row['id'];
        unlink("logs/$id");

        $sql = "DELETE FROM logs WHERE hash = '$hash'";
        $result = runQuery($sql);

        $output['status'] = "SUCCESS";
    }

    echo json_encode($output);
?>