$(function(){

	$("#principal").off("click", "#click-buscar");

	$("#principal").on("click", "#click-buscar", function(){
		$.post("./php/select-grupos.php", function(projetos){
			projetos = JSON.parse(projetos);

	        $.each(projetos[2], function(i, id, id_proj){
		        alert(projetos[2][i].id);
	        });
		});
	});

});