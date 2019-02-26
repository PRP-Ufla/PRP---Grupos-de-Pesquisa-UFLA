<?php
	
	session_start();

	if(!isset($_POST['nome'])){
		die("Inválido");
	}

	require_once 'db/DBUtils.class.php';
	require_once 'position.class.php';
	$db = new DBUtils();
	$position = new position();

	$string = $_POST['nome'];
	$array = explode(' ', $string);
	$buscar = "";

	//variavel centro define qual será o ponto central do grafo
	define("centro", 0);

	// para quebrar a string passada via post em nomes separados
	foreach ($array as $nome) {
		$buscar .= '%';
		$buscar .= $nome;
		$buscar .= '%';
	}

	// salvando o arquivo xml
	function salvar($xml){
		$arquivo = fopen('../examples/data/graph_members.gexf', 'w+');
		$escrever = fwrite($arquivo, $xml);
		fclose($arquivo);
	}

	//encontrar uma string dentro de outra
	function search_name($str){
		$str = strtolower($str); 
		$array = explode(' ', $str);

		$string = $_POST['nome'];
		$string = strtolower($string);
		$array2 = explode(' ', $string);
		$prim_name = $array2[0];

		if(in_array($prim_name, $array)){
			return true;
		}
		else return false;
	}

	/********************FIM*************************/

	// começo da geração do arquivo xml
	$xml = "<?xml version='1.0' encoding='UTF-8'?>\n";
	$xml .= "<gexf xmlns='http://www.gexf.net/1.2draft' version='1.2' xmlns:viz='http://www.gexf.net/1.2draft/viz' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:schemaLocation='http://www.gexf.net/1.2draft http://www.gexf.net/1.2draft/gexf.xsd'>\n";
	$xml .= "<meta lastmodifieddate='2014-06-26'>
    <creator>Gephi 0.8.1</creator>
    <description></description>
  </meta>\n";
  	$xml .= "<graph defaultedgetype='directed' mode='static'>
    <attributes class='node' mode='static'>
    <attribute id='id' title='id' type='integer'></attribute>
    <attribute id='nome' title='nome' type='string'></attribute>
    <attribute id='departamento' title='departamento' type='string'></attribute>
    <attribute id='sexo' title='sexo' type='string'></attribute>
    <attribute id='grpPesquisa' title='grpPesquisa' type='array'></attribute>
    <attribute id='nomePrinc' title='nomePrinc' type='string'></attribute>
    </attributes>
    <nodes>\n";

    /////////////////////////////////////////////////////////////////////
    // SQL que seleciona todos os membros de um projeto
    $sql = "select p.id_pessoa as id, p.nome, count(p.nome) as qnt, uni.sigla, p.sexo as sexo
			from projetos.membro_projeto mp
			inner join
				-- junção com nome de pessoa passada como parametro
				(select mp.id_pessoa as pessoa, mp.id_projeto as projeto
				from comum.pessoa p
				inner join projetos.membro_projeto mp
				on (mp.id_pessoa = p.id_pessoa)
				where p.nome ilike '".$buscar."') principal 
				
			on mp.id_projeto = principal.projeto 
			inner join comum.pessoa p 
			on (p.id_pessoa = mp.id_pessoa)
			left join comum.usuario u 
			on (u.id_pessoa = p.id_pessoa)
			left join comum.unidade uni 
			on (uni.id_unidade = u.id_unidade)
			group by p.nome, id, uni.sigla, sexo
			order by qnt desc, p.nome";
	// Fim SQL
	//////////////////////////////////////////////////////////////////////


	$members = $db->executarConsulta($sql);
	$qntMembers = count($members);
	
	// Adicionando as informações desses membros a um arquivo XML

	//Se essa busca retornou alguma coisa
	if($qntMembers > 0){

		/*
		$cont -> utilizado para controlar quais dados estão
		sendo inserido no xml
		$x, $y -> pontos do eixo cartesiano
		$tam -> tamanho do vértice
		$quadrante -> variavel que controlará em qual quadrante está
		o vértice, se for igual a 4 então a margem é incrementada
		*/

		$cont = 0;
		$tam = 0;
		$quadrante = 1;
		$position->setMargin($qntMembers);
		$position->setCenter(0);
		$x = $position->getCenter();
		$y = $position->getCenter();

		foreach ($members as $member) {
			
			$r = 194;
			$g = 194;
			$b = 214;
			$tam = $member[2];

			if($quadrante <= 4){
				$x = $position->getX();
				$y = $position->getY();
			}
			else{
				$position->marginIncrement();
				$x = $position->getX();
				$y = $position->getY();

				if($position->getQuadrant() == 0){
					$quadrante++;
				}
				else{
					$quadrante = 1;
				}
			}

			//se o nome do membro for igual ao que foi passado por parâmetro
			//a cor do mesmo será diferente dos demais
			$nome_principal = search_name($member[1]);

			if($nome_principal == true){
				$r = 133;
				$g = 133;
				$b = 173;
			}

			$departamento = $member[3];

			if($departamento == ''){
				$departamento = "SEM DEPARTAMENTO";
			}

			$xml .= "<node id='$member[0]' label='$member[1]'>\n";
			$xml .= "	<attvalues>
			<attvalue for='id' value='$member[0]'></attvalue>
			<attvalue for='nome' value='$member[1]'></attvalue>
			<attvalue for='departamento' value='$departamento'></attvalue>
			<attvalue for='sexo' value='$member[4]'></attvalue>";
		

			$sql = "select proj.titulo
			from projetos.projeto proj 
			inner join projetos.membro_projeto mem
			on (mem.id_projeto = proj.id_projeto)

			where mem.id_pessoa = '".$member[0]."'";

			$grp_pesquisa = $db->executarConsulta($sql);

			// foreach ($grp_pesquisa as $grp) {
			// 	$xml .= "<attvalue for='grpPesquisa'>
			// 	<grupo value='$grp[0]'></grupo>
			// 	</attvalue>";
			// }

			if($nome_principal == true){
				$xml .= "<attvalue for='nomePrinc' value='PRINCIPAIS'></attvalue>";
			}
			else $xml .= "<attvalue for='nomePrinc' value='SECUNDÁRIOS'></attvalue>";

			$xml .= "</attvalues>\n";

			$xml .= "<viz:size value='$tam'></viz:size>\n";
			$xml .= "<viz:position x='$x' y='$y' z='0.0'></viz:position>\n";

			$xml .= "<viz:color r='$r' g='$g' b='$b'></viz:color>\n";
			// $xml .= "<viz:color r='210' g='210' b='224'></viz:color>\n";
			$xml .= "</node>\n";
			$cont++;
		}

		$xml .= "</nodes>\n";

		// Fim da adição de membros

		//////////////////////////////////////////////////////////////////////
		// SQL que busca todos as relações de membros em projetos
		$sql = "select p.id_pessoa as id, mp.id_projeto as projeto
				from projetos.membro_projeto mp
				inner join
					-- junção com nome de pessoa passada como parametro
					(select mp.id_pessoa as pessoa, mp.id_projeto as projeto
					from comum.pessoa p
					inner join projetos.membro_projeto mp
					on (mp.id_pessoa = p.id_pessoa)
					where p.nome ilike '".$buscar."') principal 
					
				on mp.id_projeto = principal.projeto 
				-- and not (mp.id_pessoa = principal.pessoa)
				inner join comum.pessoa p 
				on (p.id_pessoa = mp.id_pessoa)

				order by mp.id_projeto desc, mp.id_pessoa";

		// Fim SQL
		//////////////////////////////////////////////////////////////////////

		$connections = $db->executarConsulta($sql);

		// Adicionando as conecções a um arquivo XML

		$xml .= "<edges>\n";
		$tam = count($connections);

		// Loop para criar as conecções (arestas) do grafo
		for ($i = 0; $i < $tam; $i++) {

			// $connections[$i][0] = id da pessoa;
			// $connections[$i][1] = id do projeto;

			$memberA = $connections[$i][0];
			$proj = $connections[$i][1];
			$j = $i;
			$memberB = $connections[$j][0];
			$projB = $connections[$j][1];
			while ($j < $tam && $projB == $proj) {
				$memberB = $connections[$j][0];
				$projB = $connections[$j][1];
				if(($memberA != $memberB) && ($proj == $projB)){
					$xml .= "	<edge source='$memberA' target='$memberB'>
			<attvalues></attvalues>
		</edge>\n";
				}
				
				$j++;
			}
		}

		$xml .= "</edges>
	  </graph>
	</gexf>\n";
		
		salvar($xml);
		echo $qntMembers;
	}

	else{
		die("Nenhum membro encontrado!");
	}
?>