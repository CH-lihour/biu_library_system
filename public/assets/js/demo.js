"use strict";

//Chart

// Only present on the dashboard.
if (document.getElementById('statisticsChart')) {
	var ctx = document.getElementById('statisticsChart').getContext('2d');

	var statisticsChart = window.statisticsChart = new Chart(ctx, {
		type: 'line',
		data: {
			labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
			datasets: [ {
				label: "Subscribers",
				borderColor: '#f3545d',
				pointBackgroundColor: 'rgba(243, 84, 93, 0.6)',
				pointRadius: 0,
				backgroundColor: 'rgba(243, 84, 93, 0.4)',
				legendColor: '#f3545d',
				fill: true,
				borderWidth: 2,
				data: [154, 184, 175, 203, 210, 231, 240, 278, 252, 312, 320, 374]
			}, {
				label: "New Visitors",
				borderColor: '#fdaf4b',
				pointBackgroundColor: 'rgba(253, 175, 75, 0.6)',
				pointRadius: 0,
				backgroundColor: 'rgba(253, 175, 75, 0.4)',
				legendColor: '#fdaf4b',
				fill: true,
				borderWidth: 2,
				data: [256, 230, 245, 287, 240, 250, 230, 295, 331, 431, 456, 521]
			}, {
				label: "Active Users",
				borderColor: '#177dff',
				pointBackgroundColor: 'rgba(23, 125, 255, 0.6)',
				pointRadius: 0,
				backgroundColor: 'rgba(23, 125, 255, 0.4)',
				legendColor: '#177dff',
				fill: true,
				borderWidth: 2,
				data: [542, 480, 430, 550, 530, 453, 380, 434, 568, 610, 700, 900]
			}]
		},
		options : {
			responsive: true, 
			maintainAspectRatio: false,
			legend: {
				display: false
			},
			tooltips: {
				bodySpacing: 4,
				mode:"nearest",
				intersect: 0,
				position:"nearest",
				xPadding:10,
				yPadding:10,
				caretPadding:10
			},
			layout:{
				padding:{left:5,right:5,top:15,bottom:15}
			},
			scales: {
				yAxes: [{
					ticks: {
						fontStyle: "500",
						beginAtZero: false,
						maxTicksLimit: 5,
						padding: 10
					},
					gridLines: {
						drawTicks: false,
						display: false
					}
				}],
				xAxes: [{
					gridLines: {
						zeroLineColor: "transparent"
					},
					ticks: {
						padding: 10,
						fontStyle: "500"
					}
				}]
			}, 
			legendCallback: function(chart) { 
				var text = []; 
				text.push('<ul class="' + chart.id + '-legend html-legend">'); 
				for (var i = 0; i < chart.data.datasets.length; i++) { 
					text.push('<li><span style="background-color:' + chart.data.datasets[i].legendColor + '"></span>'); 
					if (chart.data.datasets[i].label) { 
						text.push(chart.data.datasets[i].label); 
					} 
					text.push('</li>'); 
				} 
				text.push('</ul>'); 
				return text.join(''); 
			}  
		}
	});
}

// Legend needs both its container and the chart it describes.
if (document.getElementById('myChartLegend') && window.statisticsChart) {
	var myLegendContainer = document.getElementById("myChartLegend");

	// generate HTML legend
	myLegendContainer.innerHTML = statisticsChart.generateLegend();

	// bind onClick event to all LI-tags of the legend
	var legendItems = myLegendContainer.getElementsByTagName('li');
	for (var i = 0; i < legendItems.length; i += 1) {
		legendItems[i].addEventListener("click", legendClickCallback, false);
	}
}

// Only present on the dashboard.
if (document.getElementById('dailySalesChart')) {
	var dailySalesChart = document.getElementById('dailySalesChart').getContext('2d');

	var myDailySalesChart = new Chart(dailySalesChart, {
		type: 'line',
		data: {
			labels:["January",
			"February",
			"March",
			"April",
			"May",
			"June",
			"July",
			"August",
			"September"],
			datasets:[ {
				label: "Sales Analytics", fill: !0, backgroundColor: "rgba(255,255,255,0.2)", borderColor: "#fff", borderCapStyle: "butt", borderDash: [], borderDashOffset: 0, pointBorderColor: "#fff", pointBackgroundColor: "#fff", pointBorderWidth: 1, pointHoverRadius: 5, pointHoverBackgroundColor: "#fff", pointHoverBorderColor: "#fff", pointHoverBorderWidth: 1, pointRadius: 1, pointHitRadius: 5, data: [65, 59, 80, 81, 56, 55, 40, 35, 30]
			}]
		},
		options : {
			maintainAspectRatio:!1, legend: {
				display: !1
			}
			, animation: {
				easing: "easeInOutBack"
			}
			, scales: {
				yAxes:[ {
					display:!1, ticks: {
						fontColor: "rgba(0,0,0,0.5)", fontStyle: "bold", beginAtZero: !0, maxTicksLimit: 10, padding: 0
					}
					, gridLines: {
						drawTicks: !1, display: !1
					}
				}
				], xAxes:[ {
					display:!1, gridLines: {
						zeroLineColor: "transparent"
					}
					, ticks: {
						padding: -20, fontColor: "rgba(255,255,255,0.2)", fontStyle: "bold"
					}
				}
				]
			}
		}
	});
}
