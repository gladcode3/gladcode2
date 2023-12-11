<?php
	$sendername = 'gladCode';
	$senderemail = 'gladbot@gladcode.dev';

	/*
	//mailjet
	$host = 'in-v3.mailjet.com';
	$port = '80';
	$senderuser = '3d3198fe000a26c2dfb9656b71063111';
	$senderpassword = '2190e217582a90175cb145e0f97bc03a';
	*/

	//amazon
	$host = 'email-smtp.us-east-1.amazonaws.com';
	$port = 587;
	$senderuser = 'AKIA6Q3EGWTCMHB4QO4A';
	$senderpassword = 'BCCp540coFMW2ObhTkmNSKa6HSM6249ak3MoN49XSXby';
	$action = $_POST['action'];

	include_once "connection.php";
	$cancelSend = false;
	if (isset($_GET['teste'])){
		$receivername = "Pablo";
		$receiveremail = 'pswerlang@gmail.com';
		$msgbody = "TEST MESSAGE";
		$assunto = "Test subject";
	}
	elseif (isset($_GET['bulk'])){
		$n = $_GET['bulk'];
		$assunto = "Subject for $n test emails";

		$receivername = array();
		$receiveremail = array();
		$msgbody = array();

		for ($i=0 ; $i<$n ; $i++){
			array_push($receivername, "Pablo");
			array_push($receiveremail, 'contato@gladcode.dev');
			array_push($msgbody, "Test message ". ($i+1));
		}
	}
	elseif ($action  == 'MESSAGE'){
		$message = $_POST['message'];

		if(isset($_POST['replyid'])){
			$id = $_POST['replyid'];
			$sql = "SELECT u.email AS sender FROM messages m INNER JOIN usuarios u ON u.id = m.sender WHERE m.cod = '$id'";
			$result = runQuery($sql);

			$row = $result->fetch();
			$receiveremail = $row['sender'];
		}
		else
			$receiveremail = $_POST['receiver'];
		
		session_start();
		$user = $_SESSION['user'];
		
		$sql = "SELECT apelido FROM usuarios WHERE id = '$user'";
		$result = runQuery($sql);
		$row = $result->fetch();
		
		$usernick = $row['apelido'];
		
		
		$sql = "SELECT * FROM usuarios WHERE email = '$receiveremail'";
		$result = runQuery($sql);
		$row = $result->fetch();
		
		$receivername = $row['apelido'];
		
		$pref = $row['pref_message'];
		if ($pref == "0")
			$cancelSend = true;
		
		$assunto  = "Mensagem de $usernick";

		$vars = array();
		$vars['usernick'] = $receivername;
		$vars['sendernick'] = $usernick;
		$vars['message'] = $message;

		$doc = new DOMDocument();
		$doc->loadHTMLFile("mail/mail_message.html");
		$msgbody = message_replace($doc->saveHTML(), $vars);
	}
	elseif ($action  == 'FRIEND'){
		$receiveremail = $_POST['friend'];
		session_start();
		$user = $_SESSION['user'];
		
		$sql = "SELECT apelido FROM usuarios WHERE id = '$user'";
		$result = runQuery($sql);
		$row = $result->fetch();
		
		$usernick = $row['apelido'];
		
		$sql = "SELECT apelido, pref_friend FROM usuarios WHERE email = '$receiveremail'";
		$result = runQuery($sql);
		$row = $result->fetch();
		
		$receivername = $row['apelido'];
		
		$pref = $row['pref_friend'];
		if ($pref == "0")
			$cancelSend = true;

		$assunto  = "Pedido de amizade de $usernick";

		$vars = array();
		$vars['usernick'] = $receivername;
		$vars['sendernick'] = $usernick;

		$doc = new DOMDocument();
		$doc->loadHTMLFile("mail/mail_friend.html");
		$msgbody = message_replace($doc->saveHTML(), $vars);
	}
	elseif ($action  == 'UPDATE'){
		$version = $_POST['version'];
		$summary = $_POST['summary'];
		$postlink = $_POST['postlink'];
		
		$sql = "SELECT apelido, email FROM usuarios WHERE pref_update = '1' AND email_update != '$version'";// AND email IN('pswerlang@gmail.com','lixoacc@gmail.com')";
		$result = runQuery($sql);
		$receiveremail = array();
		$receivername = array();
		while($row = $result->fetch()){
			array_push($receivername, $row['apelido']);
			array_push($receiveremail, $row['email']);
		}
		
		$assunto  = "Atualização na gladCode";

		$vars = array();
		$vars['version'] = $version;
		$vars['changes'] = $summary;
		$vars['postlink'] = $postlink;

		$doc = new DOMDocument();
		$doc->loadHTMLFile("mail/mail_update.html");
		$msgbody = message_replace($doc->saveHTML(), $vars);
	}
	elseif ($action  == 'DUEL'){
		$friend = $_POST['friend'];
		session_start();
		$user = $_SESSION['user'];
		
		$sql = "SELECT cod FROM amizade WHERE (usuario1 = '$user' AND usuario2 = '$friend') OR (usuario2 = '$user' AND usuario1 = '$friend')";
		$result = runQuery($sql);
		if ($result->rowCount() != 0){
			$sql = "SELECT apelido from usuarios WHERE id = '$user'";
			$result = runQuery($sql);

			$row = $result->fetch();
			$usernick = $row['apelido'];
			
			$sql = "SELECT apelido, pref_duel, email FROM usuarios WHERE id = '$friend'";
			$result = runQuery($sql);
			$row = $result->fetch();
			
			$friendnick = $row['apelido'];
			$friendemail = $row['email'];
			
			$pref = $row['pref_duel'];
			if ($pref == "0")
				$cancelSend = true;
	
			$assunto  = "Desafio para duelo contra $usernick";
	
			$vars = array();
			$vars['friendnick'] = $friendnick;
			$vars['usernick'] = $usernick;

			$doc = new DOMDocument();
			$doc->loadHTMLFile("mail/mail_duel.html");
			$msgbody = message_replace($doc->saveHTML(), $vars);

			$receiveremail = $friendemail;
			$receivername = $friendnick;
		}
	}
	elseif ($action  == 'TOURNAMENT'){
		$hash = $_POST['hash'];

		//get email from those participating in the tournament and not dead
		$sql = "SELECT DISTINCT u.email, u.apelido FROM usuarios u INNER JOIN gladiators g ON g.master = u.id INNER JOIN gladiator_teams glt ON glt.gladiator = g.cod WHERE u.pref_tourn = 1 AND glt.team IN (SELECT te.id FROM tournament t INNER JOIN teams te ON te.tournament = t.id INNER JOIN gladiator_teams glt ON glt.team = te.id INNER JOIN gladiators g ON g.cod = glt.gladiator INNER JOIN usuarios u ON u.id = g.master WHERE t.hash = '$hash' AND (SELECT count(*) FROM gladiator_teams glt INNER JOIN gladiators g ON g.cod = glt.gladiator INNER JOIN teams te ON te.id = glt.team INNER JOIN tournament t ON t.id = te.tournament WHERE g.master = u.id AND glt.dead = 0 AND t.hash = '$hash') > 0)";
		$result = runQuery($sql);

		$receiveremail = array();
		$receivername = array();
		while($row = $result->fetch()){
			array_push($receivername, $row['apelido']);
			array_push($receiveremail, $row['email']);
		}

		$sql = "SELECT max(gr.round) AS maxround, t.name, max(gr.deadline) AS tlimit FROM `groups` gr INNER JOIN group_teams grt ON grt.groupid = gr.id INNER JOIN teams te ON te.id = grt.team INNER JOIN tournament t ON t.id = te.tournament WHERE t.hash = '$hash';
		";
		$result = runQuery($sql);
		$row = $result->fetch();
		$maxround = $row['maxround'];
		$tourn = $row['name'];
		$limit = date_format(date_create($row['tlimit']), "d/m/Y à\s H:i:s");
		
		$assunto  = "Torneio da gladCode";

		$vars = array();
		$vars['round'] = $maxround;
		$vars['tourn'] = $tourn;
		$vars['limit'] = $limit;
		$vars['hash'] = $hash;

		$doc = new DOMDocument();
		$doc->loadHTMLFile("mail/mail_tournament.html");
		$msgtmp = message_replace($doc->saveHTML(), $vars);

		$msgbody = array();
		foreach ($receiveremail as $i => $value){
			$msgtmp2 = str_replace("{{usernick}}",$receivername[$i],$msgtmp);
			array_push($msgbody, $msgtmp2);
		}
	}

	/*********************************** A PARTIR DAQUI NAO ALTERAR ************************************/ 
	$output = array();
	if (!$cancelSend){
		require_once('PHPMailer-master/PHPMailerAutoload.php');
		 
		$mail = new PHPMailer();
		 
		$mail->IsSMTP();
		$mail->SMTPAuth = true;
		//$mail->SMTPSecure = 'tls'; //amazon asks
		$mail->CharSet = 'UTF-8';
		$mail->Host = $host;
		$mail->Port = $port;
		$mail->Username = $senderuser;
		$mail->Password = $senderpassword;
		$mail->From = $senderemail;
		$mail->FromName = $sendername;
		$mail->IsHTML(true);
		$mail->Subject = $assunto;

		if (!is_array($receiveremail)){
			$receiveremail = array($receiveremail);
			$receivername = array($receivername);
			$msgbody = array($msgbody);
		}

		$errorcount = 0;
		foreach($receiveremail as $i => $value){
			if (!is_array($msgbody))
				$mail->Body = $msgbody;
			else
				$mail->Body = $msgbody[$i];

			//$mail->AltBody = $altbody;
			
			if (isset($_POST['self']))
				$receiveremail[$i] = 'pswerlang@gmail.com';
			$mail->ClearAllRecipients();
			$mail->AddAddress($receiveremail[$i],utf8_decode($receivername[$i]));
			
			try {
				$mail->Send();
			}
			catch (Exception $e) {
				if (!is_array($output['error']))
					$output['error'] = array();
				$errorcount++;
				array_push($output['error'], $mail->ErrorInfo);
				$mail->smtp->reset();
			}

			if ($action == "UPDATE"){
				$em = $receiveremail[$i];
				$sql = "UPDATE usuarios SET email_update = '$version' WHERE email = '$em'";
				$result = runQuery($sql);

			}
		}
		if ($errorcount == 0){
			$output['message'] = 'Enviado com sucesso!';
			$output['status'] = "SUCCESS";
		}
		else{
			$output['message'] = "Erro ao enviar $errorcount mensagens";
			$output['status'] = "ERROR";
		}
		$output['count'] = count($receiveremail);
	}
	else{
		$output['message'] = "Envio cancelado";
		$output['status'] = "ABORT";
	}

	echo json_encode($output);

	function message_replace($msg, $obj){
		foreach ($obj as $key => $value){
			$msg = str_replace("{{". $key ."}}",$value,$msg);
			$msg = str_replace("%7B%7B$key%7D%7D",$value,$msg);
		}
		return $msg;
	}
?>