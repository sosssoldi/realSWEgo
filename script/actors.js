$(function() {
	$('#insertActor').submit(function() {
		
		// Attore da aggiungere
		let actor = {
			name: $('#nameActor').val(),
			description: $('#descriptionActor').val()
		};
		
		//AJAX
		
		// Aggiungo l'attore alla lista
		addActor(actor);
		
		// Cancello il contenuto del form
		$('#nameActor').val('');
		$('#descriptionActor').val('');
				
		return false;
	});
});

function addActor(actor) {
	let newActor = '<div class="actor"><h2>' + actor.name + '</h2><p>' + actor.description + '</p><a class="hide delete" href="deleteActor.html?' + actor.name + '">Elimina</a><a class="hide modify" href="modifyActor.html?name=' + actor.name + '">Modifica</a></div>';
	$('#actors').append(newActor);
}