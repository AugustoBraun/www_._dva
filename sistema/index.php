<?php

	session_start();

	require_once($_SERVER["DOCUMENT_ROOT"]."/config.php");
    require_once($_SERVER["DOCUMENT_ROOT"]."/sistema/auth.php");


// ===================  sistemas ==============================================================================


    function urlAmigavel($url = null){

        if(!$url){ $url =  $_SERVER['REQUEST_URI']; }
        $url = explode('/', parse_url($url, PHP_URL_PATH));
        $caminho = array();

        foreach($url as $path){
            if($path != '') {
                $caminho[] = $path;
            }
        }

        // organizo os caminhos do sistema
        if(isset($caminho[1])){
            $sistema['controlador'] = $caminho[1];
        }else{
            $sistema['controlador'] = 'users';
        }
        if(isset($caminho[2])){
            $sistema['acao'] = 'admin_'.$caminho[2];
        }else{
            $sistema['acao'] = 'admin_list';
        }
        for($i=3, $o=0; $i<count($caminho); $i++, $o++){
            $sistema['params'][$o] = $caminho[$i];
        }
        return($sistema);
    }

    $sistema = urlAmigavel();



    //funcoes essenciais ao sistema
    function lerTemplate($opcao1= null, $opcao2=null)
    {
        global $sistema;

        if(empty($opcao1)){
            $opcao1 = $sistema['controlador'];
        }
        if(empty($opcao2)){
            $opcao2 = $sistema['acao'];
        }

        $opcao2 = str_replace('admin_', '', $opcao2);
        $ferramenta = ADMINROOT.'/'.$opcao1.'/'.$opcao2.'.php';
        return($ferramenta);
    }
			
//================================================
		

    include_once($_SERVER["DOCUMENT_ROOT"].'/sistema/_modelo_mestre.php');
    include_once($_SERVER["DOCUMENT_ROOT"].'/sistema/_controlador_mestre.php');

		
//================================================

    require_once($rootdir.'sistema/'.$sistema['controlador'].'/controlador.php');

    if(!method_exists($sistema['controlador'], $sistema['acao']))
    {
        echo 'Error controller / action';
    }else{
        $controlador = $sistema['controlador'];
        $dados_template = new $controlador;
    }


    //define o conteudo atraves dos comandos do controlador
    $conteudo = $dados_template->{$sistema['acao']}($sistema['params'], $_GET, $_POST, $_FILES);


    //lança pro ambiente as variaveis da classe controlador
    $vars = get_class_vars(get_class($dados_template));
     foreach($vars as $nome => $valor){
         $$nome = $valor;
     }
     foreach($dados_template->variaveis as $nome => $valor){
         $$nome = $valor;
     }




//====== mensagens ==========================================
			 
    if(is_array($_SESSION['msg_erro'])){$_SESSION['msg_erro'] = implode(' / ', $_SESSION['msg_erro']);}
    if(is_array($_SESSION['msg_sucesso'])){$_SESSION['msg_sucesso'] = implode(' / ', $_SESSION['msg_sucesso']);}
    if(is_array($_SESSION['msg_aviso'])){$_SESSION['msg_aviso'] = implode(' / ', $_SESSION['msg_aviso']);}

    function mostraAviso($msg, $nomediv='msg_sucesso'){
            return '<div class="'.$nomediv.'" style="display:none; position: relative; top: 20px; margin-bottom: 30px;">'.$msg.'<a class="close" href="javascript:fechaMensagens(\''.$nomediv.'\')"></a></div>';
    }

