<?php
    session_start();
    if (isset($_POST['action'])){
        if ($_POST['action'] == "SET"){
            $_SESSION['redirect'] = $_POST['url'];
            echo "SESSION SET: [". $_SESSION['redirect'] ."]";
        }
        elseif ($_POST['action'] == "UNSET"){
            unset($_SESSION['redirect']);
        }
        elseif ($_POST['action'] == "CHECK"){
            if (isset($_SESSION['redirect'])){
                $url = $_SESSION['redirect'];
                echo $url;
            }
            else{
                echo "NOT SET";
            }
        }
    }
    else {
        echo "NOT SET";
    }
?>