$(function(){

	$("#principal").off("click", "#teste");

	$("#principal").on("click", "#teste", function(){
		$.post("php/select-members.php", function(resposta){
			resposta = JSON.parse(resposta);
			// membros = "";
			// $.each(resposta, function(i, nome, id){
			// 	membros += resposta[i].nome + " " + resposta[i].id + "\n";
			// });

			// alert(membros);
			alert(resposta);
		});

		
	});

});