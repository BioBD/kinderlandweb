<body id="login-bg"> 
 
<!-- Start: login-holder -->
<div id="login-holder">

	<!-- start logo -->
	<div id="logo-login">
		<a href="index.html"><img src="<?=$this->config->item('assets');?>images/kinderland/logo.png" width="156" height="40" alt="" /></a>
	</div>
	<!-- end logo -->
	
	<div class="clear"></div>
	
	<!--  start loginbox ................................................................................. -->
	<div id="loginbox">
	
	<!--  start login-inner -->
	<div id="login-inner">
		<h1> Olá, <?=$name?> </h1>
		<br />
		<p> 
			Seu cadastro foi efetuado com sucesso! <br />Para acessar sua conta, use seu email como login.
		</p>

		<a href="<?=$this->config->item('url_link');?>login/index" class="forgot-pwd">Voltar a tela inicial</a>
	</div>
 	<!--  end login-inner -->
	<div class="clear"></div>
</div>
<!--  end loginbox -->
 

</div>
<!-- End: login-holder -->
</body>
</html>