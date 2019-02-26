<?php

	/*para não sobrecarregar a cache, antes de criar um arquivo novo 
	o antigo é apagado*/

	session_start();

	if(!isset($_POST['arquivo'])){
		die("Inválido");
	}

	$arquivo = $_POST['arquivo'];

	//Para apagar o arquivo
	unlink($arquivo);
	//Para limpar a cache
	clearstatcache();
?>