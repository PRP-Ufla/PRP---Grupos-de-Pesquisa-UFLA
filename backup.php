<?php
	
	session_start();
	require_once 'db/DBUtils.class.php';
	$db = new DBUtils();

	$string = $_POST['nome'];
	$array = explode(' ', $string);
	$buscar = "";

	//variavel centro define qual será o ponto central do grafo
	define("centro", 5000);

	// para quebrar a string passada via post em nomes separados
	foreach ($array as $nome) {
		$buscar .= '%';
		$buscar .= $nome;
		$buscar .= '%';
	}

	// salvando o arquivo xml
	function salvar($xml){
		$arquivo = fopen('../examples/data/new_graph.gexf', 'w+');
		$escrever = fwrite($arquivo, $xml);
		fclose($arquivo);
	}

	// escolhe entre duas opções passadas qual mais se distancia de um ponto central
	function get_option($A, $B){
		$option;

		if($A < $B){
			if($A > centro){
				$option = $B;
			}
			else{
				if((centro - $A) > ($B - centro)) $option = $A;
				else $option = $B;
			}
		}
		else{
			if($B > centro){
				$option = $A;
			}
			else{
				if((centro - $B) > ($A - centro)) $option = $B;
				else $option = $A;
			}
		}

		return $option;
	}

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
    </attributes>
    <nodes>\n";

    /////////////////////////////////////////////////////////////////////
    // SQL que seleciona todos os membros de um projeto
    $sql = "select p.id_pessoa as id, p.nome as nome, count(nome) as qnt
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

			group by nome, id
			order by qnt desc, nome ";
	// Fim SQL
	//////////////////////////////////////////////////////////////////////


	$members = $db->executarConsulta($sql);
	$qntMembers = count($members);
	
	// Adicionando as informações desses membros a um arquivo XML

	//Se essa busca retornou alguma coisa
	if($qntMembers > 0){
		$cont = 0;
		foreach ($members as $member) {
			$num = count($members);
			$x = 0;
			$y = 0;

			if($cont == 0){
				$x = centro;
				$y = centro;
			}
			else if($cont > 0 && $cont < $qntMembers * 0.1){
				$x = rand(centro - (centro * 0.25),centro + (centro * 0.25));	
				$y = rand(centro - (centro * 0.25),centro + (centro * 0.25));
			}
			else{
				$optionA = rand(centro - centro, centro * 2);
				$optionB = rand(centro - centro, centro * 2);
				$x = get_option($optionA,$optionB);

				$optionA = rand(centro - centro, centro * 2);
				$optionB = rand(centro - centro, centro * 2);
				$y = get_option($optionA,$optionB);
			}
			
			$tam = $member[2];				

			$xml .= "<node id='$member[0]' label='$member[1]'>\n";
			$xml .= "	<attvalues>
			<attvalue for='id' value='$member[0]'></attvalue>
			<attvalue for='nome' value='$member[1]'></attvalue>
		</attvalues>\n";
			$xml .= "<viz:size value='$tam'></viz:size>\n";
			$xml .= "<viz:position x='$x' y='$y' z='0.0'></viz:position>\n";

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
		echo ("Encontrado(s) " .$qntMembers. " membros relacionados a " .$string);
	}

	else{
		die("Nenhum membro encontrado!");
	}
?>