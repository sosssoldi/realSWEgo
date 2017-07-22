$(function() {
	
	let menu = $('nav').eq(0);	
	let button = $('#mobileMenu');
	
	menu.addClass('hideNav');
	button.removeClass('hideButton');
	button.addClass('showButton');
	
	button.on('click', function() {
		if(menu.height() === 0)
			menu.removeClass('hideNav');
		else
			menu.addClass('hideNav');
	});
	
});