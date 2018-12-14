<?php
	
	session_start();
	
	if(!isset($_POST['nome'])){
		die("Inválido");
	}

	require_once 'db/DBUtils.class.php';

	$db = new DBUtils();

	$string = $_POST['nome'];
	$array = explode(' ', $string);
	$palavra = "";

	// para quebrar a string passada via post em nomes separados
	foreach ($array as $nome) {
		$palavra .= '%';
		$palavra .= $nome;
		$palavra .= '%';
	}

	$sql = "select p.titulo as titulo, p.id_projeto as id, qnt.membros as qnt
			from projetos.projeto p
			inner join 
				(select proj.id_projeto as id, count(mp.id_projeto) as membros
				from projetos.membro_projeto mp
				inner join projetos.projeto proj
				on (proj.id_projeto = mp.id_projeto)
				group by id, mp.id_projeto) qnt
			on (qnt.id = p.id_projeto)
			
			where p.titulo ilike '".$palavra."' or p.descricao ilike '".$palavra."'";

	$projetos[0] = $db->executarConsulta($sql);

	$sql = "select distinct p.titulo as titulo, p.id_projeto as id
			from projetos.membro_projeto mp
			inner join
				(select mp.id_pessoa as id, mp.id_projeto as proj
				from projetos.membro_projeto mp
				inner join projetos.projeto p 
				on(p.id_projeto = mp.id_projeto and (p.titulo ilike '".$palavra."' or p.descricao ilike '".$palavra."'))) pessoa
			on (mp.id_pessoa = pessoa.id and mp.id_projeto != pessoa.proj)
			inner join projetos.projeto p 
			on (p.id_projeto = mp.id_projeto)

			group by p.id_projeto, mp.id_membro_projeto, titulo
			order by id";

	$projetos[1] = $db->executarConsulta($sql);

	$sql = "select mp.id_projeto as id, pessoas.projeto as id_proj
			from projetos.membro_projeto mp
			inner join 
				(select distinct mp.id_pessoa as id, p.id_projeto as projeto
				from projetos.projeto p
				inner join projetos.membro_projeto mp
				on (mp.id_projeto = p.id_projeto)
				where p.titulo ilike '".$palavra."' or p.descricao ilike '".$palavra."'
				order by id) pessoas
			on (pessoas.id = mp.id_pessoa and pessoas.projeto != mp.id_projeto)
			-- limit 0";

	$projetos[2] = $db->executarConsulta($sql);
	
	echo json_encode($projetos);
?>