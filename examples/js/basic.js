$(function(){

  $("#principal").off("click", "#buscar");

  $("#principal").on("click", "#buscar", function(){

    var nome = $("#nome").val();
    if(nome !== ""){
      $.post("../php/select-grupos.php", {nome : nome}, function(projetos){
        projetos = JSON.parse(projetos);

        var i,
            s,
            g = {
              nodes: [],
              edges: []
            };

        $.each(projetos[0], function(i, titulo, id, qnt){
          var tam = projetos[0][i].qnt;
          if(tam <= 3) tam = tam * 2;
          else if(tam > 3 && tam <= 5) tam = tam * 1.5;
          else tam = tam * 1.2;

          g.nodes.push({
            id: projetos[0][i].id,
            label: projetos[0][i].titulo,
            x: Math.random(),
            y: Math.random(),
            size: tam,
            color: '#2E64FE'
          });
        });

        $.each(projetos[1], function(i, titulo, id){
          g.nodes.push({
            id: projetos[1][i].id,
            label: projetos[1][i].titulo,
            x: Math.random(),
            y: Math.random(),
            size: 3,
            color: '#5882FA'
          });
        });


        $.each(projetos[2], function(i, id, id_proj){
            g.edges.push({
              id: i,
              source: projetos[2][i].id,
              target: projetos[2][i].id_proj,
              size: Math.random(),
              color: '#2E9AFE'
            });
            // alert(i);
        });

        // Instantiate sigma:
        s = new sigma({
          graph: g,
          container: 'graph-container'
        });

        // // Initialize the dragNodes plugin:
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

    });
    }
    else{
      alert("Inválido");
    }
    //$("#principal #conteudo").load("examples/filters.html");
    $("#container").load("basic.html #container");
  });

});