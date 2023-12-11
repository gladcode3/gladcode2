<?php
    session_start();
    $user = $_SESSION['user'];
    $action = $_POST['action'];
    $output = array();

    $default = array(
        'show_frames' => true,
        'show_bars' => true,
        'show_fps' => false,
        'show_text' => true,
        'show_speech' => true,
        'sfx_volume' => 1,
        'music_volume' => 0.1,
        'crowd' => 1
    );

    if ($action == "GET_PREF"){

        foreach ($default as $name => $value){
            $output[$name] = $value;
        }
        if (isset($_COOKIE['render_prefs'])){
            foreach (json_decode($_COOKIE['render_prefs'], true) as $name => $value){
                $output[$name] = $value;
            }
        }
    }
    elseif ($action == "SET_PREF"){
        $cookie = $default;
        if (isset($_COOKIE['render_prefs']))
            $cookie = json_decode($_COOKIE['render_prefs'], true);

        foreach ($_POST as $cki => $ckv){
            if ($cki != "action"){
                $cookie[$cki] = $ckv;
            }
        }
        
        setcookie('render_prefs', json_encode($cookie), time() + (86400 * 30 * 12), "/"); // 86400 = 1 day
        
        $output['render_pref'] = $cookie;
    }

    echo json_encode($output);
?>