<?php

	//Esse arquivo foi baseado no arquivo "gera-sml.php", todas as funções aqui presentes
	//se basearam nas funções do mesmo, portanto primeiramente o mesmo deve ser lido.
	//PS.: as funções únicas desse arquivo receberão seus respectivos comentários 
	
	session_start();

	if(!isset($_POST['nome'])){
		die("Inválido");
	}

	require_once 'db/DBUtils.class.php';

	//Class position - para entender as funções e atributos, ler o arquivo "position.class.php"
	require_once 'position.class.php';
	$db = new DBUtils();
	$position = new position();

	// salvando o arquivo xml
	function salvar($xml){
		$arquivo = fopen('../examples/data/graph_proj.gexf', 'w+');
		$escrever = fwrite($arquivo, $xml);
		fclose($arquivo);
	}

		
	$string = $_POST['nome'];
	$array = explode(' ', $string);
	$palavra = "";

	// para quebrar a string passada via post em nomes separados
	foreach ($array as $nome) {
		$palavra .= '%';
		$palavra .= $nome;
		$palavra .= '%';
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
    <attribute id='titulo' title='titulo' type='string'></attribute>
    </attributes>
    <nodes>\n";

    $sql = "select p.id_projeto, p.titulo
			from projetos.projeto p
			where p.titulo ilike '".$palavra."' or p.palavras_chave ilike '".$palavra."'";

	$projetos = $db->executarConsulta($sql);

	$qnt = count($projetos);
	$quadrante = 1;
	$position->setMargin(1000);
	$position->setCenter(0);
	$x = $position->getCenter();
	$y = $position->getCenter();
	$r; 
	$g;
	$b;


	if($qnt > 0){

		foreach ($projetos as $projeto) {
			$r = rand(100,999);
			$g = rand(100,999);
			$b = rand(100,999);

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


			$xml .= "<node id='$projeto[0]' label='$projeto[1]'>\n";
			$xml .= "	<attvalues>
			<attvalue for='id' value='$projeto[0]'></attvalue>
			<attvalue for='titulo' value='$projeto[1]'></attvalue></attvalues>\n";
			$xml .= "<viz:size value='10'></viz:size>\n";
			$xml .= "<viz:position x='$x' y='$y' z='0.0'></viz:position>\n";
			$xml .= "<viz:color r='$r' g='$g' b='$b'></viz:color>\n";
			$xml .= "</node>\n";
		}

		$sql = "select p.id_projeto, p.titulo
				from (select pes.id_pessoa as id 
						from projetos.projeto p
						inner join projetos.membro_projeto mp
						on(mp.id_projeto = p.id_projeto)
						inner join comum.pessoa pes 
						on(pes.id_pessoa = mp.id_pessoa)
						where p.titulo ilike '".$palavra."' or p.palavras_chave ilike '".$palavra."') princ

				inner join projetos.membro_projeto mp
				on (mp.id_pessoa = princ.id)
				inner join projetos.projeto p 
				on(mp.id_projeto = p.id_projeto)

				where not (p.titulo ilike '".$palavra."' or p.palavras_chave ilike '".$palavra."')

				group by p.id_projeto, p.titulo
				order by p.titulo";

		$projetos = $db->executarConsulta($sql);


		foreach ($projetos as $projeto) {
			
			$r = rand(100,999);
			$g = rand(100,999);
			$b = rand(100,999);
			
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

			$xml .= "<node id='$projeto[0]' label='$projeto[1]'>\n";
			$xml .= "	<attvalues>
			<attvalue for='id' value='$projeto[0]'></attvalue>
			<attvalue for='titulo' value='$projeto[1]'></attvalue></attvalues>\n";
			$xml .= "<viz:size value='2'></viz:size>\n";
			$xml .= "<viz:position x='$x' y='$y' z='0.0'></viz:position>\n";
			$xml .= "<viz:color r='$r' g='$g' b='$b'></viz:color>\n";
			$xml .= "</node>\n";
		}

		$xml .= "</nodes>\n";
		$xml .= "<edges>\n";

		//Essa váriavel pode ser excluída, serve somente para um teste de verificação das conexões dos vértices
		$string = "";

		//SQL que busca projetos com membros em comum ao projeto buscado

		$sql = "select p.id_projeto, p.titulo, mp.id_pessoa
				from (select pes.id_pessoa as id 
					from projetos.projeto p
					inner join projetos.membro_projeto mp
					on(mp.id_projeto = p.id_projeto)
					inner join comum.pessoa pes 
					on(pes.id_pessoa = mp.id_pessoa)
					where p.titulo ilike '".$palavra."' or p.palavras_chave ilike '".$palavra."') princ

			inner join projetos.membro_projeto mp
			on (mp.id_pessoa = princ.id)
			inner join projetos.projeto p 
			on(mp.id_projeto = p.id_projeto)

			order by mp.id_pessoa";

		$projetos = $db->executarConsulta($sql);
		$qnt = count($projetos);

		for ($i = 0; $i < $qnt; $i++) {
			$j = $i;
			while($j < $qnt && $projetos[$i][2] == $projetos[$j][2]){
				$string .= $projetos[$j][0];
				$string .= " -> ";
				$string .= $projetos[$i][0];
				$string .= "\n";
					$inicio = $projetos[$i][0];
					$fim = $projetos[$j][0];
					if($inicio != $fim && $projetos[$i][2] == $projetos[$j][2]){
						$xml .= "<edge source='$inicio' target='$fim'>
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
		echo ($string);
	}

	else die ("error!");
?>