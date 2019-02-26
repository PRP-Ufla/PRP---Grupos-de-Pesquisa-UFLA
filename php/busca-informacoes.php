<?php

	//Busca as informações dos vértices no BD
	session_start();

	if(!isset($_POST['id'])){
		die("Inválido");
	}

	$id = $_POST['id'];

	require_once 'db/DBUtils.class.php';
	$db = new DBUtils();

	$sql = "select pes.nome as nome, p.titulo, p.id_projeto, uni.sigla
			from projetos.projeto p
			inner join projetos.membro_projeto mp
			on (mp.id_projeto = p.id_projeto)
			left join comum.usuario usu
			on (usu.id_pessoa = mp.id_pessoa)
			inner join comum.pessoa pes
			on (pes.id_pessoa = mp.id_pessoa)
			left join comum.unidade uni 
			on (uni.id_unidade = usu.id_unidade)
			where mp.id_pessoa = '".$id."'
			group by pes.nome, p.titulo, p.id_projeto, uni.sigla
			order by p.titulo";

	$info[1] = $db -> executarConsulta($sql);
	$info[0] = count($info[1]);

	if($info[0] > 0){
		echo json_encode($info);
	}
	else {
		die("error");
	}
?>