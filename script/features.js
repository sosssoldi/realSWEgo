$(function() {
	var features = $('.info').length;

	for(let i=0; i < features; i++) {

		let link = 'a[href="#readFeature' + (i+1) + '"]';
		let info = '#feature' + (i+1);

		$(info).hide();
		$(link).removeClass('hideMoreInfo');
		$(link).addClass('showMoreInfo');
		$(link).on('click', function() {
			if($(info).is(':visible'))
			{
				$(info).hide();
				$(link).html('Premi per saperne di più');
			}
			else
			{
				$(info).slideToggle(500);
				$(link).html('Premi per nascondere');
			}
		});
	}
});
