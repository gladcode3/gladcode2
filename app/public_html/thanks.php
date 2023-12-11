<!DOCTYPE html>
<html>
<head>
	<meta charset='utf-8' />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="icon" type="image/gif" href="icon/gladcode_icon.png" />
	<title>gladCode - Obrigado</title>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link href="https://fonts.googleapis.com/css?family=Roboto|Source+Code+Pro&display=swap" rel="stylesheet">
	<link type='text/css' rel='stylesheet' href='css/header.css'/> 
	<script src='https://code.jquery.com/jquery-3.4.1.min.js'></script>
	<script src='https://code.jquery.com/ui/1.12.1/jquery-ui.min.js'></script>
	<script type="text/javascript" src="script/header.js"></script>
	<script type="text/javascript" src="script/googlelogin.js"></script>

	<style>
		body{
			margin: 0;
			background-color: #edf1f3;
			font-family: 'Source Code Pro', monospace;
			font-size: 16px;
			display: flex;
			flex-direction: column;
			justify-content: space-between;
			height: 100vh;
		}

		#frame {
			width: 100%;
			display: flex;
			align-items: center;
			justify-content: center;
			flex-direction: column;
			padding-top: 50px;
			flex-grow: 1;
		}
		#content-box {
			display: flex;
			flex-direction: column;
			align-items: flex-start;
			background-color: white;
			box-shadow: 1px 1px 5px 0px black;
			border-radius: 0px;
			padding: 20px 15px;
			z-index: 1;
			width: 900px;
			max-width: 100%;
			box-sizing: border-box;
			font-family: 'Roboto', sans-serif;
			color: #616161;
			justify-content: center;
			height: 300px;
		}
		h2, h3 {
			margin-left: 20px;
		}	
		#footer-wrapper {
			position: relative;
		}
	</style>
	<script>
		time();
		function time() {
			setTimeout(() => {
				var val = parseInt($('#content-box span').html()) - 1;
				$('#content-box span').html(val);
				if (val == 0)
					window.location.href = 'news';
				else
					time();
			}, 1000);
		}
	</script>
</head>
<body>
    <?php include("header.php"); ?>
	<div id='frame'>
        <div id='content-box'>
            <h2>A gladCode agradece seu apoio.</h2>
            <h3>Você será redirecionado em <span>10</span> segundos.</h2>
        </div>
	</div>
</body>
</html>