?><!DOCTYPE html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">

		<link rel="stylesheet" href="/sistema/css/sistema.css">
		<link rel="stylesheet" href="/css/ui-darkness/jquery-ui-1.8.16.custom.css">
		<link href="/css/skins/grey.css" rel="stylesheet" type="text/css" />
		<link href="/css/jquery_notification.css" type="text/css" rel="stylesheet"/>
        <link href="/css/tablesorter.css" type="text/css" rel="stylesheet" media="print, projection, screen"/>


        <script type="text/javascript" src="/js/jquery.js"></script>
        <script type="text/javascript" src="/js/jquery_notification.js"></script>
        <script type="text/javascript" src="/js/jquery.tablesorter.min.js"></script>
	    <script language="javascript" type="text/javascript">

            var SITEURL = '<?php echo SITEURL; ?>';

			$(function(){

                <?php

                    $qtdshow = 0;

                    if($_SESSION['msg_sucesso'])
                    {
                        echo 'showNotification({
                                    type : "success",
                                    message: "<table border=0><tr><td width=1%><img src=/sistema/img/icones/48_sucesso.png hspace=2></td><td width=99%>'.str_replace('<br>','. | ', htmlentities(strip_tags($_SESSION['msg_sucesso'])).'</td></tr></table>').'",
                                    autoClose: true,
                                    duration: 8
                                });';
                        $qtdshow++;
                    }

                    if($_SESSION['msg_erro'])
                    {
                        if($qtdshow>0){echo 'setTimeout( function(){';}
                        echo 'showNotification({
                                    type : "error",
                                    message: "<table border=0><tr><td width=1%><img src=/sistema/img/icones/48_erro2.png align=absmiddle hspace=2></td><td width=99%>'.str_replace('<br>','. | ', htmlentities(strip_tags($_SESSION['msg_erro'])).'</td></tr></table>').'",
                                    autoClose: true,
                                    duration: 8
                                });';
                        if($qtdshow>0){echo'}, 9000);';}
                        $qtdshow++;
                    }

                    if($_SESSION['msg_aviso'])
                    {
                        if($qtdshow>0){echo 'setTimeout( function(){';}
                        echo 'showNotification({
                                    type : "warning",
                                    message: "<table border=0><tr><td width=1%><img src=/sistema/img/icones/48_alerta.png align=absmiddle hspace=2></td><td width=99%>'.str_replace('<br>','. | ', htmlentities(strip_tags($_SESSION['msg_aviso'])).'</td></tr></table>').'",
                                    autoClose: true,
                                    duration: 8
                                });';
                        if($qtdshow==1){echo'}, 9000);';}
                        if($qtdshow==2){echo'}, 18000);';}
                        $qtdshow++;

                    }

                ?>


                $("#tabela_editar")
                    .tablesorter({
                        widgets: ['zebra','indexFirstColumn']
                    });


            });


			function fechaMensagens(div)
            {
                $('.'+div).hide();
			}

			function abreMensagens(div){	
				if ($('.'+div).is(':visible')) {
					$('.'+div).fadeTo("fast", 0.00, function(){ //fade
						$(this).hide('blind', '1000');
					});
				}else{
					$('.'+div).fadeTo("slow", 1.00, function(){ //fade
						$(this).slideDown('slow');
					});
				}
			}


			function confirmaAcao(form, texto, link){
				function confirma(v,m,f){
					if(form != ''){
						if(v == true){ envia(form); }else{ return false; }
					}else{
						if(v != true){ return false; }else{document.location.href = link}
					}
				}
				function envia(form){
					document.getElementById(form).submit();
				}
				$.prompt(texto,{
				    callback: confirma,
				    buttons: { Confirm: true, Cancel: false },
					prefix: 'cleanblue'
				});
			}

            function confirmaCallback(texto, callback)
            {
                function confirma(v,m,f)
                {
                    if(v == true){
                        callback();
                    }else{
                        return false;
                    }
                }
                $.prompt(texto,{
                    callback: confirma,
                    buttons: { Confirm: true, Cancel: false },
                    prefix: 'cleanblue'
                });
            }

            function showMessage(text, callback)
            {
                $.prompt(text,{
                    callback: callback,
                    buttons: { OK: false },
                    prefix: 'cleanblue'
                });
            }



        </script>
<style>

    body{
        background-color: #666;
    }

    .menu-logo{
        background-image: url(/sistema/img/logo.png);
        background-size: 117px;
        background-repeat: no-repeat;
        background-position: 50px 0px;
        width: 100%;
        height: 100px;
        filter: invert(100%);
    }

</style>
</head>
<body id="body"
    <?php

        if($_SESSION['adminUser']['msg_alert']){

            echo 'onload="javascript:alert(\''.$_SESSION['adminUser']['msg_alert'].'\');" ';
            $_SESSION['adminUser']['msg_alert'] = null;
        }
    ?>
    >

<div id="box_usuario">
    <div style="max-width:1000px; width: 100%; margin: 0 auto; text-align: center;">
        <div id="box_usuario_txt">
            <div class="box_foto" <?php
                if(is_file(ROOTDIR.'uploads/img/usuario/'.$_SESSION['userid'].'.jpg')){
                    echo ' style="background-image: url(\'/uploads/img/usuario/'.$_SESSION['adminUser']['userid'].'.jpg\'); background-repeat: no-repeat;"';
                }
            ?>>
            </div>
            <div class="box_usario_nome">
                <?php echo $_SESSION['adminUser']['usernome']; ?>
            </div>
            <table border=0 cellpadding=2 cellspacing=5">
                <tr>
                    <td>
                        <div class="box_usuario_detalhes">
                            <a href="<?php echo ADMINURL; ?>/login.php" class="tooltip" style="color: #ffffff;">
                                <img src="/sistema/img/_sair.png" border=0 align=absmiddle title="Clique para sair do Sistema">
                                sair
                            </a>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>


<div id="Content">




