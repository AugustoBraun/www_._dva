<?php

session_start();
require_once($_SERVER["DOCUMENT_ROOT"]."/config.php");
$_SESSION['adminUser'] = array();

?><!DOCTYPE html>
<html lang="pt">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
		<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
		<META name="ROBOTS" CONTENT="NOINDEX,NOFOLLOW">
		<META NAME="ROBOTS" CONTENT="NONE">

		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
		<meta name="apple-mobile-web-app-capable" content="yes" />
        <title>Sistema Administrativo Sesafio Wide Pay</title>

		<link rel="stylesheet" href="/sistema/css/sistema.css">

		<script type="text/javascript" src="/js/jquery.min.js"></script>
        <script src='https://www.google.com/recaptcha/api.js'></script>

        <script>

            $(document).ready(function(){

                $('.login-error-close').click(function(){
                    $('.login-error').fadeOut();
                });

            });

        </script>
</head>

<body>


    <div id="login-content">

        <?php

            if(!empty($error))
            {
                echo '<div class="login-error">
                        <div class="login-error-close">X</div>';
                echo implode('<br>',$error);
                echo '</div>';

            }


        ?>

            <div id="login-card">

                <div id="face1">
                    <img src="/sistema/img/logo.png" style="width: 117px"><br>

                </div>

                <div id="face2">

                    <div style="clear:both"></div>

                    <div id="login-form">

                        <form name="loginform" action="/sistema/" method="POST" >

                            <?php

                                require_once('recaptchalib.php');
                                $publickey = $recaptcha_public_key;

                            ?>
                            <input type="text" name="usuario" placeholder="login" class="login-form-input" value="usuario">
                            <input type="password" name="senha" placeholder="******"  class="login-form-input" value="senha">

                            <div class="g-recaptcha" data-sitekey="<?php echo $recaptcha_public_key; ?>" style="width: 20vw !important; height; 6vw !important;"></div>

                            <input type="submit"  name="access" id="access" class="botao1" value="acessar" style="width: 100%; margin: 20px 0; padding: 10px;"/>
                        </form>

                    </div>

                </div>

            </div>


    </div>


</html>
