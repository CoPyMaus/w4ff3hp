<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Raumwaffennetzwerk</title>
    <link rel="stylesheet" type="text/css" href="styles/w4ff3/style.css">
    <link rel="stylesheet" type="text/css" href="styles/w4ff3/topnav.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="styles/w4ff3/js/function.js"></script>
</head>
<body onLoad="set_screen()">
<div id="ajax_cache" style="display: none;"></div>
<div id="outercontent" name="outercontent">
    <div id="innercontent" name="innercontent" class="container">
        <div id="header" name="header" onclick="get_startsite()"></div>
        <div id="topmenu-container">
        	<div id="topmenu">
                <ul>
                  <li class="dropdown">
                    <a href="javascript:void(0)" class="dropbtn">Benutzer: {username}</a>
                    <div class="dropdown-content">
                      <a href="#">Passwort ändern</a>
                      <a href="#">Benutzerprofil</a>
                      <a href="#">Abmelden</a>
                    </div>
                  </li>
                  <li><a href="#news">Nachrichten</a></li>
                  <li><a href="#forum">Forum</a></li>
                </ul>
        	</div>
        </div>
        <!--<div id="blankline" class="blankline"></div>-->
        <div id="wrapper">
            <div id="wrappernavigation">
                <div class="boxheader"></div>
                <div id="sitebox_left">
                <!-- Hier ist nichts zu sehen -->
                </div>
            </div>
            <div id="wrappercontent" class="container">
                <div class="boxheader"></div>
                <div id="contentbox">
