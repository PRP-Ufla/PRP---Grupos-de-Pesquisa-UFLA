$(function(){

	$("#principal").off("click", "#teste");

	$("#principal").on("click", "#teste", function(){
		var nome = $("#nome").val();
		if(nome !== ""){
			$.post("php/gera-xml.php", {nome : nome}, function(resposta){
				alert(resposta);
			});
		}
		else{
			alert("Inválido");
		}
		//$("#principal #conteudo").load("examples/filters.html");
	});

});