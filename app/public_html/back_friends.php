<?php
	session_start();
	include_once "connection.php";
	include("back_node_message.php");

	if (isset($_POST['action']) && isset($_SESSION['user'])){
		$action = $_POST['action'];
		$user = $_SESSION['user'];
		
		if($action == "GET"){
			$sql = "SELECT a.cod, u.apelido, u.foto, u.lvl FROM amizade a INNER JOIN usuarios u ON u.id = a.usuario1 WHERE a.usuario2 = '$user' AND pendente = 1";
			$result = runQuery($sql);
			
			$pending = array();
			while($row = $result->fetch()){
				$p = array();
				$p['id'] = $row['cod'];
				$p['nick'] = $row['apelido'];
				$p['picture'] = $row['foto'];
				$p['lvl'] = $row['lvl'];
				array_push($pending, $p);
			}

			$fields = "a.cod, u.id, u.apelido, u.lvl, u.foto, TIMESTAMPDIFF(MINUTE,ativo,now()) as ultimoativo";
			$sql = "SELECT $fields FROM amizade a INNER JOIN usuarios u ON u.id = a.usuario1 WHERE a.usuario2 = '$user' AND pendente = 0 UNION SELECT $fields FROM amizade a INNER JOIN usuarios u ON u.id = a.usuario2 WHERE a.usuario1 = '$user' AND pendente = 0";
			$result = runQuery($sql);

			$confirmed = array();
			while($row = $result->fetch()){
				$c = array();
				$c['id'] = $row['cod'];
				$c['user'] = $row['id'];
				$c['nick'] = $row['apelido'];
				$c['lvl'] = $row['lvl'];
				$c['active'] = $row['ultimoativo'];
				$c['picture'] = $row['foto'];
				array_push($confirmed, $c);
			}

			$output = array();
			$output['pending'] = $pending;
			$output['confirmed'] = $confirmed;
			echo json_encode($output);
		}
		elseif ($action == "REQUEST"){
			$id = $_POST['id'];
			if ($_POST['answer'] == "YES")
				$sql = "UPDATE amizade SET pendente = '0' WHERE cod = '$id' AND usuario2 = '$user'";
			else
				$sql = "DELETE FROM amizade WHERE cod = '$id' AND usuario2 = '$user'";
			$result = runQuery($sql);
			echo "OK";

			send_node_message(array(
				'profile notification' => array('user' => array($user))
			));
		}
		elseif ($action == "SEARCH"){
			$text = $_POST['text'];
			$sql = "SELECT apelido, id, email FROM usuarios WHERE apelido LIKE '%$text%' AND id != '$user' LIMIT 10";
			$result = runQuery($sql);

			$output = array();
			while($row = $result->fetch()){
				$person = array();
				$person['nick'] = $row['apelido'];
				$person['user'] = $row['id'];
				$person['email'] = $row['email'];
				array_push($output, $person);
			}
			echo json_encode($output);
		}
		elseif ($action == "DELETE"){
			$id = $_POST['user'];
			$sql = "DELETE FROM amizade WHERE cod = '$id' AND (usuario1 = '$user' OR usuario2 = '$user')";
			$result = runQuery($sql);
			echo "OK";
		}
		elseif ($action == "ADD"){
			$friend = $_POST['user'];
			$sql = "SELECT * FROM amizade WHERE (usuario1 = '$user' AND usuario2 = '$friend') OR (usuario2 = '$user' AND usuario1 = '$friend')";
			$result = runQuery($sql);
			if ($result->rowCount() == 0){
				$sql = "INSERT INTO amizade (usuario1,usuario2) VALUES ('$user','$friend')";
				$result = runQuery($sql);
				echo "OK";

				send_node_message(array(
					'profile notification' => array('user' => array($friend))
				));
			}
			else
				echo "EXISTS";
		}
		elseif ($action == "FILTER"){
			$text = $_POST['text'];
			$fields = "a.cod, u.id, u.apelido, u.lvl, u.foto";
			$sql = "SELECT $fields FROM amizade a INNER JOIN usuarios u ON u.id = a.usuario1 WHERE a.usuario2 = '$user' AND pendente = 0 AND apelido LIKE '%$text%' UNION SELECT $fields FROM amizade a INNER JOIN usuarios u ON u.id = a.usuario2 WHERE a.usuario1 = '$user' AND pendente = 0 AND apelido LIKE '%$text%'";
			$result = runQuery($sql);

			$friends = array();
			$c = 0;
			while($row = $result->fetch()){
				$friends[$c] = array();
				$friends[$c]['id'] = $row['cod'];
				$friends[$c]['user'] = $row['id'];
				$friends[$c]['nick'] = $row['apelido'];
				$friends[$c]['lvl'] = $row['lvl'];
				$friends[$c]['picture'] = $row['foto'];
				$c++;
			}

			echo json_encode($friends);
		}
	}
?>