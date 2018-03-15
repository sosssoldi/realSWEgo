$(function() {
	if($('#noResult').text() != 'Non ci sono Requisiti.') {
  
		$('#name_R').on('keyup', filterTable);

		$('#search_R').on('submit', function(event) {
			return false;
		});
 	}
});

function filterTable() {
	const colDescription = 1;
	const colCodice = 0;

	$('#allR tbody tr').each(function() {
		// Utilizzando toUpperCase per rendere la ricerca case-insensitive
		if($(this).children().eq(colDescription).text().toUpperCase().indexOf($('#name_R').val().toUpperCase()) > -1 || $(this).children().eq(colCodice).text().toUpperCase().indexOf($('#name_R').val().toUpperCase()) > -1)
			$(this).show();
		else
			$(this).hide();
	});
	
	// Avviso se non ci sono risultati
	$('#noResult').remove();
	if(!($('#allR tbody tr').is(':visible')))
		$('#allR').after('<p id="noResult">Nessun risultato trovato</p>');
}