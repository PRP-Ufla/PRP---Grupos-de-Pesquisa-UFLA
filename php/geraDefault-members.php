<?php
	
	//Gera um grafo inicial com os pesquisadores com mais projetos no BD
	//Esse arquivo foi baseado no arquivo "gera-sml.php", todas as funções aqui presentes
	//se basearam nas funções do mesmo, portanto primeiramente o mesmo deve ser lido.
	//PS.: as funções únicas desse arquivo receberão seus respectivos comentários 

	session_start();

	require_once 'db/DBUtils.class.php';
	require_once 'position.class.php';
	$db = new DBUtils();

	//Class position - para entender as funções e atributos, ler o arquivo "position.class.php"
	$position = new position();

	$sql = "select count(*) qnt, p.nome, p.id_pessoa
			from projetos.membro_projeto mp
			inner join comum.pessoa p 
			on(p.id_pessoa = mp.id_pessoa)

			group by mp.id_pessoa, p.nome, p.id_pessoa
			order by qnt desc
			limit 20";

	$members = $db -> executarConsulta($sql);
	
	$qnt = count($members);
	
	if($qnt > 0){

		// salvando o arquivo xml
		function salvar($xml){
			$arquivo = fopen('../examples/data/graph_members.gexf', 'w+');
			$escrever = fwrite($arquivo, $xml);
			fclose($arquivo);
		}

		function friend($memberA, $memberB, $db){
			//Verifica se um pesquisador possui projetos em comum a outro
			if($memberA != $memberB){
				$sql = "select *
						from projetos.membro_projeto mpA, projetos.membro_projeto mpB

						where mpA.id_pessoa = '".$memberA."' and mpB.id_pessoa = '".$memberB."'
						and (mpA.id_projeto = mpB.id_projeto)";

				return count($db -> executarConsulta($sql));
			}

			return 0;
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

	    $cont = 0;
		$tam = 0;
		$quadrante = 1;
		$position->setMargin($qnt);
		$position->setCenter(0);
		$x = $position->getCenter();
		$y = $position->getCenter();
		$i = 0;
		foreach ($members as $member) {
			
			$r = rand(100,999);
			$g = rand(100,999);
			$b = rand(100,999);
			$tam = $member[0];

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

			$xml .= "<node id='$member[2]' label='$member[1]'>\n";
			$xml .= "	<attvalues>
			<attvalue for='id' value='$member[2]'></attvalue>
			<attvalue for='nome' value='$member[1]'></attvalue>";

			$xml .= "</attvalues>\n";

			$xml .= "<viz:size value='$tam'></viz:size>\n";
			$xml .= "<viz:position x='$x' y='$y' z='0.0'></viz:position>\n";

			$xml .= "<viz:color r='$r' g='$g' b='$b'></viz:color>\n";
			// $xml .= "<viz:color r='210' g='210' b='224'></viz:color>\n";
			$xml .= "</node>\n";

			$cont++;
		}
		$xml .= "</nodes>\n";

		$xml .= "<edges>\n";

		//Percorre a busca de membros original para ver quais membros tem projetos em comum
		//e assim adicionar uma conexão entre os mesmos

		foreach ($members as $memberA) {
			foreach ($members as $memberB) {
				$friend = friend($memberA[2],$memberB[2],$db);

				if($friend > 0){
					$xml .= "	<edge source='$memberA[2]' target='$memberB[2]'>
						<attvalues></attvalues>
					</edge>\n";
				}
			}
		}

		$xml .= "</edges>
			  </graph>
			</gexf>\n";
		
		// Fim da adição de membros

		salvar($xml);
		echo $qnt;

	}

	else die ("erro");

?>