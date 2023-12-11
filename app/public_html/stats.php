<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8' />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/gif" href="icon/gladcode_icon.png" />
    <title>gladCode - Estatísticas</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto|Source+Code+Pro&display=swap" rel="stylesheet">
    <link rel='stylesheet' href="css/side-menu.css"/>
    <link rel='stylesheet' href="css/table.css"/>
    <link rel='stylesheet' href="css/stats.css"/>
    <link rel='stylesheet' href="css/dialog.css"/>
    <link rel='stylesheet' href="css/header.css"/>
    
    <script src='https://code.jquery.com/jquery-3.4.1.min.js'></script>
    <script src='https://code.jquery.com/ui/1.12.1/jquery-ui.min.js'></script>
    <script src="https://kit.fontawesome.com/c1a16f97ec.js" crossorigin="anonymous"></script>

    <script src="script/stats.js"></script>
    <script src="script/stats_func.js"></script>
    <script src="script/side-menu.js"></script>
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

<body>
    <?php include("header.php"); ?>
    <div id='frame'>
        <div id='side-menu'></div>
        <div id='right-side'>
            <div id='content-box'>
                <h1>Estatísticas da gladCode</h1>
                <div id='search-container'>
                    <div id='date-container'>
                        <span>Pesquisar entre</span><input type='text' id='date-str' class='input' placeholder='último mês'><span>e</span><input type='text' id='date-end' class='input' placeholder='hoje'>
                    </div>
                    <div id='mmr-container'>
                        <span>Intervalo de renome</span><div id='mmr-slider'></div>
                    </div>
                </div>
                <h2>Habilidades e ataques</h2>
                <table class='table' id='t-hab'>
                    <thead>
                        <tr>
                            <th>Habilidade</th>
                            <th><span>Utilizado</span><i class='info fas fa-question-circle' title='Percentual de batalhas que a habilidade foi utilizada'></i></th>
                            <th><span>Média</span><i class='info fas fa-question-circle' title='Média utilizações nas batalhes que a habilidade foi presente'></i></th>
                            <th><span>Vitórias</span><i class='info fas fa-question-circle' title='Percentual de vezes que o gladiador que utilizou a habilidade venceu'></i></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

                <h2>Gladiadores</h2>
                <table class='table' id='t-glad'>
                    <thead>
                        <tr>
                            <th></th>
                            <th>Média<i class='info fas fa-question-circle' title='Média de todos gladiadores das batalhas'></i></th>
                            <th>Vencedor<i class='info fas fa-question-circle' title='O gladiador vencedor de cada batalha'></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr data-info='STR'>
                            <td class='fixed'>Força predominante<i class='info fas fa-question-circle' title='Percentual de gladiadores que possuem a força como seu atributo predominante'></i></td>
                        </tr>
                        <tr data-info='AGI'>
                            <td class='fixed'>Agilidade predominante<i class='info fas fa-question-circle' title='Percentual de gladiadores que possuem a agilidade como seu atributo predominante'></i></td>
                        </tr>
                        <tr data-info='INT'>
                            <td class='fixed'>Inteligência predominante<i class='info fas fa-question-circle' title='Percentual de gladiadores que possuem a inteligência como seu atributo predominante'></i></td>
                        </tr>
                        <tr data-info='lvl'>
                            <td class='fixed'>Nível máximo<i class='info fas fa-question-circle' title='Maior nível atingido pelo gladiadores vivos nos últimos 5 segundos da batalha'></i></td>
                        </tr>
                    </tbody>
                </table>

                <h2>Poções</h2>
                <table class='table' id='t-pot'>
                    <thead>
                        <tr>
                            <th><i class='info fas fa-question-circle' title='Passe o mouse em uma célula para obter informações'></i></th>
                            <th title='Poção nível 1'>I</th>
                            <th title='Poção nível 2'>II</th>
                            <th title='Poção nível 3'>III</th>
                            <th title='Poção nível 4'>IV</th>
                            <th title='Poção nível 5'>V</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

                <h2>Batalhas</h2>
                <div id='single-stats'>
                    <div class='card'>
                        <div class='title'>Tempo médio das batalhas</div>
                        <div class='value' id='avg-time'></div>
                    </div>
                    <div class='card'>
                        <i id='low-battles' class='hidden fas fa-question-circle'></i>
                        <div class='title'>Número de batalhas encontradas</div>
                        <div class='value' id='nbattles'></div>
                    </div>
                </div>
            </div>
            <div id='footer'></div>
        </div>
    </div>
</body>
</html>