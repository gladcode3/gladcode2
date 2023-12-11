<!DOCTYPE html>

<?php
    session_start();
    if (isset($_GET['h']))
        $hash = $_GET['h'];

    if (isset($_GET['r']))
        $round = $_GET['r'];
    else
        header("Location: $hash/0");
?>

<html>
<head>
    <meta charset='utf-8' />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <BASE href="../../">
    <link rel="icon" type="image/gif" href="icon/gladcode_icon.png" />
    <title>gladCode - Treino</title>
    
    <link href="https://fonts.googleapis.com/css?family=Orbitron|Acme|Roboto|Source+Code+Pro&display=swap" rel="stylesheet">    
    <link rel='stylesheet' href='https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css'/> 
    
    <link rel='stylesheet' href="css/header.css"/>
    <link rel='stylesheet' href="css/dialog.css"/>
    <link rel='stylesheet' href="css/tournament.css"/>
    <link rel='stylesheet' href="css/training.css"/>
    <link rel='stylesheet' href="css/glad-card.css"/>
    <link rel='stylesheet' href="css/chat.css"/>

    <script src='https://code.jquery.com/jquery-3.4.1.min.js'></script>
    <script src='https://code.jquery.com/ui/1.12.1/jquery-ui.min.js'></script>
    <script src="https://widget.cloudinary.com/v2.0/global/all.js"></script>
    <script src="https://kit.fontawesome.com/c1a16f97ec.js" crossorigin="anonymous"></script>

    <script src="script/header.js"></script>
    <script src="script/dialog.js"></script>
    <script src="script/glad-card.js"></script>
    <script src="script/runSim.js"></script>
    <script src="script/assets.js"></script>
    <script src="script/emoji.js"></script>
    <script src="script/socket.js"></script>
    <script src="script/chat.js"></script>
    <script src="script/googlelogin.js"></script>
    <script src="script/training.js"></script>

    </head>
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-VT4EF5GTBP"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-VT4EF5GTBP');
    </script>

<body>
    <?php
        include("header.php");
        echo "<div id='hash' hidden>$hash</div><div id='round' hidden>$round</div>";
    ?>
    <div id='frame'>
        <div id='content-wrapper'>
            <div id='content-box'></div>
            <div id='footer'></div>
        </div>
        <div id='chat-panel' class='tournament'></div>
    </div>
</body>
</html>