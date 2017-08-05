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
		    },
		    title: {
		        display: true,
		        text: 'Requisiti desiderabili'
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

	var ctxobb = document.getElementById("graphObb").getContext("2d");
	window.myDoughnut1 = new Chart(ctxobb, obbligatori);
	var ctxdes = document.getElementById("graphDes").getContext("2d");
	window.myDoughnut2 = new Chart(ctxdes, desiderabili);
	var ctxopz = document.getElementById("graphOpz").getContext("2d");
	window.myDoughnut3 = new Chart(ctxopz, opzionali);
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
			$('#graphics canvas').show();
			$('#graphics div').show();
			$('#graphics a').html('Nascondi copertura dei requisiti');
		}
	});
});