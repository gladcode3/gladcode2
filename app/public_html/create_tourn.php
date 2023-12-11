<html>
<body>

<?php
include_once "connection.php";
$output = array();

//test for creating test tournaments
if (isset($_GET['n']) && isset($_GET['t'])){
    $name = $_GET['n'];
    $nteams = $_GET['t'];
    
    $sql = "SELECT id FROM usuarios WHERE email = 'pswerlang@gmail.com'";
    $result = runQuery($sql);
    $row = $result->fetch();
    $manager = $row['id'];

    $sql = "INSERT INTO tournament (name, creation, maxteams, maxtime, flex, manager, hash, password, description) VALUES ('$name', now(), 50, '00:30:00', 1, '$manager', '', '', '')";
    $result = runQuery($sql);
    $tournid = $conn->lastInsertId();

    $teams = array();
    for ($i=0 ; $i<$nteams ; $i++){
        $sql = "INSERT INTO teams (name, password, tournament, modified) VALUES ('eq$i','bababa', $tournid, now())";
        $result = runQuery($sql);
        array_push($teams, $conn->lastInsertId());
    }

    $limit = $nteams*3;
    $glads = array();
    $sql = "SELECT g.cod FROM gladiators g GROUP BY g.master LIMIT $limit";
    $result = runQuery($sql);
    while ($row = $result->fetch()){
        array_push($glads, $row['cod']);
    }

    foreach($teams as $team){
        for ($i=0 ; $i<3 ; $i++){
            $glad = array_pop($glads);
            $sql = "INSERT INTO gladiator_teams (gladiator, team) VALUES ($glad, $team)";
            $result = runQuery($sql);
        }
    }

    $output['status'] = "DONE";
}
else
    $output['status'] = "NOTSET";

echo "<div>". json_encode($output) ."</div>"; 

?>

</body>
</html>