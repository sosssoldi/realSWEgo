$(function() {
	
	$('.multiple').each(function() {
		if($(this).find('input:checked').length == 1)
			$(this).find('input:checkbox:first').prop('checked', true);
		else
			$(this).find('input:checkbox:first').prop('checked', false);
	});
	
	// onClick per deselezionare o meno checkbox 'Nessuno'
	$('.multiple').on('click', function() {
		let checked = $(this).find('input:checked');
		let valueChecked = [];
		for(let i=0; i < checked.length; i++)
			valueChecked.push(checked.eq(i).val());
		
		if(checked.length == 0) 
			$(this).find('input:checkbox:first').prop('checked', true);
		
		if(checked.length > 1 && valueChecked.includes('NULL'))
			$(this).find('input:checkbox:first').prop('checked', false);
	});
});