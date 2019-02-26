Em geral, os arquivos js presentes aqui seguem o mesmo padrão. A maioria das funções presentes são do plugin filters da API. Para informações sobre o mesmo  basta ler sua documentação que se encontra no arquivo do diretório "plugins/sigma.plugins.filter/README.md" ou no próprio arquivo do plugin "sigma.plugins.filter.js" que se encontra no mesmo diretório citado.


PROBLEMAS ENCONTRADOS

	Os problemas identificados durante a implementação desse protótipo serão listados a baixo:

	1º - A memória cache está enchendo com dados de pesquisas feitas; quando uma determinada busca é feita ela pode não apresentar os dados reais da busca. Esse problema pode ser causado por uma busca ineficiente, por um arquivo xml muito grande, problemas no hardware/software dessa própria máquina ou algum outro não identificado;

	2º - Como citado acima, o problema da cache será algo recorrente e talvez o principal desafio. Porém, outras coisas relacionadas serão desafios a se resolverem. Quando uma busca SQL retorna muitos arquivos, um arquivo xml muito grande será gerado. Com isso, o tempo para ler esse mesmo arquivo será maior que o tempo para carregar a página. Isso faz com que as inforamações sejam exibidas de forma incorreta na página.

	3º - Ao criar um filtro em que o vértice possua mais de uma instâcia desse filtro, ao aplica-lo a API não exibe os dados de forma correta.
	Ex.: Um pesquisador pode participar de mais de um grupo de pesquisa; porém, ao aplicar o filtro de grupos de pesquisa (já presente na página porém desativado), os pesquisadores realmente presentes nesses grupos não são exibidos de forma correta.