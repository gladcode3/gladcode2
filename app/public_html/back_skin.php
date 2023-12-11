<?php
	include_once "connection.php";
	
	if ($_POST['action'] == "SAVE"){
		$skin = $_POST['skin'];
		$hash = md5('skinhash'. $skin);
		
		$sql = "SELECT * FROM skins WHERE skin = '$skin'";
		if(!$result = $conn->query($sql)){ die('There was an error running the query [' . $conn->error . ']'); }

		if ($result->rowCount() == 0){
			$sql = "INSERT INTO skins (hash,skin) VALUES ('$hash','$skin')";
			if(!$result = $conn->query($sql)){ die('There was an error running the query [' . $conn->error . ']'); }
		}
		
		echo $hash;
	}
	if ($_POST['action'] == "GET"){
		$hash = $_POST['hash'];
		
		$sql = "SELECT * FROM skins WHERE hash = '$hash'";
		if(!$result = $conn->query($sql)){ die('There was an error running the query [' . $conn->error . ']'); }

		if ($result->rowCount() > 0){
			$row = $result->fetch();
			echo $row['skin'];
		}
	}
?>