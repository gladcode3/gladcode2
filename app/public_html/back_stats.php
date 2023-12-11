<?php
    include("connection.php");
    
    if (isset($_POST['action'])){
        if ($_POST['action'] == 'SAVE'){
            $fireball = $_POST['fireball'];
            $teleport = $_POST['teleport'];
            $charge = $_POST['charge'];
            $block = $_POST['block'];
            $assassinate = $_POST['assassinate'];
            $ambush = $_POST['ambush'];
            $melee = $_POST['melee'];
            $ranged = $_POST['ranged'];
            $win = $_POST['win'];
            $avglvl = $_POST['avglvl'];
            $winnerlvl = $_POST['winnerlvl'];
            $duration = $_POST['duration'];
            $highstr = $_POST['highstr'];
            $highagi = $_POST['highagi'];
            $highint = $_POST['highint'];
            $loghash = $_POST['loghash'];
            $potionuse = $_POST['potionuse'];
            $potionwin = $_POST['potionwin'];

            $sql = "SELECT avg(g.mmr) AS mmr FROM gladiators g INNER JOIN reports r ON g.cod = r.gladiator INNER JOIN logs l ON l.id = r.log WHERE l.hash = '$loghash'";
            $result = runQuery($sql);
            $row = $result->fetch();
            $mmr = $row['mmr'];

            $sql = "INSERT INTO stats (time, fireball, teleport, charge, block, assassinate, ambush, melee, ranged, win, avglvl, winnerlvl, duration, highstr, highagi, highint, avgmmr, potionuse, potionwin) VALUES (now(), '$fireball', '$teleport', '$charge', '$block', '$assassinate', '$ambush', '$melee', '$ranged', '$win', '$avglvl', '$winnerlvl', '$duration', '$highstr', '$highagi', '$highint', '$mmr', '$potionuse', '$potionwin')";
            $result = runQuery($sql);
        }
        elseif ($_POST['action'] == 'load'){
            if ($_POST['start'] == ''){
                $start = explode("-", date("Y-m-d", time()));
                $start[1]--;
                $start = implode("-", $start);
            }
            else
                $start = date('Y-m-d', strtotime(implode("-", explode("/", $_POST['start']))));
            
            if ($_POST['end'] == '')
                $end = date("Y-m-d H:i:s", time());
            else{
                $end = explode("/", $_POST['end']);
                $end[0]++;
                $end = date('Y-m-d', strtotime(implode("-", $end)));
            }

            $smmr = $_POST['smmr'];
            $emmr = $_POST['emmr'];
            
            $sql = "SELECT * FROM stats WHERE time >= '$start' AND time <= '$end' AND (avgmmr IS NULL OR (avgmmr >= '$smmr' AND avgmmr <= '$emmr'))";
            //echo $sql;
            
            $result = runQuery($sql);
            $nrows = $result->rowCount();
            $uses = array();
            $abwin = array();
            $abilities = array(
                'fireball',
                'teleport',
                'charge',
                'block',
                'assassinate',
                'ambush',
                'ranged',
                'melee'
            );
            $sumtime = array( 'sum' => 0, 'count' => 0 );
            $highattr = array( 
                'sum' => array( 
                    'STR' => 0, 'AGI' => 0, 'INT' => 0 ),
                'count' => array( 
                    'STR' => 0, 'AGI' => 0, 'INT' => 0 ),
                'total' => 0
            );
            $lvl = array(
                'sum' => array(
                    'avg' => 0, 'winner' => 0),
                'count' => array(
                    'avg' => 0, 'winner' => 0)
            );

            $potions = array(
                'use' => array(),
                'win' => array(),
                'battles' => 0
            );

            while($row = $result->fetch()){
                //set uses of each ability
                foreach ($row as $key => $value){
                    if (in_array($key, $abilities)){
                        if (!isset($uses[$key])){
                            $uses[$key] = array(
                                'sum' => 0,
                                'count' => 0
                            );
                        }
                        if ($value > 0){
                            $uses[$key]['sum'] += $value;
                            $uses[$key]['count']++;
                        }
                    }
                }

                //set every ability the winner cast
                $win = json_decode($row['win']);
                foreach ($win as $ability){
                    if (!isset($abwin[$ability]))
                        $abwin[$ability] = 0;
                    $abwin[$ability]++;
                }

                //time the simulation lasted
                if (!is_null($row['duration'])){
                    $sumtime['sum'] += $row['duration'];
                    $sumtime['count']++;
                }
                
                //how many glads of each attr, how many fight, and total glads
                foreach ($highattr['sum'] as $attr => $val){
                    if (isset($row["high$attr"]) && !is_null($row["high$attr"])){
                        $highattr['sum'][$attr] += $row["high$attr"];
                        $highattr['count'][$attr]++;
                        $highattr['total'] += $row["high$attr"];
                    }
                }
                    
                //total lvl of glads and how many fights
                foreach ($lvl['sum'] as $attr => $val){
                    if (isset($row[$attr."lvl"]) && !is_null($row[$attr."lvl"])){
                        $lvl['sum'][$attr] += $row[$attr."lvl"];
                        $lvl['count'][$attr]++;
                    }
                }

                if (!is_null($row['potionuse'])){
                    $potions['battles']++;
                    $potionuse = json_decode($row['potionuse'], true);
                    foreach ($potionuse as $value){
                        if (isset($potions['use'][$value])){
                            $potions['use'][$value]++;
                        }
                        else{
                            $potions['use'][$value] = 1;
                        }
                    }

                    $potionwin = json_decode($row['potionwin'], true);
                    foreach ($potionwin as $value){
                        if (isset($potions['win'][$value])){
                            $potions['win'][$value]++;
                        }
                        else{
                            $potions['win'][$value] = 1;
                        }
                    }
                }                        
            }
            $info = array(
                'average' => array(),
                'percuse' => array(),
                'percwin' => array()
            );
            //average uses of each ability and % of the winner
            foreach ($uses as $ability => $use){
                $upperab = ucwords($ability);
                if ($use['count'] == 0)
                    $info['average'][$upperab] = 0;
                else
                    $info['average'][$upperab] = $use['sum'] / $use['count'];
                
                $info['percuse'][$upperab] = $use['count'] / $nrows * 100;
                
                if (isset($abwin[$ability]))
                    $info['percwin'][$upperab] = $abwin[$ability] / $use['count'] * 100;
                else
                    $info['percwin'][$upperab] = 0;
            }

            //number of battles found since new infos added
            $info['nbattles'] = array(
                'total' => $nrows,
                'highattr' => $sumtime['count']
            );

            //avg time battles lasted
            if ($sumtime['count'] > 0)
                $info['duration'] = $sumtime['sum'] / $sumtime['count'];

            $info['highattr'] = array(
                'avg' => array(),
                'winner' => array()
            );
            //% glads of each attr, and % of winner of each type
            foreach ($highattr['sum'] as $attr => $val){
                if ($highattr['total'] > 0)
                    $info['highattr']['avg'][$attr] = $highattr['sum'][$attr] / $highattr['total'] * 100;

                if (isset($abwin[strtoupper($attr)]))
                    $info['highattr']['winner'][$attr] = $abwin[strtoupper($attr)] / $highattr['count'][$attr] * 100;
                else
                    $info['highattr']['winner'][$attr] = 0;
            }
            
            //avg lvl of all glads (last 5 secs) and of the winner
            foreach ($lvl['sum'] as $attr => $val){
                if ($lvl['count'][$attr] > 0)
                    $info['highattr'][$attr]['lvl'] = $lvl['sum'][$attr] / $lvl['count'][$attr];
            }

            $info['potions'] = $potions;

            echo json_encode($info);
        }
        
    }
?>
