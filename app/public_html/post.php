<!DOCTYPE html>

<?php
    if (isset($_GET['p']))
        $hash = $_GET['p'];
    else
        header("Location: index");
?>

<html>
<head>
    <meta charset='utf-8' />
    <BASE href="../">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/gif" href="icon/gladcode_icon.png" />

	<title>gladCode - Publicação</title>
	<link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">

    <link rel='stylesheet' href="css/header.css"/>
    <link rel='stylesheet' href="css/post.css"/>
    
    <script src='https://code.jquery.com/jquery-3.4.1.min.js'></script>
    <script src='https://code.jquery.com/ui/1.12.1/jquery-ui.min.js'></script>
	<script src="https://kit.fontawesome.com/c1a16f97ec.js" crossorigin="anonymous"></script>

    <script src="script/googlelogin.js"></script>
    <script src="script/socket.js"></script>
    <script src="script/header.js"></script>
    <script src="script/post.js"></script>
    
    </head>
<body>
    <?php 
        include("header.php");
        if (isset($hash))
            echo "<div id='hash' hidden>". $hash ."</div>";
    ?>
	<div id='frame'>
        <div id='post'></div>
        <div id='button-container'>
            <a id='prev' class='disabled' title='Publicação anterior'><i class='fas fa-chevron-left'></i><span>ANTERIOR</span></a>
            <a id='next' class='disabled' title='Próxima publicação'><span>PRÓXIMO</span><i class='fas fa-chevron-right'></i></a>
        </div>
	</div>
	<div id='footer'></div>
</body>
</html>

