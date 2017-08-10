function createGraphics(obb, des, opz) {
	
	// Grafico per i requisiti obbligatori
	let obbligatori = {
		type: 'doughnut',
		data: {
		    datasets: [{
		        data: [obb['s'],obb['u']],
		        backgroundColor: ["#4572c1","#eb7b34"],
		        label: 'Requisiti obbligatori'
		    }],
		    labels: ["Implementati","Non Implementati"]
		},
		options: {
		    responsive: true,
		    legend: {
		        position: 'top',
		    },
		    title: {
		        display: true,
		        text: 'Requisiti Obbligatori'
		    },
		    animation: {
		        animateScale: true,
		        animateRotate: true
		    }
		}
	};
	
	// Grafico per i requisiti desiderabili
	let desiderabili = {
		type: 'doughnut',
		data: {
		    datasets: [{
		        data: [des['s'],des['u']],
		        backgroundColor: ["#629b6d","#e8d685"],
		        label: 'Requisiti desiderabili'
		    }],
		    labels: ["Implementati","Non Implementati"]
		},
		options: {
		    responsive: true,
		    legend: {
		        position: 'top',
						labels: {
							fontColor: 'white'
						}
		    },
		    title: {
		        display: true,
		        text: 'Requisiti desiderabili',
						fontColor: 'white'
		    },
		    animation: {
		        animateScale: true,
		        animateRotate: true
		    }
		}
	};

	// Grafico per i requisiti opzionali
	let opzionali = {
		type: 'doughnut',
		data: {
		    datasets: [{
		        data: [opz['s'],opz['u']],
		        backgroundColor: ["#84b761","#cc4748"],
		        label: 'Requisiti opzionali'
		    }],
		    labels: ["Implementati","Non Implementati"]
		},
		options: {
		    responsive: true,
		    legend: {
		        position: 'top',
		    },
		    title: {
		        display: true,
		        text: 'Requisiti opzionali'
		    },
		    animation: {
		        animateScale: true,
		        animateRotate: true
		    }
		}
	};

	if(obb['s'] > 0 || obb['u'] > 0) {
		var ctxobb = document.getElementById("graphObb").getContext("2d");
		window.myDoughnut1 = new Chart(ctxobb, obbligatori);
	}
	else {
		$("#graphObb").remove();
		$("#graphicObb ul").remove();
		$("#graphicObb").prepend('<p class="noRequirement">Nessun requisito obbligatorio inserito.</p>');
	}
	
	if(des['s'] > 0 || des['u'] > 0) {
		var ctxdes = document.getElementById("graphDes").getContext("2d");
		window.myDoughnut2 = new Chart(ctxdes, desiderabili);
	}
	else {
		$("#graphDes").remove();
		$("#graphicDes ul").remove();
		$("#graphicDes").prepend('<p class="noRequirement">Nessun requisito desiderabile inserito.</p>');
	}
	
	if(opz['s'] > 0 || opz['u'] > 0) {
		var ctxopz = document.getElementById("graphOpz").getContext("2d");
		window.myDoughnut3 = new Chart(ctxopz, opzionali);
	}
	else {
		$("#graphOpz").remove();
		$("#graphicOpz ul").remove();
		$("#graphicOpz").prepend('<p class="noRequirement">Nessun requisito opzionale inserito.</p>');
	}
}

function createGraphicsAdmin(month, logs) {
	var logdata = {
        type: 'line',
        data: {
            labels: month,
            datasets: [{
                label: "Accessi",
                backgroundColor: "#f00",
                borderColor: "#f00",
                data: logs,
                fill: false,
            }]
        },
        options: {
            responsive: true,
            title:{
                display:true,
                text:'Accessi'
            },
            tooltips: {
                mode: 'index',
                intersect: false,
            },
            hover: {
                mode: 'nearest',
                intersect: true
            },
            scales: {
                xAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Settimana'
                    }
                }],
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Numero'
                    }
                }]
            }
        }
	};
	var ctxlog = document.getElementById("graph").getContext("2d");
    window.myLine = new Chart(ctxlog, logdata);
    console.log(window.myLine);
}

// Hide/show features user
$(function() {
	setTimeout(function() {
		$('#change_password').hide();
		$('#graphics h3').hide();
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
				$('#graphics h3').hide();
				$('#graphics div').hide();
				$('#graphics a').html('Mostra copertura dei requisiti');
			}
			else
			{
				$('#graphics h3').show();
				$('#graphics div').show();
				$('#graphics a').html('Nascondi copertura dei requisiti');
			}
		});
	},200);
});