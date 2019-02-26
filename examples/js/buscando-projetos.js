/*Esse arquivo js segue os padrões do outro arquivo "buscando.js" presente no mesmo 
diretório ao qual o mesmo está contido. Todas as funções daqui vieram de lá*/

$(function(){

  function buscar(nome){
    if(nome !== ""){
        var arquivo = 'data/graph_proj.gexf';
        $.post("./../php/apagar-xml.php", {arquivo : arquivo});
        $.post("./../php/gera-xml-proj.php", {nome : nome}, function(resposta){
          // alert(resposta);
          $("#principal #container").empty();
        });

      }
      else{
        alert("Inválido");
      }
  }

  /**
   * This is an example on how to use sigma filters plugin on a real-world graph.
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
        el.style.display = (el.style.display != 'none' ? 'none' : '' );
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
    // get max degree
    var maxDegree = 0,
        categories = {};
    
    // read nodes
    graph.nodes().forEach(function(n) {
      maxDegree = Math.max(maxDegree, graph.degree(n.id));
      categories[n.attributes.acategory] = true;
    })

    // min degree
    _.$('min-degree').max = maxDegree;
    _.$('max-degree-value').textContent = maxDegree;
    
    // node category
    var nodecategoryElt = _.$('node-category');
    Object.keys(categories).forEach(function(c) {
      var optionElt = document.createElement("option");
      optionElt.text = c;
      nodecategoryElt.add(optionElt);
    });

    // reset button
    _.$('reset-btn').addEventListener("click", function(e) {
      _.$('min-degree').value = 0;
      _.$('min-degree-val').textContent = '0';
      _.$('node-category').selectedIndex = 0;
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
  /*Controle do click do mouse no campo de busca/links*/

  $("#cabecalho").off("click", "#buscar");

  $("#cabecalho").on("click", "#buscar", function(){
    var nome = $("#nome-proj").val();
    buscar(nome);
  });

  /*                        FIM                      */
  /////////////////////////////////////////////////////

  // Initialize sigma with the dataset:
  //   e-Diaspora Moroccan corpus of websites
  //   by Dana Diminescu & Matthieu Renault
  //   http://www.e-diasporas.fr/wp/moroccan.html
  sigma.parsers.gexf('data/graph_proj.gexf', {
    container: 'graph-container',
    settings: {
        edgeColor: 'default',
        defaultEdgeColor: '#d1d1e0',
        borderSize: 5,
        defaultNodeColor: '#c2c2d6',
        defaultNodeBorderColor: '#09eea7',
        minNodeSize: 10,
        maxNodeSize: 40,
        doubleClickEnabled: false,
        labelSize: 'proportional',
        labelThreshold: 20,
        font: 'Courier New'
      }
  }, function(s) {
    // Initialize the Filter API
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

    function applyCategoryFilter(e) {
      var c = e.target[e.target.selectedIndex].value;
      filter
        .undo('node-category')
        .nodesBy(function(n) {
          return !c.length || n.attributes.acategory === c;
        }, 'node-category')
        .apply();
    }

    _.$('min-degree').addEventListener("input", applyMinDegreeFilter);  // for Chrome and FF
    _.$('min-degree').addEventListener("change", applyMinDegreeFilter); // for IE10+, that sucks
    _.$('node-category').addEventListener("change", applyCategoryFilter);

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

    s.bind("clickNode", function(event){
      var node = event.data.node; 
      console.log(node.id);
      var id = node.id;
      $('#nome-info').empty();
      $('#informacoes').empty();
      $.post("../php/busca-info-proj.php", {id : id}, function(info){
        info = JSON.parse(info);
        var informacoes = "";
        var titulo = "";
        $("#info-node #nome-info").load("busca-grupos.html #nome-info", function(){
            $.each(info[1],function(i,titulo,resumo){
              titulo = info[1][i].titulo;
              informacoes = info[1][i].resumo;
              if(informacoes ===  "" || informacoes === null || informacoes === "null"){
                informacoes = "Sem informacoes sobre o projeto";
              }
              $('#nome-info').append("<h4>"+titulo+"</h4>")
              $('#informacoes').append("<p>"+informacoes+"<p/>");
            });
        });
      });
    });

  });
});