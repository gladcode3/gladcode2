<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8' />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/gif" href="icon/gladcode_icon.png" />
    <title>gladCode - Editor</title>
    <link href="https://fonts.googleapis.com/css?family=Acme|Roboto|Source+Code+Pro&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.17.1/themes/prism-coy.min.css" rel="stylesheet" type="text/css"/>

    <link rel='stylesheet' href="css/sprite.css"/>
    <link rel='stylesheet' href="css/slider.css"/>
    <link rel='stylesheet' href="css/glad-card.css"/>
    <link rel='stylesheet' href="css/dialog.css"/>
    <link rel='stylesheet' href="css/chat.css"/>
    <link rel='stylesheet' href="css/header.css"/>
    <link rel='stylesheet' href="css/editor.css"/>
    
    <link rel='stylesheet' href='https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css'/>
    <script src="https://kit.fontawesome.com/c1a16f97ec.js" crossorigin="anonymous"></script>
    <script src='https://code.jquery.com/jquery-3.4.1.min.js'></script>
    <script src='https://code.jquery.com/ui/1.12.1/jquery-ui.min.js'></script>
    <script src="https://widget.cloudinary.com/v2.0/global/all.js"></script>
    <script src="https://rawgithub.com/ajaxorg/ace-builds/master/src/ace.js" type="text/javascript" charset="utf-8"></script>
    <script src="https://rawgithub.com/ajaxorg/ace-builds/master/src/ext-language_tools.js" type="text/javascript" charset="utf-8"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.17.1/prism.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.17.1/plugins/autoloader/prism-autoloader.min.js"></script>
    <script>Prism.plugins.autoloader.languages_path = 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.17.1/components/'</script>
    <script src="https://cdn.jsdelivr.net/npm/blockly@3.20200123.1/blockly.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/blockly@3.20200123.1/msg/pt-br.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/blockly@3.20200123.1/python.js"></script>

    <!-- <script src="script/fallback/font-awesome.js" crossorigin="anonymous"></script>
    <script src='script/fallback/jquery-3.4.1.min.js'></script>
    <script src='script/fallback/jquery-ui.min.js'></script>
    <script src="script/fallback/cloudinary.js"></script>
    <script src="script/fallback/ace.js" type="text/javascript" charset="utf-8"></script>
    <script src="script/fallback/ace-ext-language_tools.js" type="text/javascript" charset="utf-8"></script>
    <script src="script/fallback/prism.min.js"></script>
    <script src="script/fallback/prism-autoloader.min.js"></script>
    <script>Prism.plugins.autoloader.languages_path = 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.17.1/components/'</script>
    <script src="script/fallback/blockly.min.js"></script>
    <script src="script/fallback/blockly-pt-br.js"></script>
    <script src="script/fallback/blockly-python.js"></script> -->

    <script src="script/editor.js"></script>
    <script src="script/assets.js"></script>
    <script src="script/dialog.js"></script>
    <script src="script/runSim.js"></script>
    <script src="script/tutorial.js"></script>
    <script src="script/googlelogin.js"></script>
    <script src="script/header.js"></script>
    <script src="script/socket.js"></script>
    <script src="script/emoji.js"></script>
    <script src="script/chat.js"></script>
    <script src="script/blocks.js"></script>
    
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
        include_once "connection.php";
        session_start();
        
        if(isset($_SESSION['user']) && isset($_GET['g'])) {
            $user = $_SESSION['user'];
            unset($_SESSION['code']);
            $id = $_GET['g'];
            if ($id == 0){
                echo "<div id='newglad'></div>";
            }
            else{
                $sql = "SELECT * FROM gladiators INNER JOIN usuarios ON id = master WHERE master = '$user' AND cod = $id";
                $result = runQuery($sql);
                if ($result->rowCount() > 0){
                    $row = $result->fetch();
                    $name = $row['name'];
                    $vstr = $row['vstr'];
                    $vagi = $row['vagi'];
                    $vint = $row['vint'];
                    $skin = $row['skin'];
                    $nick = $row['apelido'];
                    $code = htmlspecialchars($row['code']);
                    $blocks = htmlspecialchars($row['blocks']);
                    echo "<div id='glad-code' hidden>
                        <div id='idglad'>$id</div>
                        <div id='name'>$name</div>
                        <div id='vstr'>$vstr</div>
                        <div id='vagi'>$vagi</div>
                        <div id='vint'>$vint</div>
                        <div id='skin'>$skin</div>
                        <div id='code'>$code</div>
                        <div id='blocks'>$blocks</div>
                        <div id='user'>$nick</div>
                    </div>";
                }
            }
        }
        include("header.php");
    ?>
    <div id='frame'>
        <div id='panel-left'>
            <div id='profile-icon' class='mrow' title='Ir para o seu perfil'>
                <img src='icon/profile.png'>
            </div>
            <div id='new' class='mrow' title='Criar novo gladiador'>
                <i class="fas fa-baby"></i>
            </div>
            <div id='open' class='mrow' title='Editar outro gladiador'>
                <i class="fas fa-users"></i>
            </div>
            <div id='save' class='mrow disabled' title='Guardar alterações no gladiador'>
                <i class='fas fa-sd-card'></i>
            </div>
            <div id='skin' class='mrow' title='Painel de aparência do gladiador'>
                <i class='fas fa-paint-roller'></i>
            </div>
            <div id='test' class='mrow disabled' title='Testar gladiador em batalha'>
                <i class='fas fa-gamepad'></i>
            </div>
            <div id='switch' class='mrow' title='Alternar para editor de blocos'>
                <i class='fas fa-puzzle-piece'></i>
            </div>
            <div id='settings' class='mrow' title='Preferências'>
                <i class='fas fa-cog'></i>
            </div>
            <div id='help' class='mrow' title='Ajuda'>
                <i class='fas fa-question-circle'></i>
            </div>
        </div>
        <div id='panel-left-opener' class='open'></div>
        <div id='editor'>
            <pre id='code'></pre>
            <div id='blocks'></div>
        </div>
        <div id='panel-right'>
        </div>
    </div>
    <div id='float-card'>
        <div class='glad-card-container'>
            <div class='glad-preview'></div>
        </div>
    </div>
    <div id='fog-skin' class='fog'></div>
    <div id='fog-glads' class='fog'>
        <div id='open-glad'>
            <div id='message'>
                <h2>Editar gladiador</h2>
                <h3>Selecione um de seus gladiadores</h3>
            </div>
            <div class='glad-card-container'></div>
            <div id='button-container'>
                <button id='btn-glad-cancel' class='button'>CANCELAR</button>
                <button id='btn-glad-open' class='button' disabled>ABRIR</button>
            </div>
        </div>
    </div>
    <div id='fog-battle' class='fog'>
        <div id='battle-window'>
            <div id='message'>
                <h2>Testar gladiador</h2>
                <h3>Selecione os gladiadores que serão os oponentes de <span></span></h3>
            </div>
            <div id='selection-container'>
                <div id='list-container'>
                    <div id='list-title'><span></span><img src='icon/death-skull.png' title='Dificuldade'></div>
                    <div id='list'></div>
                </div>
                <div class='glad-card-container'>
                </div>
            </div>
            <div id='button-container'>
                <button id='btn-cancel' class='button'>CANCELAR</button>
                <button id='btn-battle' class='button' disabled>BATALHA</button>
            </div>
        </div>
    </div>
    <div id='chat-panel'></div>
</body>
</html>