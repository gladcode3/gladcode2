<!DOCTYPE html>

<html>
<head>
    <meta charset='utf-8' />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="Programação, Gladiador, Arena, Robocode, RPG">
    <meta name="description" content="Batalha de agentes virtuais autônomos.">
    <link rel="icon" type="image/gif" href="icon/gladcode_icon.png" />
    
    <meta property="og:title" content="gladCode">
    <meta property="og:image" content="https://www.gladcode.dev/icon/gladcode_icon.png">
    <meta property="og:image:width" content="200">
    <meta property="og:image:height" content="200">	

    <meta property="og:url" content="https://gladcode.dev">
    <meta property="og:description" content="Batalha de agentes virtuais autônomos.">
    
    <title>gladCode</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto|Source+Code+Pro&display=swap" rel="stylesheet">
    
    <link rel='stylesheet' href="css/index.css"/>
    <link rel='stylesheet' href="css/dialog.css"/>
    <link rel='stylesheet' href="css/header.css"/>
    
    <script src='https://code.jquery.com/jquery-3.4.1.min.js'></script>
    <script src='https://code.jquery.com/ui/1.12.1/jquery-ui.min.js'></script>
    <script src="script/index.js"></script>
    <script src="script/dialog.js"></script>
    <script src="script/socket.js"></script>
    <script src="script/googlelogin.js"></script>
    <script src="script/header.js"></script>
    
    </head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-VT4EF5GTBP"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-VT4EF5GTBP');
    </script>
<?php
    include("header.php");
    if (isset($_GET['login'])){
        if (isset($_SESSION['user'])){
            $link = "profile.php?t=".$_GET['login'];
            header("Location: $link");
        }
        else
            echo "<div id='loginhash' hidden>".$_GET['login']."</div>";
    }
?>

<body>
    <div id='section-1'>
        <div class='card'>
            <div class='title'>Onde a programação e os jogos se encontram</div>
            <div class='body'>Programe o comportamento de seus gladiadores e faça-os lutar usando magias e habilidades especiais enquanto eles ganham poder e experiêcia. Não parece divertido?</div>
        </div>
    </div>
    <div id='section-2'>
        <div class='big'>
            <div class='card-wrapper'>
                <div class='card'>
                    <div class='container'>
                        <div class='content'>
                            <div class='title'><img src='image/huntress.png'>Desenvolva sua lógica</div>
                            <div class='text'>Treine suas habilidades de programação e veja os resultados em um ambiente diferente da tradicional tela preta. Na gladCode a lógica que você criar definirá o comportamento e a inteligência dos gladiadores. Dentro da arena eles não poderão receber sua ajuda, portanto você precisa antecipar cada detalhe.</div>
                        </div>
                        <div class='video'><img class='play' src='icon/youtube-play.png'><img class='thumb' src="https://img.youtube.com/vi/te1M98UDKiM/mqdefault.jpg"></div>
                    </div>
                    <div class='a-wrapper'><a href='manual'>Conheça a simulação</a></div>
                </div>
            </div>
            <div class='card-wrapper'>
                <div class='card'>
                    <div class='container'>
                        <div class='content'>
                            <div class='title'><img src='image/archer.png'>Entre no clima</div>
                            <div class='text'>Ambientado com uma temática de fantasia medieval, a gladCode proprociona uma experiência épica e divertida onde você pode criar seus gladiadores para serem guerreiros, ladinos, magos ou qualquer combinação que você preferir. Com uma grande quantidade de opções para configurar a aparência dos seus gladiadores, atributos, e é claro comportamento, cada gladiador que você criar será único.</div>
                        </div>
                        <div class='video'><img class='play' src='icon/youtube-play.png'><img class='thumb' src="https://img.youtube.com/vi/tjMjqQ14AS8/mqdefault.jpg"></div>
                    </div>
                    <div class='a-wrapper'><a href='editor'>Crie seu gladiador</a></div>
                </div>
            </div>
            <div class='card-wrapper'>
                <div class='card'>
                    <div class='container'>
                        <div class='content'>
                            <div class='title'><img src='image/mage.png'>Sinta o poder</div>
                            <div class='body'>Tenha à sua disposição uma série de magias e habilidades especiais que farão seus oponentes cair aos seus pés. A combinação escolhida de atributos do gladiador, combinada ao uso eficiente das habilidades farão seu gladiador subir de nível ficando cada vez mais poderoso, e você subir de ranking em emocionantes partidas multiplayer onde só os fortes chegam ao topo.</div>
                        </div>
                        <div class='video'><img class='play' src='icon/youtube-play.png'><img class='thumb' src="https://img.youtube.com/vi/5QQtfruq8_8/mqdefault.jpg"></div>
                    </div>
                </div>
            </div>
            <div class='card-wrapper'>
                <div class='card'>
                    <div class='container'>
                        <div class='content'>
                            <div class='title'><img src='image/orc.png'>Conheça suas ferramentas</div>
                            <div class='text'>Na gladCode, você possui à sua disposição toda sintaxe básica da linguagem de programação. Mas como não existe terminal para entrada e saída de dados, existem mais de 50 funções que possibilitam os gladiadores executarem as mais diversas ações dentro da arena, como movimentar, virar, atacar, usar uma magia ou habilidade, detectar inimigos, informações sobre si mesmo, dentre muitas outras. O conhecimento da função certa para o momento certo é a chave para a vitória.</div>
                        </div>
                        <div class='video'><img class='play' src='icon/youtube-play.png'><img class='thumb' src="https://img.youtube.com/vi/Wrc-0_Kq-_4/mqdefault.jpg"></div>
                    </div>
                    <div class='a-wrapper'><a href='docs'>Aprenda mais</a></div>
                </div>
            </div>
        </div>
        <div class='small'>
            <div class='card-wrapper'>
                <div class='card'>
                    <div class='title'><img src='image/girl.png'>Como participo?</div>
                    <div class='body'>Para participar basta fazer login com sua conta do Google. Fazendo login Você poderá criar e editar todos seus gladiadores, visualizar seu ranking e comparar com o de seus amigos e também disputar emocionantes partidas multiplayer.</div>
                    <div class='a-wrapper'><a id='account' href='#'>Fazer login</a></div>
                </div>
            </div>
            <div class='card-wrapper' hidden>
                <div class='card'>
                    <div class='title'><img src='image/warrior.png'>Engaje-se nas batalhas</div>
                    <div class='body'>Nos modos clássicos de batalha e torneio, você não precisa possuir um perfil na gladCode, basta fazer upload do código dos gladiadores e deixarem eles lutarem até a morte.</div>
                    <div class='a-wrapper'><a href='socks'>Teste as batalhas clássicas</a></div>
                    <div class='a-wrapper'><a href='tournment'>Crie um torneio clássico</a></div>
                </div>
            </div>
            <div class='card-wrapper'>
                <div class='card'>
                    <div class='title'><img src='image/dress.png'>Interaja com a comunidade</div>
                    <div class='body'>Mostre para todos seus gladiadores, compartilhe estratégias, tire suas dúvidas, peça ajuda ou simplesmente divirta-se na nossa página do facebook ou comunidade do reddit.</div>
                    <div class='a-wrapper'><a href='https://forms.gle/BDbSmcLpPgwLe4Uc7' target='_blank'>gladCode no whatsapp</a></div>
                    <div class='a-wrapper'><a href='https://www.reddit.com/r/gladcode/' target='_blank'>gladCode no reddit</a></div>
                    <div class='a-wrapper'><a href='https://www.facebook.com/gladcode/' target='_blank'>gladCode no facebook</a></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>