$(function(){

  function buscar(nome){
    /*Essa função é utilizada para buscar um nome no BD e então
    gerar um arquivo XML com esse nome buscado*/

    // Variavel para guardar o nome do arquivo XML e o caminho
    var arquivo = 'data/graph_members.gexf';

    /*Primeiramente essa função limpa a memória cache.
                      
                      IMPORTANTE
                      
    Aqui nos deparamos com um dos principais problemas: A quantidade
    de buscas feita no BD será um volume alto comparado apenas com
    os testes feitos inicialmente. Nos mesmos, a cache enche rapidamente
    e logo os grafos começam a ficar incoerentes com a busca, isso devido ao
    fato do navegador não carregar o arquivo XML e sim buscar o grafo que foi gerado
    anteriormente na cache do navegador.
    Com esse arquivo .php a baixo, a memória cache é limpa antes de um novo arquivo 
    ser buscado*/
    $.post("./../php/apagar-xml.php", {arquivo : arquivo});


    /*O nome digitado na busca é verificado. Essa sessão deverá ser alterada para que
    ao abrir a página de cara o arquivo default seja carregado.
    */
    if(nome !== ""){
      /*Será gerada uma busca no BD e assim um arquivo
      xml será gerado.
      Verificar o .php a baixo para mais informações*/
      $.post("./../php/gera-xml.php", {nome : nome}, function(resposta){
        $("#principal").load("busca-membros.html", function(){
          $("#nome").val(nome);
        });
      });

    }
    else{
      /*Se nenhum nome for passado no campo de texto é gerado um arquivo default
      com os pesquisadores com mais conexões com outros pesquisadores do BD
      Verificar o .php a baixo*/
      $.post("./../php/geraDefault-members.php", function(resposta){
        $("#principal").load("busca-membros.html");
      });
    }
  }

  /*Função auxiliar para formatar os nomes do label. Serve para ajudar na busca
  pois os nomes do label vem com formatações diferentes*/

  function formName(text) {

      var loweredText = text.toLowerCase();
      var words = loweredText.split(" ");
      for (var a = 0; a < words.length; a++) {
          var w = words[a];

          var firstLetter = w[0];
          w = firstLetter.toUpperCase() + w.slice(1);

          words[a] = w;
      }
      return words.join(" ");
  }

  /**
   * This is an example on how to use sigma filters plugin on a real-world graph.

                            IMPORTANTE

   Abaixo estão as funções, objetos e variáveis que já vieram com a API, verificar o
   arquivo da API Sigma.js no diretório examples/filters.html.
   */
  var filter;

  /**
   * DOM utility functions
   */
  var _ = {
    $: function (id) {
      return document.getElementById(id);
    },

    all: function (selectors) {
      return document.querySelectorAll(selectors);
    },

    removeClass: function(selectors, cssClass) {
      var nodes = document.querySelectorAll(selectors);
      var l = nodes.length;
      for ( i = 0 ; i < l; i++ ) {
        var el = nodes[i];
        // Bootstrap compatibility
        el.className = el.className.replace(cssClass, '');
      }
    },

    addClass: function (selectors, cssClass) {
      var nodes = document.querySelectorAll(selectors);
      var l = nodes.length;
      for ( i = 0 ; i < l; i++ ) {
        var el = nodes[i];
        // Bootstrap compatibility
        if (-1 == el.className.indexOf(cssClass)) {
          el.className += ' ' + cssClass;
        }
      }
    },

    show: function (selectors) {
      this.removeClass(selectors, 'hidden');
    },

    hide: function (selectors) {
      this.addClass(selectors, 'hidden');
    },

    toggle: function (selectors, cssClass) {
      var cssClass = cssClass || "hidden";
      var nodes = document.querySelectorAll(selectors);
      var l = nodes.length;
      for ( i = 0 ; i < l; i++ ) {
        var el = nodes[i];
        //el.style.display = (el.style.display != 'none' ? 'none' : '' );
        // Bootstrap compatibility
        if (-1 !== el.className.indexOf(cssClass)) {
          el.className = el.className.replace(cssClass, '');
        } else {
          el.className += ' ' + cssClass;
        }
      }
    }
  };

  function updatePane (graph, filter) {

    /*          IMPORTANTE
      
      Essas variáveis são os filtros que estarão presentes
    no nosso grafo. Vale lembrar que esses filtros devem estar no arquivo
    html

    PARA INSERIR UM FILTRO NOVO PRIMEIRAMENTE ADICIONAMOS UMA VARIÁVEL ABAIXO
    DESSE FILTRO...
    */

    // get max degree
    var maxDegree = 0,
        departamento = {};
        sexo = {};
        nomePrinc = {};
        // grpPesquisa = {};
    
    // read nodes
    graph.nodes().forEach(function(n) {

      /*Habilitamos a nossa variável filtro adicionada anteriormente nas linhas 
      149...*/
      maxDegree = Math.max(maxDegree, graph.degree(n.id));
      departamento[n.attributes.departamento] = true;
      sexo[n.attributes.sexo] = true;
      nomePrinc[n.attributes.nomePrinc] = true;
      // grpPesquisa[n.attributes.grpPesquisa] = true;
    })

    // min degree
    _.$('min-degree').max = maxDegree;
    _.$('max-degree-value').textContent = maxDegree;
    
    // node category

    /*Funções que buscam os atributos dos vértices
    Ao adicionar um filtro novo uma função dessas deverá 
    ser criada

    Qualquer dúvida basta ler a documentação da API na parte de
    configurações e nos arquivos do plugin filters*/

    var nodecategoryElt = _.$('departamento');
    Object.keys(departamento).forEach(function(c) {
      var optionElt = document.createElement("option");
      optionElt.text = c;
      nodecategoryElt.add(optionElt);
    });

    var nodecategoryElt = _.$('sexo');
    Object.keys(sexo).forEach(function(c) {
      var optionElt = document.createElement("option");
      optionElt.text = c;
      nodecategoryElt.add(optionElt);
    });

    var nodecategoryElt = _.$('nomePrinc');
    Object.keys(nomePrinc).forEach(function(c) {
      var optionElt = document.createElement("option");
      optionElt.text = c;
      nodecategoryElt.add(optionElt);
    });

    // var nodecategoryElt = _.$('grpPesquisa');
    // Object.keys(grpPesquisa).forEach(function(c) {
    //   var optionElt = document.createElement("option");
    //   optionElt.text = c;
    //   nodecategoryElt.add(optionElt);
    // });

    // reset button
    _.$('reset-btn').addEventListener("click", function(e) {
      _.$('min-degree').value = 0;
      _.$('min-degree-val').textContent = '0';
      _.$('departamento').selectedIndex = 0;
      _.$('sexo').selectedIndex = 0;
      // _.$('grpPesquisa').selectedIndex = 0;
      filter.undo().apply();
      _.$('dump').textContent = '';
      _.hide('#dump');
    });

    // export button
    _.$('export-btn').addEventListener("click", function(e) {
      var chain = filter.export();
      console.log(chain);
      _.$('dump').textContent = JSON.stringify(chain);
      _.show('#dump');
    });
  }

  //////////////////////////////////////////////////////
  /*              
                  Controle do click do mouse          
  */

  $("#principal").off("click", "#buscar-membro");

  $("#principal").on("click", "#buscar-membro", function(){
    var nome = $("#nome").val();
    buscar(nome);
  });

  $("#info-node").off("click", "#informacoes a");

  $("#info-node").on("click", "#informacoes a", function(){

    var nome = this.text;
    confirm(nome);

    var arquivo = 'data/graph_proj.gexf';
    $.post("./../php/apagar-xml.php", {arquivo : arquivo});
    $.post("./../php/gera-xml-proj.php", {nome : nome}, function(resposta){
      
      $("#principal").load("busca-grupos.html");

    });

  });

  /*                        FIM                      */
  /////////////////////////////////////////////////////

  // Initialize sigma with the dataset:
  //   e-Diaspora Moroccan corpus of websites
  //   by Dana Diminescu & Matthieu Renault
  //   http://www.e-diasporas.fr/wp/moroccan.html
  sigma.parsers.gexf('data/graph_members.gexf', {

    /*
    Instanciando os objetos do plugin filters

    Para entender essas instâncias basta acessar 
    o arquivo no diretório src/sigma.settings.js, 
    no mesmo da para vermos como insrir ou alterar uma
    instância do arquivo principal de configurações
    */

    container: 'graph-container',
    settings: {
      edgeColor: 'default',
      defaultEdgeColor: '#d1d1e0',
      borderSize: 5,
      defaultNodeColor: '#c2c2d6',
      defaultNodeBorderColor: '#a3a3c2',
      minNodeSize: 10,
      maxNodeSize: 40,
      doubleClickEnabled: false,
      labelSize: 'proportional',
      labelThreshold: 50,
      font: 'Courier New'
    }
  }, function(s) {
    // Initialize the Filter API
    //Abaixo seguem as funções que aplicam os filtros desejados
    filter = new sigma.plugins.filter(s);

    updatePane(s.graph, filter);

    function applyMinDegreeFilter(e) {
      var v = e.target.value;
      _.$('min-degree-val').textContent = v;

      filter
        .undo('min-degree')
        .nodesBy(function(n) {
          return this.degree(n.id) >= v;
        }, 'min-degree')
        .apply();
    }

    function applyDepartamentoFilter(e) {
      var c = e.target[e.target.selectedIndex].value;
      filter
        .undo('departamento')
        .nodesBy(function(n) {
          return !c.length || n.attributes.departamento === c;
        }, 'departamento')
        .apply();
    }

    function applySexoFilter(s) {
      var c = s.target[s.target.selectedIndex].value;
      filter
        .undo('sexo')
        .nodesBy(function(n) {
          return !c.length || n.attributes.sexo === c;
        }, 'sexo')
        .apply();
    }

    function applyNomeFilter(p) {
      var c = p.target[p.target.selectedIndex].value;
      filter
        .undo('nomePrinc')
        .nodesBy(function(n) {
          return !c.length || n.attributes.nomePrinc === c;
        }, 'nomePrinc')
        .apply();
    }

    // function applyGrpFilter(s) {
    //   var c = s.target[s.target.selectedIndex].value;
    //   filter
    //     .undo('grpPesquisa')
    //     .nodesBy(function(n) {
    //       return !c.length || n.attributes.grpPesquisa === c;
    //     }, 'grpPesquisa')
    //     .apply();
    // }

    _.$('min-degree').addEventListener("input", applyMinDegreeFilter);  // for Chrome and FF
    _.$('min-degree').addEventListener("change", applyMinDegreeFilter); // for IE10+, that sucks
    _.$('departamento').addEventListener("change", applyDepartamentoFilter);
    _.$('sexo').addEventListener("change", applySexoFilter);
    _.$('nomePrinc').addEventListener("change", applyNomeFilter);
    // _.$('grpPesquisa').addEventListener("change", applyGrpFilter);


    // Initialize the dragNodes plugin:
    var dragListener = sigma.plugins.dragNodes(s, s.renderers[0]);

    dragListener.bind('startdrag', function(event) {
      console.log(event);
    });
    dragListener.bind('drag', function(event) {
      console.log(event);
    });
    dragListener.bind('drop', function(event) {
      console.log(event);
    });
    dragListener.bind('dragend', function(event) {
      console.log(event);
    });

    // Para carregar a página com as informações do nó que foi clicado
    s.bind("doubleClickNode", function(event){
      var node = event.data.node; 
      console.log(node.id);
      nome = node.label;
      buscar(nome);
    });

    //Ao clicar em um vértice, as informações sobre o mesmo são atualizadas dinamicamente
    //em uma div (informações)
    s.bind("clickNode", function(event){
      var node = event.data.node; 
      console.log(node.id);
      var id = node.id;
      var nome = node.label;
      nome = formName(nome);
      $('#nome-info').empty();
      $('#informacoes').empty();
      $.post("../php/busca-informacoes.php", {id : id}, function(info){
        info = JSON.parse(info);
        var informacoes = "";
        var id_proj = "";
        $("#info-node #nome-info").load("busca-membros.html #nome-info", function(){
          $('#nome-info').append("<h4>"+nome+" participa de " +info[0]+ " projetos</h4>")
            $.each(info[1],function(i,nome,titulo,id_projeto,uni){
              informacoes = info[1][i].titulo;
              id_proj = info[1][i].id_projeto;
              $('#informacoes').append("<br><a id="+id_proj+" href='busca-grupos.html'>"+informacoes+"<a/><div class='line'></div>");
            });
        });
      });
    });

    sigma.refresh();
});

});

