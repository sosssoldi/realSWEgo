function createGraphics(obb, des, opz) {
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
		],
		"clickSlice": null,
		"clickRightSlice": null
	});
	
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
		],
		"clickSlice": null,
		"clickRightSlice": null
	});
	
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
		],
		"clickSlice": null,
		"clickRightSlice": null
	});
}

// Nascondo il link "abusivo" nei grafici e mostro/nascondo le funzioni
$(function() {
	
	$('#graphics a').css('display', 'none');
	
	
	
});