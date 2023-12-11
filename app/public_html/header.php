<?php
    include_once "connection.php";
    if (session_status() == PHP_SESSION_NONE)
        session_start();
?>
<html>
<head>
    <meta name="google-signin-client_id" content="1036458629781-8j247asma3gm7u956gbn3d0m0nobqhie.apps.googleusercontent.com">
    <script src="https://cdn.socket.io/socket.io-2.3.1.js" ></script>
    <script src="https://apis.google.com/js/platform.js?onload=onLoadCallback" async defer></script>
</head>
<body>
<div id='header-container'>	
    <div id='header'>
        <div id='menu-button'></div>
        <div id='logo'><a href='index'><img src='icon/logo.png'></a></div>
        <i id='search' class='fas fa-search'></i>
        <div id='h-items'>
            <div class='item drop-menu mobile'>
                <div class='title'>LOGIN</div>
                <div class='item-container'>
                    <div class='item'><a id='login' title='{{header_tooltip_login}}' class='hidden'>{{header_enter}}</a><a id='profile' href='news' title='{{header_tooltip_profile}}' class='hidden'><span>{{profile}}</span></a></div>
                </div>
            </div>
            <div class='item desktop' id='header-profile'><a id='login' title='{{header_tooltip_login}}' class='hidden'>LOGIN</a><a id='profile' href='news' title='{{header_tooltip_profile}}' class='hidden'><span>{{profile}}</span></a></div>
            <div class='item' id='learn'><a href='manual' title='{{header_tooltip_learn}}'><span>{{learn}}</span></a></div>
            <div class='item' id='header-editor'><a href='editor' title='{{header_tooltip_editor}}'>EDITOR</a></div>
            <div class='item drop-menu'>
                <div class='title'><span>{{community}}</span></div>
                <div class='item-container'>
                    <div class='item'><a href='https://www.reddit.com/r/gladcode/' target='_blank'><span>{{header_community_reddit}}</span></a></div>
                    <div class='item'><a href='https://www.facebook.com/gladcode/' target='_blank'><span>{{header_community_facebook}}</span></a></div>
                    <div class='item'><a href='https://forms.gle/BDbSmcLpPgwLe4Uc7' target='_blank'><span>{{header_community_whatsapp}}</span></a></div>
                </div>
            </div>
            <div class='item drop-menu' id='about'>
                <div class='title'><span>{{about}}</span></div>
                <div class='item-container'>
                    <div class='item'><a href='about' title='{{header_tooltip_about}}'><span>{{header_about_project}}</span></a></div>
                    <div class='item'><a href='about#support' title='{{header_tooltip_support}}'><span>{{header_about_support}}</span></a></div>
                    <div class='item'><a href='creditos' title='{{header_tooltip_credits}}'><span>{{header_about_credits}}</span></a></div>
                    <div class='item'><a href='stats' title='{{header_tooltip_stats}}'><span>{{header_about_stats}}</span></a></div>
                </div>
            </div>
            <div class='item drop-menu'>
                <div class='title'><span>{{projects}}</span></div>
                <div class='item-container'>
                    <div class='item'><a href='code' title='{{header_tooltip_compiler}}'><span>{{header_projects_compiler}}</span></a></div>
                    <div class='item'><a href='https://github.com/werlang/gladcode' title='{{header_tooltip_github_gladcode}}' target='_blank'>GLADCODE V1</a></div>
                    <div class='item'><a href='https://github.com/werlang/automin' title='{{header_tooltip_automin}}' target='_blank'>AUTOMIN</a></div>
                </div>
            </div>
            <!-- <div class='item drop-menu'>
                <div id='language' class='title'>PORTUGUÊS</div>
                <div class='item-container'>
                    <div id='english' class='item'><a>ENGLISH</a></div>
                </div>
            </div> -->
        </div>
    </div>
</div>
</body>
</html>