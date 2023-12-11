<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8' />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/gif" href="icon/gladcode_icon.png" />
    <title>gladCode - Sobre</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel='stylesheet' href="css/about.css"/>
    <link rel='stylesheet' href="css/radio.css"/>
    <link rel='stylesheet' href="css/dialog.css"/>
    <link rel='stylesheet' href="css/header.css"/>
    
    <link href="https://fonts.googleapis.com/css?family=Roboto|Source+Code+Pro&display=swap" rel="stylesheet">
    <script src='https://code.jquery.com/jquery-3.4.1.min.js'></script>
    <script src='https://code.jquery.com/ui/1.12.1/jquery-ui.min.js'></script>	
    <script src="script/about.js"></script>
    <script src="script/radio.js"></script>
    <script src="script/dialog.js"></script>
    <script src="script/googlelogin.js"></script>
    <script src="script/socket.js"></script>
    <script src="script/header.js"></script>
    
    </head>
<body>
    <?php include("header.php"); ?>
    <div id='frame'>
        <div id='mission-container'><div id='mission'>
            <div id='line1'>Programar pode ser divertido</div>
            <div id='line2'>É nisso que a gladCode acredita. Trazendo o ambiente de programação para uma temática interessante como dos jogos, em que o usuário veja graficamente o resultado de seus esforços através das ações de seu avatar, a gladCode busca incitar iniciantes e experts em programação para competirem e melhorarem suas habilidades. Diversão e aprendizado é o nosso objetivo.</div>
        </div></div>
        <div id='author'>
            <div class='row title'>Pablo Werlang</div>
            <div class='row subtitle'>Idealizador e criador da gladCode</div>
            <div class='row'>
                <div class='col' id='myself'>
                    <img class='image' src='image/pablo2.jpg'>
                </div>
                <div class='col' id='info'>
                    <div class='feats'>
                        <img src='icon/run_code.png'><a href='http://www.ecomp.c3.furg.br/' target='_blank'>Engenheiro de Computação</a>
                    </div>
                    <div class='feats'>
                        <img src='icon/teacher.png'><a href='http://www.ifsul.edu.br/' target='_blank'>Professor do IFSUL</a>
                    </div>
                    <div class='feats'>
                        <img src='icon/gamepad.png'><a>Apaixonado por jogos e programação</a>
                    </div>
                </div>
            </div>
            <div class='row' id='social'>
                <a href='mailto:pswerlang@gmail.com'><img src='icon/gmail.png'><span>pswerlang@gmail.com</span></a>
                <a href='https://facebook.com/pswerlang' target='_blank'><img src='icon/facebook_blue.png'><span>facebook.com/pswerlang</span></a>
                <a href='https://github.com/werlang' target='_blank'><img src='icon/github.png'><span>github.com/werlang</span></a>
            </div>
        </div>
        <div id='timeline'>
            <div id='background'></div>
            <div class='title'>História</div>
            <div class='row'>
                <span class='text'>Concepção da ideia da gladCode durante o evento <a href='http://charcode.tk' target='_blank'>Charcode</a>, como uma versão customizada da simulação <a href='https://robocode.sourceforge.io/' target='_blank'>Robocode</a>. A ideia do projeto era utilizam a linguagem C e criar um sistema de personagens que lembrasse um rpg.</span>
                <img src='icon/swords.png'>
                <span class='time'>Out 2016</span>
            </div>
            <div class='row'>
                <span class='text'>Lançamento da primeira versão da gladCode como modalidadede de competição na Charcode. O projeto rodava localmente na máquina, e requeria a cópia dos códigos, imagens e informações das equipes. Disponível no <a href='https://github.com/werlang/gladCode/' target='_blank'>GitHub</a>.</span>
                <img src='icon/swords.png'>
                <span class='time'>Nov 2017</span>
            </div>
            <div class='row'>
                <span class='text'>Versão Alpha da gladCode 2 é lançada, permitindo a execução das simulações no servidor e a criação da aparência dos gladiadores pelo site.</span>
                <img src='icon/swords.png'>
                <span class='time'>Mar 2018</span>
            </div>
            <div class='row'>
                <span class='text'>O sistema da gladCode 2 foi usado no evento Charcode usando a interface hoje chamada de modo <span title='Modo descontinuado' class='discontinued'>Torneio clássico</span>.</span>
                <img src='icon/swords.png'>
                <span class='time'>Out 2018</span>
            </div>
            <div class='row'>
                <span class='text'>Lançamento da <a href='https://www.reddit.com/r/gladcode/comments/auttt3/atualiza%C3%A7%C3%A3o_gladcode_251beta/' target='_blank'>versão Beta</a>, que proporciona a criação e teste de gladiadores de maneira integrada através do editor, além de perfis de usuários, batalhas contra outros usuários e ranking.</span>
                <img src='icon/swords.png'>
                <span class='time'>Jan 2019</span>
            </div>
            <div class='row'>
                <span class='text'>Inauguração do novo <a href='https://www.reddit.com/r/gladcode/comments/d5h8qv/atualiza%C3%A7%C3%A3o_gladcode_26beta/' target='_blank'>Modo torneio</a> na CharCode 2019, além de mudanças expressivas na aparência da arena.</span>
                <img src='icon/swords.png'>
                <span class='time'>Dez 2019</span>
            </div>
            <div class='row'>
                <span class='text'>Suporte à linguagem de programação Python e programação em blocos.</span>
                <img src='icon/swords.png'>
                <span class='time'>Mar 2020</span>
            </div>
            <div class='row future'>
                <span class='text'>Modo lendário, onde gladiadores mantém seus níveis e a morte é definitiva.</span>
                <img src='icon/swords.png'>
                <span class='time'>2020</span>
            </div>
            <div class='row future'>
                <span class='text'>Lançamento de novas habilidades.</span>
                <img src='icon/swords.png'>
                <span class='time'>2020</span>
            </div>
            <div class='row future'>
                <span class='text'>Implementação de itens para uso dos gladiadores durante as batalhas.</span>
                <img src='icon/swords.png'>
                <span class='time'>2020</span>
            </div>
            <div class='row future'>
                <span class='text'>Loja de skins e mercado de gladiadores da gladCode.</span>
                <img src='icon/swords.png'>
                <span class='time'>2021</span>
            </div>
            <div class='row future'>
                <span class='text'>Árvore de habilidades e achievements dos mestres.</span>
                <img src='icon/swords.png'>
                <span class='time'>2021</span>
            </div>
            <div class='row future'>
                <span class='text'>Versão multi idiomas da gladCode. Tradução para o inglês e templates para outros idomas.</span>
                <img src='icon/swords.png'>
                <span class='time'>2022</span>
            </div>
        </div>
        <div id='know'>
            <div class='info'>
                <div class='title'>Conheça mais</div>
                <div class='text'>Na gladCode você cria gladiadores, configurando sua aparência e comportamento. Eles possuem atributos físicos e mentais que determinam o quão fortes eles são correndo, usando ataques ou lançando magias. Para configurar o comportamento de seus gladiadores você precisa usar seus conhecimentos de lógica de programação. Uma vez criados, seus gladiadores podem ser colocados para participar de disputas contra outros gladiadores, onde só os melhores conseguem chegar ao topo do ranking.</div>
                <div class='button-container'>
                    <a href='manual' class='button'>Visite o Manual</a>
                    <a href='docs' class='button'>Veja as funções</a>
                </div>
            </div>
            <iframe frameborder="0" width="100%" height="100%" src="https://www.youtube.com/embed/lUR3CszStUg?autoplay=1&controls=0&showinfo=0&autohide=1&modestbranding=1&loop=1&playlist=lUR3CszStUg&mute=1"></iframe>
        </div>
        <div id='plans'>
            <div id='img-rank'><img src='image/personal-ranking.png'></div>
            <div class='col'>
                <div class='title'>Planos para escolas</div>
                <div class='text'>
                    <p>Caso você seja afiliado a alguma instituição de ensino, ou até mesmo preste tutoria para alguns alunos, introduza a gladCode em suas aulas e veja o engajamento crescer.</p>
                    <p>Experimente a funcionalidade exclusiva do plano para escolas, o <a href='battle' target='_blank'>treino de equipes</a>.</p>
                    <p>Esta modalidade permite a criação de rodadas de combates entre seus alunos com tempo definido e número máximo de participantes por combate, além de um ranking específico da sua escola que demonstra o desempenho dos alunos ao longo de todos treino realizados.</p>
                </div>
                <div id='button-container'>
                    <div id='premium-frame'>
                        <span class='s4'>Somente</span>
                        <div><span class='s2'>R$</span><span class='s1'>0,99</span><span class='s4'>/ mês</span></div>
                        <span class='s3'>por aluno</span>
                        <span class='s4'>Ganhe <b>R$ 30,00</b> de créditos para testar as funcionalidades</span>
                    </div>
                    <button id='premium' class='button'>TENHO INTERESSE</button>
                </div>
            </div>
        </div>
        <div id='support'>
            <div class='col'>
                <div class='title'>Apoie o projeto</div>
                <div class='text'>
                    <p>Para participar da gladCode você não precisa pagar nada, mas ainda assim existem gastos com o servidor que hospeda o sistema, além do grande investimento de tempo que existe para desenvolvimento de novas funcionalidades e correção de bugs.</p>
                    <p>Por isso, caso você goste da gladCode, reconhece o trabalho realizado e gostaria de apoiar o projeto, pode fazer uma doação.</p>
                    <div id='icons'>
                        <img src='icon/server.png'>
                        <img src='icon/time.png'>
                        <img src='icon/money.png'>
                    </div>
                </div>
                <div class='payment'>
                    <div class='question'>
                        <span>Que tipo de doação você deseja fazer?</span>
                        <label><input type="radio" name="pay-time" id="one-time" class='radio'>Uma única vez</label>
                        <label><input type="radio" name="pay-time" id="monthly" class='radio'>Recorrente mensal</label>
                    </div>

                    <div class='question' id='method'>
                        <span>Qual método de pagamento deseja usar?</span>
                        <label><input type="radio" class='radio' name="pay-method" id="card">Cartão de crédito/débito</label>
                        <label><input type="radio" class='radio' name="pay-method" id="boleto">Boleto</label>
                        <label><input type="radio" class='radio' name="pay-method" id="crypto">Criptomoedas</label>
                    </div>					
                    
                    <div id='buttons'>
                        <a id='pagseguro' class='donate hidden big' href='https://pag.ae/7UGrqxqw3' target='_blank' title='Pague com boleto, cartão ou transferência bancária'>
                            <span>Doar com</span>
                            <div class='inner-box'><img src='icon/pagseguro.png'></div>
                        </a>
                        <a id='paypal' class='donate hidden big' href='https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=5C6SCZTEEEGT2&source=url' target='_blank' title='Doe uma única vez ou mensalmente com cartão de crédito ou débito'>
                            <span>Doar com</span>
                            <div class='inner-box'><img src='icon/paypal.png'></div>
                        </a>
                        <div id='bitcoin' class='donate small hidden big' title=''>
                            <span>Doar Bitcoin</span>
                            <div class='inner-box left'><img src='icon/bitcoin.png'></div>
                        </div>
                        <div id='ethereum' class='donate small hidden big' title=''>
                            <span>Doar Ethereum</span>
                            <div class='inner-box left'><img src='icon/ethereum.png'></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id='contact'>
            <div class='title'>Entre em contato</div>
            <div class='subtitle'>Adoraríamos saber o que você pensa sobre o projeto</div>
            <div id='social'>
                <a href='mailto:pswerlang@gmail.com' title='Enviar Email' target='_blank'><img src='icon/at-sign.png'></a>
                <a href='https://facebook.com/gladcode' title='Página do Facebook' target='_blank'><img src='icon/facebook.png'></a>
                <a href='https://forms.gle/BDbSmcLpPgwLe4Uc7' title='Grupo do WhatsApp' target='_blank'><img src='icon/whatsapp_full.png'></a>
                <a href='https://www.reddit.com/r/gladcode' title='Comunidade do Reddit' target='_blank'><img src='icon/reddit.png'></a>
            </div>
        </div>
        <div id='about-footer'>Copyright © 2018-2020 Pablo Werlang</div>
            <div id='long' hidden>
                Ainda durante a faculdade um professor apresentou para a turma um programa traria a inspiração para a gladCode anos mais tarde. Este programa possuía uma linguagem própria, e nele, nós alunos deveríamos programar a inteligência de um robô virtual, que deveria batalhar contra os robôs dos outros alunos em um mini torneio realizado após a entrega do trabalho da disciplina.
                Os anos se passaram. Hoje sou professor do <a href='ifsul.edu.br'>Instituto Federal Sul-Riograndense (IFSul)</a> - Campus Charqueadas e ministro principalmente disciplinas de lógica de programação e linguagem de programação C.

                No campus Charqueadas existe um evento anual chamado <a href='charcode.tk'>CharCode</a>. Neste evento existem hoje quatro modalidades: Hackathon, Maratona de programação, <a href='https://robocode.sourceforge.io/'>Robocode</a> e a gladCode. Durante a CharCode 2016 eu e outros professores estávamos conversando sobre a Robocode, quando surgiu a ideia de desenvolver um software semelhante, com a diferença de usar a linguagem C, ao invés de Java, pois traria um engajamento maior dos alunos novatos do campus (pois C é ensinado desde o início do curso), e principalmente usando uma temática dos RPGs antigos ambientados em fantasia medieval.
                Resolvi então tocar adiante o projeto que nomeei gladCode (pois se trataria de uma arena de gladiadores), e em 2017 foi incluído na CharCode a nova modalidade: a gladCode. Este meu novo projeto tinha por objetivo auxiliar alunos no processo de aprendizado de conceitos de lógica ao mesmo tempo que permitia que eles tivessem contato com a linguagem C em um ambiente gráfico, fugindo da tela preta do terminal, enquanto se divertiam em uma competição ambientada em uma temática de jogos estilo RPG.

                A primeira versão da gladCode rodava localmente e funcionava no Windows. O repositório <a href='https://github.com/werlang/gladCode'>gladCode</a> contém a primeira versão do projeto, para quem quiser entender como funciona.

                Na gladCode todos códigos criados pelo usuário precisavam incluir o arquivo-fonte da simulação. Este arquivo continha a função principal (main) e iniciava uma thread responsável por controlar o gladiador do usuário. Além de um processo para cada usuário, um outro processo responsável por gerenciar a simulação e intermediador os dados recebidos pelo processo de cada gladiador também era iniciada. A simulação era então executada, e após isso um log era gerado em um arquivo contendo o que aconteceu em cada instante de tempo da simulação. Este log era o histórico da luta, descrito em texto. Um render das lutas, responsável por mostrar na tela as animações referentes ao log da criado pela simulação, foi desenvolvido em Java pelo meu colega Maurício Escobar. Além disso uma interface gráfica também foi desenvolvida por mim em C para permitir que as equipes da competição selecionassem seus arquivos com os códigos-fonte e as imagens dos gladiadores. As imagens dos gladiadores eram extraídas do <a href='http://gaurav.munjal.us/Universal-LPC-Spritesheet-Character-Generator/'>gerador de folha de sprites</a> do <a href='http://lpc.opengameart.org/'>projeto LPC</a>, que diga-se de passagem é um maravilhoso projeto artístico que permite aos novos desenvolvedores de games criarem personagens altamente personalizáveis.

                Embora um enorme desafio desafio do ponto de vista técnico, a gladCode foi um sucesso. Mas o projeto, muito promissor e elogiado por alunos e professores não podia para por aí. A gladCode era um programa que exigia um certo trabalho para rodar, e isso muitas vezes era um impecilho na hora de um aluno iniciante dar os primeiros passos. Então 2018 começou com o desafio de transportar a gladCode para um sistema web, permitindo que o usuário execute e visualize a simulação no navegador, sem precisar instalar nada.

                Como eu precisava que o servidor compilasse e executasse os programas, eu precisava de acesso root ao servidor, por isso contratei um <a href='https://www.hostinger.com.br/tutoriais/o-que-e-vps-como-escolher-um-servidor-vps'>VPS</a> para hospedar o projeto. Depois da tarefa de configuração do servidor (que eu nunca tinha tido contato até então) percebi que eu precisaria executar o código do usuário em um ambiente seguro e isolado, então fui apresentado ao <a href='https://www.docker.com/'>Docker</a> e seus containers, e desta empreitada surgiu o <a href='code'>compilador C</a> da gladCode.

                Após esta etapa precisei fazer o port de todo o código da gladCode para linux para executar no servidor, então aproveitei o momento para modificar a estrutura do programa. A partir de então a comunicação entre os processos passou a ser feita por troca de mensagens em um servidor <a href='https://blog.pantuza.com/artigos/o-que-sao-e-como-funcionam-os-sockets'>socket</a> ao invés de por <a href='https://pt.wikipedia.org/wiki/Mem%C3%B3ria_compartilhada'>memória compartilhada</a>. Esta mudança permitiu criar uma <a href='https://pt.wikipedia.org/wiki/Interface_de_programa%C3%A7%C3%A3o_de_aplica%C3%A7%C3%B5es'>API</a> onde no futuro se tornaria muito mais simples adicionar suporte à outras linguagens de programação além do C. Troquei também o padrão de comunicação do log da simulação por <a href='https://www.json.org/json-pt.html'>JSON</a>, visto que a comunicação entre o servidor e o usuário seria feita pelo navegador em linguagem javascript. Assim nasceu a segunda versão da gladCode.

                Como a renderização da simulação agora seria feita no navegador, utilizando a <a href='https://phaser.io/'>framework Phaser</a> programei o novo render da gladCode.
                Os passos seguintes foram a criação das páginas de documentação do projeto e o editor de aparência dos gladiadores, que permitia ao usuário de maneira intuitiva escolher cada peça de equipamento que comporia seu novo gladiador, e gravá-lo no banco da dados do servidor.
                
                Na Charcode 2018 a modalidade gladCode utilizou a hoje chamada interface de <a href='#' title='Modo descontinuado'>torneio clássico</a> para realizar a competição, que permitiu a geração dos logs das batalhas para visualização posterior.

                Os próximos passos da gladCode seriam torná-la um sistema multiplayer online, onde os usuários criariam seu perfil, salvariam seus códigos e seus gladiadores no servidor, e os colocariam para batalhar contra os gladiadores de outros usuários em um sistema de ranking online da plataforma. Desta forma nasceu o <a href='editor'>editor de gladiadores</a>, que une o editor da aparência do gladiador com o editor de texto do compilador C da gladCode. A página de perfil do usuário foi criada permitindo ao usuário comunicar-se com seus amigos, visualizar seu ranking, e inscrever seus gladiadores em batalhas.
            </div>
        </div>
    </div>
</body>
</html>