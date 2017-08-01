function createGraphics(obb, des, opz) {
	
	// Grafico per i requisiti obbligatori
	let obbligatori = AmCharts.makeChart('graphicObb', {
		"type": "pie",
		"theme": "light",
		"titleField": "Soddisfacimento",
		"valueField": "Requisiti",
		"colorField": "color",
		"fontSize": 9,
		"tabIndex": 2,
		"dataProvider": [
		{
			"Soddisfacimento": obb[0].sodd,
			"Requisiti": obb[0].req,
			"color": "#4572c1"
		},
		{
			"Soddisfacimento": obb[1].sodd,
			"Requisiti": obb[1].req,
			"color": "#eb7b34"
		}
		]
	});
	
	// Grafico per i requisiti desiderabili
	let desiderabili = AmCharts.makeChart('graphicDes', {
		"type": "pie",
		"theme": "dark",
		"titleField": "Soddisfacimento",
		"valueField": "Requisiti",
		"colorField": "color",
		"fontSize": 9,
		"tabIndex": 4,
		"dataProvider": [
		{
			"Soddisfacimento": des[0].sodd,
			"Requisiti": des[0].req,
			"color": "#629b6d"
		},
		{
			"Soddisfacimento": des[1].sodd,
			"Requisiti": des[1].req,
			"color": "#e8d685"
		}
		]
	});

	// Grafico per i requisiti opzionali
	let opzionali = AmCharts.makeChart('graphicOpz', {
		"type": "pie",
		"theme": "light",
		"titleField": "Soddisfacimento",
		"valueField": "Requisiti",
		"colorField": "color",
		"fontSize": 9,
		"tabIndex": 6,
		"dataProvider": [
		{
			"Soddisfacimento": obb[0].sodd,
			"Requisiti": obb[0].req,
			"color": "#84b761"
		},
		{
			"Soddisfacimento": obb[1].sodd,
			"Requisiti": obb[1].req,
			"color": "#cc4748"
		}
		]
	});
}

// Hide/show features user
$(function() {
	$('#change_password').hide();
	$('#graphics h2').hide();
	$('#graphics div').hide();
	
	$('#changePassword a').click(function() {
		if($('#change_password').is(':visible'))
		{
			$('#change_password').hide();
			$('#changePassword a').html('Cambia passoword');
		}
		else
		{
			$('#change_password').show();
			$('#changePassword a').html('Nascondi cambia password');
			
		}
	});
	
	$('#graphics a').click(function() {
		if($('#graphics div').is(':visible'))
		{
			$('#graphics h2').hide();
			$('#graphics div').hide();
			$('#graphics a').html('Mostra copertura dei requisiti');
		}
		else
		{
			$('#graphics h2').show();
			$('#graphics div').show();
			$('#graphics a').html('Nascondi copertura dei requisiti');
		}
	});
});