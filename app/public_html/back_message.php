<?php
	session_start();
	include_once "connection.php";
	include("back_node_message.php");
	$action = $_POST['action'];

	if ($action == "GET"){
		$user = $_SESSION['user'];
		$page = $_POST['page'];

		$sql = "SELECT cod FROM messages m INNER JOIN usuarios u ON u.id = m.sender WHERE m.receiver = '$user'";
		$result = runQuery($sql);
		$total = $result->rowCount();

		$units = 10;
		
		if ($page < 1)
			$page = 1;
		$offset = $units * ($page - 1);
		if ($offset >= $total){
			$offset = $total - 1;
			$page--;
		}
		
		if ($offset < 0)
			$offset = 0;

		$sql = "SELECT * FROM messages m INNER JOIN usuarios u ON u.id = m.sender WHERE m.receiver = '$user' ORDER BY time DESC LIMIT $units OFFSET $offset";
		$result = runQuery($sql);
		
		$meta = array();
		$meta['page'] = $page;
		$meta['total'] = $total;
		$meta['start'] = $offset + 1;
		$meta['end'] = $offset + $result->rowCount();

		$info = array();
		$i = 0;
		while($row = $result->fetch()){
			$info[$i] = array();
			$info[$i]['id'] = $row['cod'];
			$info[$i]['sender'] = $row['sender'];
			$info[$i]['nick'] = $row['apelido'];
			$info[$i]['picture'] = $row['foto'];
			$info[$i]['time'] = $row['time'];
			$info[$i]['message'] = $row['message'];
			$info[$i]['isread'] = $row['isread'];
			$i++;
		}

		$output = array('meta' => $meta, 'info' => $info);
		
		echo json_encode($output);
	}
	elseif ($action == "SEND"){
		$sender = $_SESSION['user'];
		$receiver = $_POST['id'];
		$message = htmlentities($_POST['message']);
		
		$sql = "INSERT INTO messages (time, sender, receiver, message) VALUES (now(), '$sender', '$receiver', '$message')";
		echo $sql;
		$result = runQuery($sql);

		send_node_message(array(
			'profile notification' => array('user' => array($receiver))
		));
	}
	elseif ($action == "READ"){
		$user = $_SESSION['user'];
		$id = $_POST['id'];
		$val = $_POST['value'];
		$sql = "UPDATE messages SET isread = '$val' WHERE cod = '$id' AND receiver = '$user'";
		$result = runQuery($sql);

		send_node_message(array(
			'profile notification' => array('user' => array($user))
		));
	}
	elseif ($action == "DELETE"){
		$user = $_SESSION['user'];
		$id = $_POST['id'];
		$sql = "DELETE FROM messages WHERE cod = '$id' AND receiver = '$user'";
		$result = runQuery($sql);
	}
	elseif ($action == "REPLY"){
		$sender = $_SESSION['user'];
		$id = $_POST['replyid'];
		$message = htmlentities($_POST['message']);
		
		$sql = "SELECT * FROM messages WHERE cod = '$id' AND receiver = '$sender'";
		$result = runQuery($sql);

		$row = $result->fetch();
		$receiver = $row['sender'];
		$oldmessage = $row['message'];
		$message = "<quote>$oldmessage</quote>$message";
		
		$sql = "INSERT INTO messages (time, sender, receiver, message) VALUES (now(), '$sender', '$receiver', '$message')";
		$result = runQuery($sql);

		send_node_message(array(
			'profile notification' => array('user' => array($receiver))
		));
	}

?>