<table width="95%">
  <tr> 
     <td  valign="top" width="260">

         <div class="menu-logo"></div>

        <div class="wrap menu_ferramentas">

            <div class="grey demo-container">
                <ul class="accordion nobullet" id="accordion-1">

                        <li>
                            <a href="/sistema/users/list">Listar Usuários</a>
                        </li>

                </ul>
            </div>

        </div>




    </td>
    <td  valign="top" width="100%">
      <div class="sistema_conteudos"  style="min-height: 700px;"><?php
	  

		if($controlador_icone == ''){$controlador_icone = 'info';}
		if($acao_icone == ''){$acao_icone = 'configuracoes';}
		
 		echo '<div class="titulo_controlador_acao">
					<table  width=100% cellpadding=0 cellspacing=0><tr><td bgcolor=#cccccc nowrap width=10%>
						<span class="titulo_controlador"><img src="/sistema/img/ico/24/'.$controlador_icone.'.png" border=0 style="position:relative; top:0px;"> '.$controlador_nome.'</span>
					</td><td bgcolor=#cccccc width=0%>
						<img src="/sistema/img/titulo_sep.png" height=100%  align=right>
					</td><td bgcolor=#E6E6E6 nowrap width=90%>
						<span class="titulo_acao"><img src="/sistema/img/ico/24/'.$acao_icone.'.png" border=0 style="position:relative; top:4px;"> '.$conteudo['acao_nome'].'</span>
					</td></tr></table>
				</div>';  


		
		if($_SESSION['config_avisos_vis']==2 || $_SESSION['adminUser']['config_dicas_vis'] == 2)
        {
				if($_SESSION['msg_erro']){echo '<div class="msg_erro">'.$_SESSION['msg_erro'].'<a class="close" href="javascript:fechaMensagens(\'msg_erro\')"></a> </div>';}
				if($_SESSION['msg_sucesso']){echo '<div class="msg_sucesso">'.$_SESSION['msg_sucesso'].'<a class="close" href="javascript:fechaMensagens(\'msg_sucesso\')"></a> </div>';}
				if($_SESSION['msg_aviso']){echo '<div class="msg_aviso">'.$_SESSION['msg_aviso'].'<a class="close" href="javascript:fechaMensagens(\'msg_aviso\')"></a></div>';}

		}elseif($_SESSION['config_avisos_vis']==3 || $_SESSION['adminUser']['config_dicas_vis'] == 3){

				if( ($_SESSION['msg_erro']) || ($_SESSION['msg_sucesso']) || ($_SESSION['msg_aviso']) )
                {
					echo '<div class="avisos">';
					if($_SESSION['msg_erro']){		echo '<a href="javascript://" onClick="abreMensagens(\'msg_erro\');" class="tooltip" style="text-decoration: none;"><img src="/sistema/img/ico/erro.png"><span>'.$_SESSION['msg_erro'].'</span></a>';}
					if($_SESSION['msg_sucesso']){echo '<a href="javascript://" onClick="abreMensagens(\'msg_sucesso\');" class="tooltip" " style="text-decoration: none;"><img src="/sistema/img/ico/sucesso.png"><span>'.$_SESSION['msg_sucesso'].'</span></a>';}
					if($_SESSION['msg_aviso']){	echo '<a href="javascript://" onClick="abreMensagens(\'msg_aviso\');" class="tooltip" style="text-decoration: none;"><img src="/sistema/img/ico/aviso.png"><span>'.$_SESSION['msg_aviso'].'</span></a>';}
					if($_SESSION['config_dicas_vis']==2){
						if($_SESSION['msg_dica']){	echo '<a href="javascript://" onClick="abreMensagens(\'msg_dica\');" class="tooltip" style="text-decoration: none;"><img src="/sistema/img/ico/dica.png"><span>'.$_SESSION['msg_dica'].'</span></a>';}
					}
					echo '</div>';
					if($_SESSION['msg_erro']){echo mostraAviso($_SESSION['msg_erro'],'msg_erro');}
					if($_SESSION['msg_sucesso']){echo mostraAviso($_SESSION['msg_sucesso'],'msg_sucesso');}
					if($_SESSION['msg_aviso']){echo mostraAviso($_SESSION['msg_aviso'],'msg_aviso');}
					if($_SESSION['config_dicas_vis']==2){
						if($_SESSION['msg_dica']){echo mostraAviso($_SESSION['msg_dica'],'msg_dica');}
					}
				}				
		}


		if($_SESSION['config_dicas_vis']==1 || $_SESSION['adminUser']['config_dicas_vis'] == 1){

			if($_SESSION['msg_dica']){echo '<div class="msg_dica">'.$_SESSION['msg_dica'].'<a class="close" href="javascript:fechaMensagens(\'msg_dica\')"></a></div>';}
		}
		if( ($_SESSION['config_dicas_vis']==2) && ($_SESSION['config_avisos_vis'] != 3) ){
			if($_SESSION['msg_dica']){	
				echo '<div class="avisos">
						<a href="javascript://" onClick="abreMensagens(\'msg_dica\');" class="tooltip" style="text-decoration: none;"><img src="/sistema/img/ico/dica.png"><span>'.$_SESSION['msg_dica'].'</span></a>
						</div>';
				echo mostraAviso($_SESSION['msg_dica'],'msg_dica');
			}
		}		

		
		
		
		
		//caso seja enviado algum conteudo fora de template, carrega
		if($conteudo['miolo']){echo $conteudo['miolo'];}

				
		// caso o controlador solicite um template, carrega.
		if(is_array($conteudo['template'])){
            include_once(lerTemplate($conteudo['template'][0],$conteudo['template'][1]));
        }

		//apos o uso removo as mensagens da sessao;
		$dados_template->removeMensagensNaSessao();


		
		

                ?></div>
                </td>
              </tr>
            </table>

        </div>

    </body>
</html>
