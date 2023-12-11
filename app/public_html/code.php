<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8' />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google-signin-client_id" content="1036458629781-8j247asma3gm7u956gbn3d0m0nobqhie.apps.googleusercontent.com">
    <link rel="icon" type="image/gif" href="icon/gladcode_icon.png" />
    <title>gladCoding</title>
    <link rel='stylesheet' href="css/code.css"/>
    
    <link href="https://fonts.googleapis.com/css?family=Source+Code+Pro&display=swap" rel="stylesheet">
    <link rel='stylesheet' href='https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css'/>
    <script src='https://code.jquery.com/jquery-3.4.1.min.js'></script>
    <script src='https://code.jquery.com/ui/1.12.1/jquery-ui.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.6/ace.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.6/ext-language_tools.js"></script>
    <script src="https://apis.google.com/js/platform.js" async defer></script>
    
    <script src="script/code.js"></script>
    <script src="script/tabs.js"></script>
    <script src="script/keybinds.js"></script>
    <script src="script/socket.js"></script>
    <script src="script/file_manager.js"></script>
    <script src="script/googlelogin.js"></script>
    
    </head>
<body>
    <div id='frame'>
        <div id='panel'>
            <div id='gladcode' class='mrow'>
                <img src='icon/gladcode_icon.png' title='Ir para gladCode'>
                <span class='name'>Página inicial da gladCode</span>
            </div>
            <div id='button-new' class='mrow'>
                <img src='icon/new_file.png' title='Abrir nova aba (Alt+N)'>
                <span class='name'>Abrir nova aba</span>
            </div>
            <div id='button-cload' class='mrow'>
                <img src='icon/cloud_download.png' title='Abrir arquivo da nuvem (Alt+O)'>
                <span class='name'>Abrir arquivo da nuvem</span>
            </div>
            <div id='button-csave' class='mrow'>
                <img src='icon/cloud_sync.png' title='Sincronizar com a nuvem (Alt+S)'>
                <span class='name'>Sincronizar com a nuvem</span>
            </div>
            <div id='button-csync' class='mrow' title='Sincronizando com a nuvem' hidden>
                <img src='icon/cloud_ok.png'>
                <span class='name'>Sincronizar com a nuvem</span>
            </div>
            <div id='button-load' class='mrow'>
                <img src='icon/open_file.png' title='Abrir arquivo local (Ctrl+O)'>
                <span class='name'>Abrir arquivo local</span>
            </div>
            <div id='button-save' class='mrow'>
                <img src='icon/save_file.png' title='Baixar arquivo (Ctrl+S)'>
                <span class='name'>Baixar arquivo</span>
            </div>
            <div id='button-fontm' class='mrow'>
                <img src='icon/font_minus.png' title='Diminuir tamanho da fonte (Ctrl+-)'>
                <span class='name'>Diminuir tamanho da fonte</span>
            </div>
            <div id='button-fontp' class='mrow'>
                <img src='icon/font_plus.png' title='Aumentar tamanho da fonte (Ctrl++)'>
                <span class='name'>Aumentar tamanho da fonte</span>
            </div>
            <div id='button-input' class='mrow'>
                <img src='icon/terminal.png' title='Inserir entrada de dados (F8)'>
                <span class='name'>Inserir entrada de dados</span>
            </div>
            <div id='button-run' class='mrow'>
                <img src='icon/run_code.png' title='Executar programa (F9)'>
                <span class='name'>Executar programa</span>
            </div>
            <div id='button-layout' class='mrow'>
                <img src='icon/layout.png' title='Alterar layout (F10)'>
                <span class='name'>Alterar layout</span>
            </div>
            <div id='button-fullscreen' class='mrow'>
                <img src='icon/full_screen.png' title='Modo tela cheia (F11)'>
                <span class='name'>Modo tela cheia</span>
            </div>
            <div id='button-help' class='mrow'>
                <img src='icon/question.png' title='Exibir ajuda (F12)'>
                <span class='name'>Exibir ajuda</span>
            </div>
        </div>
        <div id='tab-bar'>
            <img id='button-menu' src='icon/menu_icon.png'>
        </div>
        <div id='editor'>
            <div id='code-wrapper'>
                <pre id='code'></pre>
            </div>
            <div id='term-wrapper'>
                <div id='term-title'>
                    <img id='button-close-term' class='red' src='icon/close_x.png'>
                </div>
                <pre id='term'></pre>
            </div>
        </div>
        <input type='file' hidden id='fileload'>
    </div>
</body>
</html>