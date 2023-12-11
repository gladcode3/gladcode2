<?php
	session_start();
	include_once "connection.php";
	$action = $_POST['action'];

	$sql = "SELECT id FROM usuarios WHERE email = 'pswerlang@gmail.com'";
	$result = runQuery($sql);
	$row = $result->fetch();
	$id = $row['id'];

	if (isset($_SESSION['user']) && $_SESSION['user'] == $id){
		if($action == 'GET'){
			$version = explode(".", file_get_contents("version"));
			echo json_encode($version);
		}
		elseif($action == 'SET'){
			if (md5($_POST['pass']) != '07aec7e86e12014f87918794f521183b')
				echo "WRONGPASS";
			else{
				$version = $_POST['version'];
				$keepup = $_POST['keepup'];
				if ($keepup == "true"){
					$oldversion = file_get_contents("version");
					$sql = "UPDATE gladiators SET version = '$version' WHERE version = '$oldversion'";
					$result = runQuery($sql);
				}
				file_put_contents("version", $version);
				echo "OK";
			}
		}
	}
?>