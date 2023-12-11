<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8' />
    <BASE href="../">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/gif" href="icon/gladcode_icon.png" />
    <title>gladCode</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto|Source+Code+Pro&display=swap" rel="stylesheet">
    <link type='text/css' rel='stylesheet' href='https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css'/> 
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.17.1/themes/prism-coy.min.css" rel="stylesheet" type="text/css"/>

    <link rel='stylesheet' href="css/table.css"/>
    <link rel='stylesheet' href="css/docs.css"/>
    <link rel='stylesheet' href="css/side-menu.css"/>
    <link rel='stylesheet' href="css/function.css"/>
    <link rel='stylesheet' href="css/header.css"/>
    
    <script src='https://code.jquery.com/jquery-3.4.1.min.js'></script>
    <script src='https://code.jquery.com/ui/1.12.1/jquery-ui.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.5/latest.js?config=TeX-MML-AM_CHTML' async></script>
    <script src="https://kit.fontawesome.com/c1a16f97ec.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.17.1/prism.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.17.1/plugins/autoloader/prism-autoloader.min.js"></script>
    <script>Prism.plugins.autoloader.languages_path = 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.17.1/components/'</script>
    <script src="https://cdn.jsdelivr.net/npm/blockly@3.20200123.1/blockly.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/blockly@3.20200123.1/msg/pt-br.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/blockly@3.20200123.1/python.js"></script>

    <!-- <script src="script/fallback/blockly.min.js"></script>
    <script src="script/fallback/blockly-pt-br.js"></script>
    <script src="script/fallback/blockly-python.js"></script> -->
    
    <script src="script/blocks.js"></script>
    <script src="script/function.js"></script>
    <script src="script/side-menu.js"></script>
    <script src="script/googlelogin.js"></script>
    <script src="script/socket.js"></script>
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
            <div id='content'>			
                <div id='language'>
                    <label>Linguagem: </label>
                    <select>
                        <option value='c'>C</option>
                        <option value='python'>Python</option>
                        <option value='blocks'>Blocos</option>
                    </select>
                </div>

                <div id='template'>
                    <h2 id='temp-name'></h2>
                    <pre><code class="language-c" id='temp-syntax'></code></pre>
                    <p id='temp-description'></p>
                                
                    <h3>Parâmetros</h3>
                    <div id='temp-param'></div>
                    
                    <h3>Retorno</h3>
                    <p id='temp-return'></p>
                    
                    <h3>Exemplo</h3>
                    <pre><code class="language-c" id='temp-sample'></code></pre>
                    <p id='temp-explain'></p>
                    
                    <h3>Veja também</h3>
                    <table class='table t-funcs'>
                        <tbody id='temp-seealso'>
                        </tbody>
                    </table>
                </div>
            </div>
            <div id='footer'></div>
        </div>
    </div>
    <?php
        $func = "";
        if (isset($_GET['f']))
            echo "<input type='hidden' id='vget' value='". $_GET['f'] ."'>";
        if (isset($_GET['l']))
            echo "<div id='dict' hidden>". $_GET['l'] ."</div>";
        if (isset($_GET['p']))
            echo "<div id='get-lang' hidden>". $_GET['p'] ."</div>";
        
    ?>
</body>
</html>