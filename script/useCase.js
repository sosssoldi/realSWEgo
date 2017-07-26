$(function() {
	
	$('#name_UC').on('keyup', filterTable);
	
	$('#search_UC').on('submit', function(event) {
		return false;
	});
	
});

function filterTable() {
	const colNome = 1;

	$('#allUC tbody tr').each(function() {
		
		// Utilizzando toUpperCase per rendere la ricerca case-insensitive
		if($(this).children().eq(colNome).text().toUpperCase().indexOf($('#name_UC').val().toUpperCase()) > -1)
			$(this).show();
		else
			$(this).hide();
	});
	
	// Avviso se non ci sono risultati
	$('#noResult').remove();
	if(!($('#allUC tbody tr').is(':visible')))
		$('#allUC').after('<p id="noResult">Nessun risultato trovato</p>');
}