<?php
    include_once "connection.php";
    session_start();
    include("back_node_message.php");
    date_default_timezone_set('America/Sao_Paulo');

    $user = null;
    if (isset($_SESSION['user']))
        $user = $_SESSION['user'];

    $output = array();
    $outtext = ""; 
    $error = "";
    $cancel_run = false;
    
    $foldername = md5('folder'.microtime(true)*rand());
    $path = "/home/gladcode";

    system("mkdir $path/temp/$foldername && cp $path/Payload/* $path/temp/$foldername");

    $ids = array();
    $codes = array();
    $skins = array();

    $args = json_decode($_POST['args'], true);

    $glads = array();
    if (isset($args['glads']))
        $glads = $args["glads"];
    if (isset($_SESSION['match'])){
        $glads = array_merge($glads, $_SESSION["match"]);
        unset($_SESSION['match']);
    }

    $userglad = "";
    if (isset($args['duel'])){
        $id = $args['duel'];
        $sql = "SELECT gladiator1 FROM duels WHERE id = '$id' AND user2 = '$user' AND log IS NULL";
        $result = runQuery($sql);
        $row = $result->fetch();
        $userglad = $glads;
        $glads = array($glads, $row['gladiator1']);

        $version = file_get_contents("version");
        $glads_string = implode(",", $glads);
        $sql = "SELECT version FROM gladiators WHERE cod IN ($glads_string)";
        $result = runQuery($sql);
        while ($row = $result->fetch()){
            if ($version != $row['version'])
                $cancel_run = true;
        }

    }

    if (isset($args['tournament'])){
        if (isset($_SESSION['tourn-group'])){
            $groupid = $args['tournament'];
            if (isset($_SESSION['tourn-group'][$groupid]) && $_SESSION['tourn-group'][$groupid] != md5("tourn-group-$groupid-id")){
                $groupid = null;
                $cancel_run = true;
            }
            unset($_SESSION['tourn-group'][$groupid]);

            $sql = "SELECT log FROM `groups` WHERE id = $groupid";
            $result = runQuery($sql);
            $row = $result->fetch();
            if ($row['log'] == null){
                $sql = "SELECT grt.gladiator FROM group_teams grt WHERE grt.groupid = '$groupid'";
                $result = runQuery($sql);
    
                while($row = $result->fetch()){
                    array_push($glads, $row['gladiator']);
                }
            }
            else
                $groupid = null;
        }
        else{
            $groupid = null;
            $cancel_run = true;
        }
    }

    if (isset($args['training'])){
        if (isset($_SESSION['train-run'])){
            $groupid = $args['training'];
            if (!isset($_SESSION['train-run']) || $_SESSION['train-run']['id'] != md5("train-$groupid-id")){
                $groupid = null;
                $cancel_run = true;
            }
            unset($_SESSION['train-run']);

            $sql = "SELECT log FROM training_groups WHERE id = $groupid";
            $result = runQuery($sql);
            $row = $result->fetch();
            if (is_null($row['log'])){
                $sql = "SELECT gladiator FROM gladiator_training WHERE groupid = '$groupid'";
                $result = runQuery($sql);
    
                while($row = $result->fetch()){
                    array_push($glads, $row['gladiator']);
                }
            }
            else
                $groupid = null;
        }
        else{
            $groupid = null;
            $cancel_run = true;
        }
    }

    foreach ($glads as $glad){
        if (ctype_digit($glad) || is_int($glad)){
            array_push($ids, $glad);
        }
        else{
            $code = htmlspecialchars_decode($glad);

            $nick = getUser($code);
            if ($nick === false)
                $nick = "user". count($codes);

            $hash = getSkin($code);
            $code = preg_replace('/setSpritesheet\("[\d\w]*?"\)[;]{0,1}/', "", $code);
            $code = preg_replace('/setSkin\("[\W\w]*?"\)[;]{0,1}/', "", $code);
            $code = preg_replace('/setUser\("[\W\w]*?"\)[;]{0,1}/', "", $code);

            preg_match('/setName\("([\W\w]*?)"\)/', $code, $name);
            $name = $name[1];

            if (strlen($hash) != 32){
                $skins[$name .'@'. $nick] = $hash;
            }
            else{
                $sql = "SELECT skin FROM skins WHERE hash = '$hash'";
                $result = runQuery($sql);
                if ($result->rowCount() > 0){
                    $row = $result->fetch();
                    $skins[$name .'@'. $nick] = $row['skin'];
                }
            }	

            $pattern = '/setName\("([\w À-ú]+?)"\)/';
            $replacement = 'setName("$1@'. $nick .'")';
            $code = preg_replace($pattern, $replacement, $code);
            array_push($codes, $code);
        }
    }

    if (isset($args['breakpoints']) && $args['breakpoints'] !== false && isset($args['single'])){
        $code = $codes[count($codes) - 1];
        $oldcode = $code;
        $breakpoints = $args['breakpoints'];
        $language = getLanguage($code);

        if ($language == 'c'){
            // add {} in single line blocks to avoid messing with structure if breakpoint is inserted in between
            $pattern = '/((?:(?:if)||(?:for)|(?:while))[ ]{0,1}\([^\n]*?\))\n(.*)|(else)\n(.*)/';
            $replacement = '$1$3{'. PHP_EOL .'$2$4}';
            $code = preg_replace($pattern, $replacement, $code);

            // find where setup ends
            $code = explode(PHP_EOL, $code);
            foreach ($code as $i => $line){
                if ($line == "}"){
                    $setup = $i + 2;
                    break;
                }
            }

            //insert breakpoint line
            foreach ($breakpoints as $ln){
                $line = $ln - 1 + $setup;
                $hint = preg_replace('/\s*(.*?)[{};]*\n/', "$1", $code[$line]);
                $code[$line] = "breakpoint(\"". $hint ."\");". PHP_EOL . $code[$line];
                $code = preg_replace($pattern, $replacement, $code);
                    
            }
        }
        else if ($language == 'python'){
            // find where setup ends
            $code = explode(PHP_EOL, $code);
            foreach ($code as $i => $line){
                if ($line == "# start of user code"){
                    $setup = $i + 2;
                    break;
                }
            }

            //insert breakpoint line
            foreach ($breakpoints as $ln){
                $line = $ln - 2 + $setup;
                $hint = preg_replace('/(\s*)(.*)/', "$2", $code[$line]);
                $tab = preg_replace('/(\s*)(.*)/', "$1", $code[$line]);
                $code[$line] = $tab ."breakpoint(\"". $hint ."\")". PHP_EOL . $code[$line];
                    
            }
        }

        $code = implode(PHP_EOL, $code);
        $codes[count($codes) - 1] = $code;
    }


    if (count($ids) > 0){
        $ids = implode(",", $ids);
        $sql = "SELECT u.id AS 'userid', code, apelido, vstr, vagi, vint, g.name, skin FROM gladiators g INNER JOIN usuarios u ON g.master = u.id WHERE g.cod IN ($ids)";
        $result = runQuery($sql);

        while($row = $result->fetch()){
            $code = $row['code'];
            $nick = $row['apelido'];
            $name = $row['name'];
            $vstr = $row['vstr'];
            $vagi = $row['vagi'];
            $vint = $row['vint'];
            $skins[$name .'@'. $nick] = $row['skin'];
            $uid = $row['userid'];

            $potions = "";
            $sql = "SELECT item FROM slots WHERE user = $uid AND expire > now() ORDER BY expire LIMIT 4";
            $result2 = runQuery($sql);
            $potions = array();
            for ($i=0 ; $i<4 ; $i++){
                if ($row = $result2->fetch()){
                    array_push($potions, $row['item']);
                }
                else {
                    array_push($potions, "0");
                }
            }
            $potions = implode(",", $potions);

            $language = getLanguage($code);
            if ($language == "c"){
                $setup = "setup(){\n    setName(\"$name@$nick\");\n    setSTR($vstr);\n    setAGI($vagi);\n    setINT($vint);\n    setSlots(\"$potions\");\n}\n\n";
            }
            else if ($language == "python"){
                $setup = "def setup():\n    setName(\"$name@$nick\")\n    setSTR($vstr)\n    setAGI($vagi)\n    setINT($vint)\n    setSlots(\"$potions\")\n# start of user code\n";
            }

            $code = $setup . $code;

            array_push($codes, $code);
        }
    }

    $invalid_attr = false;
    foreach($codes as $i => $code){
        if (!validate_attr($code)){
            $invalid_attr = true;
        }

        $language = getLanguage($code);
        if ($language == "c"){
            $code = "#include \"gladCodeCore.c\"\n". $code;
            file_put_contents("$path/temp/$foldername/code$i.c",$code);
        }
        else if ($language == "python"){
            $code = "from gladCodeAPI import *\n\n". $code ."\n\ninitClient()\nsetup()\nif startSim():\n    while running():\n        loop()\n";
            file_put_contents("$path/temp/$foldername/code$i.py",$code);
        }
    }

    if ($cancel_run)
        $output['simulation'] = null;
    elseif (!$invalid_attr){		
        system("$path/script/call_socket.sh $foldername &>> $path/temp/$foldername/error.txt");
        
        if (file_exists("$path/temp/$foldername/outputc.txt"))
            $outtext .= file_get_contents ("$path/temp/$foldername/outputc.txt");
        if (file_exists("$path/temp/$foldername/outputs.txt"))
            $outtext .= file_get_contents ("$path/temp/$foldername/outputs.txt");
        if (file_exists("$path/temp/$foldername/error.txt"))
            $error .= file_get_contents ("$path/temp/$foldername/error.txt");
        if (file_exists("$path/temp/$foldername/errors.txt"))
            $error .= file_get_contents ("$path/temp/$foldername/errors.txt");
        if (file_exists("$path/temp/$foldername/errorc.txt"))
            $error .= file_get_contents ("$path/temp/$foldername/errorc.txt");

        $spechar = array("\n", "\r", "\t", "\"");
        $repchar = array("\\n", "\\r", "\\t", '\\"');
        
        if ($error != ""){
            $error = str_replace($spechar, $repchar, $error);
        }
        if ($outtext != ""){
            $outtext = str_replace($spechar, $repchar, $outtext);
        }
        
        //stream the file contents
        if ($error == "" && file_exists("$path/temp/$foldername/simlog")){
            if (isset($args['savecode']) && $args['savecode'] === true){
                if (isset($args['breakpoints']) && $args['breakpoints'] !== false && isset($args['single'])){
                    $codes[count($codes)-1] = $oldcode;
                }

                // remove banned functions
                $banned = json_decode(file_get_contents("banned_functions.json"), true)['functions'];

                $code = $codes[count($codes) - 1];
                foreach($banned as $function){
                    $pattern = '/'. $function .'.*/';
                    $code = preg_replace($pattern, "", $code);
                }
                $codes[count($codes) - 1] = $code;

                // save code on session
                $language = getLanguage($code);
                if ($language == "c"){
                    $_SESSION['code'] = preg_replace('/setup\(\)[\w\W]*?{[\w\W]*?}\n\n/', "", $codes[count($codes)-1]);
                }
                else if ($language == "python"){
                    $_SESSION['code'] = preg_replace('/def setup\(\)[\w\W]*?:[\w\W]*?\n# start of user code\n/', "", $codes[count($codes)-1]);
                }
                //echo $_SESSION['code'];
            }

            $file = "[". file_get_contents("$path/temp/$foldername/simlog") ."]";

            $simulation = json_decode($file);
            foreach ($simulation[0]->{'glads'} as $gkey => $glad){
                $nick = preg_replace('/#/', " ", $glad->{'user'});
                $name = preg_replace('/#/', " ", $glad->{'name'});
                foreach($skins as $key => $skin){
                    $key = explode("@", $key);
                    if ($name == $key[0] && $nick == $key[1]){
                        $simulation[0]->{'glads'}[$gkey]->{'skin'} = $skin;
                        //cannot uncomment this because C crashes
                        //$simulation[0]->{'glads'}[$gkey]->{'user'} = $user;
                        //$simulation[0]->{'glads'}[$gkey]->{'name'} = $name;
                    }
                }
            }
            //$output['test'] = json_encode($simulation);

            $file = json_encode($simulation);
            
            $hash = save_log($conn, $file, $args['origin']);

            if (isset($args['ranked'])){
                $deaths = death_times($conn, $ids, $file);
                $rewards = battle_rewards($conn, $deaths, $user);
                send_reports($rewards, $hash);
            }
            if (isset($args['duel']) && !$cancel_run){
                $id = $args['duel'];
                $sql = "UPDATE duels SET log = '$hash', gladiator2 = '$userglad', time = now() WHERE id = '$id' AND user2 = '$user'";
                $result = runQuery($sql);

                $sql = "SELECT user1, user2 FROM duels WHERE id = $id";
                $result = runQuery($sql);
                $row = $result->fetch();

                send_node_message(array( 'profile notification' => array(
                    'user' => array($row['user1'], $row['user2'])
                )));
            }
            if (isset($args['tournament']) && $groupid != null){
                $sql = "SELECT l.id FROM logs l WHERE l.hash = '$hash'";
                $result = runQuery($sql);
                $row = $result->fetch();
                $logid = $row['id'];

                $sql = "SELECT log FROM `groups` WHERE id = $groupid";
                $result = runQuery($sql);
                $row = $result->fetch();
                if ($row['log'] == null){
                    $sql = "UPDATE `groups` SET log = '$logid' WHERE id = '$groupid'";
                    $result = runQuery($sql);

                    $sql = "SELECT hash FROM tournament WHERE id = (SELECT tournament FROM teams WHERE id = (SELECT team FROM group_teams WHERE groupid = $groupid LIMIT 1))";
                    $result = runQuery($sql);
                    $row = $result->fetch();

                    send_node_message(['tournament refresh' => [ 'hash' => $row["hash"] ]]);
                }
            }
			if (isset($args['training']) && $groupid != null){
				$sql = "SELECT id FROM logs WHERE hash = '$hash'";
				$result = runQuery($sql);
				$row = $result->fetch();
                $logid = $row['id'];

				$sql = "SELECT tg.log, t.hash FROM training_groups tg INNER JOIN gladiator_training gt ON gt.groupid = tg.id INNER JOIN training t ON t.id = gt.training WHERE tg.id = $groupid";
				$result = runQuery($sql);
				$row = $result->fetch();
				if (is_null($row['log'])){
					$sql = "UPDATE training_groups SET log = '$logid' WHERE id = '$groupid'";
                    $result = runQuery($sql);
                    
                    send_node_message(array('training refresh' => array(
                        'hash' => $row['hash']
                    )));
				}
			}

            $output['simulation'] = $hash;
            //echo "{\"error\":\"$error\",\"output\":\"$output\",\"simulation\":\"$hash\"}";
        }
        $output['error'] = $error;
        $output['output'] = $outtext;
            //echo "{\"error\":\"$error\",\"output\":\"$output\",\"simulation\":\"\"}";
        
    }
    else{
        //echo "{\"error\":\"INVALID_ATTR\",\"output\":\"\",\"simulation\":\"\"}";
        $output['error'] = "INVALID_ATTR";
    }

    echo json_encode($output);

    system("rm -rf $path/temp/$foldername");
    
    function getSkin($subject) {
        $pattern = '/setSpritesheet\("([\d\w]*?)"\)[;]{0,1}/';
        $hash = codeMatch($subject, $pattern);

        if ($hash === false){
            $pattern = '/setSkin\("([\W\w]*?)"\)[;]{0,1}/';
            $hash = codeMatch($subject, $pattern);
        }

        return $hash;
    }

    function getUser($subject) {
        $pattern = '/setUser\("([\W\w]*?)"\)[;]{0,1}/';
        return codeMatch($subject, $pattern);
    }

    function getSTR($subject) {
        $pattern = '/setSTR\(([\d]{1,2})\)[;]{0,1}/';
        return codeMatch($subject, $pattern);
    }
    
    function getAGI($subject) {
        $pattern = '/setAGI\(([\d]{1,2})\)[;]{0,1}/';
        return codeMatch($subject, $pattern);
    }

    function getINT($subject) {
        $pattern = '/setINT\(([\d]{1,2})\)[;]{0,1}/';
        return codeMatch($subject, $pattern);
    }

    function codeMatch($subject, $pattern){
        preg_match ( $pattern , $subject , $matches );
        if (count($matches) < 2 || $matches[1] == 'undefined')
            return false;
        else
            return $matches[1];
    }

    function calcAttrValue($attr){
        if ($attr == 0)
            return 0;
        return calcAttrValue($attr - 1) + ceil($attr/6);
    }

    function validate_attr($code){
        $vstr = getSTR($code);
        $vagi = getAGI($code);
        $vint = getINT($code);
        $soma = calcAttrValue($vstr) + calcAttrValue($vagi) + calcAttrValue($vint);
        if ($soma == 50)
            return true;
        else {
            return false;
        }
    }

    function save_log($conn, $log, $origin){
        $version = file_get_contents("version");
        $hash = substr(md5('log'.microtime(true)*rand()), 0,16);

        $sql = "INSERT INTO logs (time, version, hash, origin) VALUES (now(), '$version', '$hash', '$origin')";
        $result = runQuery($sql);
        
        $id = $conn->lastInsertId();
        file_put_contents("logs/$id",$log);
        return $hash;
    }

    function battle_rewards($conn, $deaths, $user){
        $ids = array();
        $times = array();
        foreach($deaths as $glad){
            array_push($ids,$glad['id']);
            array_push($times,$glad['time']);
        }

        $ids = implode(",", $ids);
        $sql = "SELECT g.cod, g.mmr, g.master, u.lvl, u.xp FROM gladiators g INNER JOIN usuarios u ON u.id = g.master WHERE cod IN ($ids) ORDER BY FIELD(cod,$ids)";
        $result = runQuery($sql);

        $glads = array();
        $i = 0;
        while($row = $result->fetch()){
            $glads[$i] = array();
            $glads[$i]['id'] = $row['cod'];
            $glads[$i]['time'] = $times[$i];
            $glads[$i]['mmr'] = $row['mmr'];
            $glads[$i]['master'] = $row['master'];
            $glads[$i]['masterlvl'] = $row['lvl'];
            $glads[$i]['masterxp'] = $row['xp'];
            $i++;
        }

        $nglads = count($glads);
        $rewards = updateMMR($glads, $conn);
        
        $glad_rewards = array();
        foreach($glads as $i => $glad)
            $glad_rewards[$glad['id']] = $rewards[$i];

        foreach($glads as $i => $glad){
            if ($glad['master'] == $user){
                $thisglad = $i;
                break;
            }
        }

        $glad = $glads[$thisglad];
        $lvl = $glad['masterlvl'];
        $xp = $glad['masterxp'];
        
        /*
            $income = 1000 + 30% de bonus em cima do mmr = dinheiro que a arena arrecadou
            $silver = income * 
                0.7 -> 30% lucro do governo
                300 -> manutencao da arena
                /10 -> /2 porque metade é apostas e metade é ingressos, /5 porque os ingressos vao pra cada gladiador
                -20 -> custo de manutencao do gladiador
            na vitória -> ganha a parte das apostas

            400g é o income básico
            200 por apostas e 200 por ingressos
            20g numa derrota (ingressos divididos por 5 - manutenção)
            em 5 lutas com 1 vitória, média de 60g
            agenciadores te colocam em 20 lutas (1200g média) por dia
            depois, tu luta em arenas não oficiais por 1/10 disso ilimitado
        */

        $income = 1000 + ($glad['mmr'] - 1000) * 0.3;
        $silver = max(0, (0.7 * $income - 300)/10 - 20);
        $xplose = 100;
        $xpwin = 150;
        $xp += $xplose;
        // winner
        if ($thisglad == 0){
            $xp += $xpwin;
            $silver += (0.7 * $income - 300)/2;
        }

        // get how many battles today
        $sql = "SELECT l.id FROM reports r INNER JOIN gladiators g ON r.gladiator = g.cod INNER JOIN logs l ON l.id = r.log WHERE g.master = $user AND l.time > now() - INTERVAL 1 DAY AND r.started = '1'";
        $result = runQuery($sql);
        // cut silver if not managed battle        
        $silver = $result->rowCount() < 20 ? $silver : $silver / 10;
        
        /*
        y = ax+b;
        a = 2; b = 0 = numeros de lutas que precisa ganhar
        130 = xp médio por luta
        */
        $a = 1.9;
        $b = 1;
        $avgxp = ($xplose * $nglads + $xpwin) / $nglads;
        $tonext = ($a * $lvl + $b) * $avgxp;

        if ($lvl == 60)
            $xp = 0;
        elseif ($xp >= $tonext && $lvl < 60){
            $lvl++;
            $xp -= $tonext;
        }
        
        $sql = "UPDATE usuarios SET lvl = '$lvl', xp = '$xp', silver = silver + $silver WHERE id = '$user'";
        $result = runQuery($sql);

        send_node_message(array(
            'profile notification' => array('user' => array($user))
        ));

        return $glad_rewards;
    }

    function updateMMR($glads, $conn){
        $reward = calcReward($glads);
                
        foreach($glads as $i => $glad){
            $mmr = $reward[$i];
            $id = $glad['id'];
            
            $sql = "UPDATE gladiators SET mmr = mmr + '$mmr' WHERE cod = '$id'";
            $result = runQuery($sql);
        }		
        
        return $reward;
    }
    
    function calcReward($glads){
        $maxReward = 10;
        $timeWeight = 1.5;
        $winWeight = 1;
        $nglad = count($glads);
        
        $rewardRaw = array();
        $timeDiff = $glads[1]['time'] - $glads[$nglad-1]['time'];
        
        $avgmmr = 0;
        foreach($glads as $i => $glad){
            $diff = $glad['time'] - $glads[$nglad-1]['time'];
            if ($timeDiff == 0)
                $timeNorm = 1;
            else
                $timeNorm = $diff/$timeDiff;

            $win = 0;
            if ($i == 0 || $glad['time'] == $glads[0]['time']){
                $win = 1;
                $timeNorm = 1;
            }
            $rewardRaw[$i] = ($timeNorm * $nglad * $timeWeight) + ($win * $nglad * $winWeight);
            $avgmmr += $glad['mmr'];
        }
        $avgmmr /= $nglad;
        $evenReward = array_sum($rewardRaw) / $nglad;
        $rewardWeighted = array();
        $rewardDev = array();
        
        foreach($glads as $i => $glad){
            $rewardWeighted[$i] = $rewardRaw[$i] - $evenReward;
            $rewardNorm = $rewardWeighted[$i] / $rewardWeighted[0] * $maxReward;

            $mmrDev = $glad['mmr'] / $avgmmr;
            
            if ($rewardNorm > 0)
                $rewardDev[$i] = $rewardNorm / max($mmrDev, 0.5);
            else
                $rewardDev[$i] = $rewardNorm * min($mmrDev, 2);
            
            //faz perder menos quanto mais próximo de mmr 0
            if ($rewardDev[$i] < 0 && $glad['mmr'] < 1000){ 
                $bias = 0.001 * $glad['mmr'] - 1;
                $rewardDev[$i] = $rewardDev[$i] + $rewardDev[$i] * $bias;
            }
        }		
        
        return $rewardDev;
    }	

    function death_times($conn, $ids, $log){
        $log = json_decode($log, true);
        $deaths = array();

        foreach($log[0]['glads'] as $glad){
            $member = array(
                'name' => preg_replace('/#/', " ", $glad['name']),
                'user' => preg_replace('/#/', " ", $glad['user'])
            );
            array_push($deaths, $member);
        }

        foreach($log as $i => $step){
            foreach($step['glads'] as $g => $glad){
                if (isset($glad['hp'])){
                    $deaths[$g]['time'] = floatval($step['simtime']);
                    $deaths[$g]['hp'] = floatval($glad['hp']);
                }
            }
        }

        uasort($deaths, "death_sort");

        foreach($deaths as $i => $glad){
            $name = $glad['name'];
            $nick = $glad['user'];
            $sql = "SELECT g.cod FROM gladiators g INNER JOIN usuarios u ON g.master = u.id WHERE g.name = '$name' AND u.apelido = '$nick'";
            $result = runQuery($sql);

            while($row = $result->fetch()){
                $deaths[$i]['id'] = $row['cod'];
            }
        }
        return $deaths;
    }

    function death_sort($a,$b) {
        if ($a['hp'] > 0 && $b['hp'] <= 0)
            return -1;
        if ($b['hp'] > 0 && $a['hp'] <= 0)
            return 1;
        return $b['time'] - $a['time'];
    }

    function send_reports($rewards, $log){
        global $conn;
        global $user;

        $sql = "SELECT id FROM logs WHERE hash = '$log'";
        $result = runQuery($sql);
        $row = $result->fetch();
        $log = $row['id'];
        
        $masters = array();
        foreach($rewards as $glad => $reward){
            $sql = "SELECT master FROM gladiators WHERE cod = $glad";
            $result = runQuery($sql);
            $row = $result->fetch();
            array_push($masters, $row['master']);

            if ($row['master'] == $user){
                $fields = "(log, gladiator, reward, started)";
                $values = "('$log', '$glad', '$reward', '1')";
            }
            else {
                $fields = "(log, gladiator, reward)";
                $values = "('$log', '$glad', '$reward')";
            }

            $sql = "INSERT INTO reports $fields VALUES $values";
            $result = runQuery($sql);

        }

        send_node_message(array(
            'profile notification' => array('user' => $masters)
        ));
    }

    function getLanguage($code){
        $language = "c";
        if (strpos($code, "def loop():") !== false)
            $language = "python";
        
        return $language;
    }
?>