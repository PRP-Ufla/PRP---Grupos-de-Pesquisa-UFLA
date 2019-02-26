<?php

	/*Busca as informações de um projeto em que seu id foi passado via POST para que esses dados sejam exibidos como informação na div "informacoes"*/

	session_start();

	if(!isset($_POST['id'])){
		die("Inválido");
	}

	$id = $_POST['id'];

	require_once 'db/DBUtils.class.php';
	$db = new DBUtils();

	$sql = "select p.titulo, p.resumo
			from projetos.projeto p
			where p.id_projeto = '".$id."'";

	$info[1] = $db -> executarConsulta($sql);
	$info[0] = count($info[1]);

	if($info[0] > 0){
		echo json_encode($info);
	}
	else {
		die("error");
	}
?>