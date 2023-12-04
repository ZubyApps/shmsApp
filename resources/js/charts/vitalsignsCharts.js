import { Chart, registerables } from "chart.js";
Chart.register(...registerables)

function getVitalsignsChartByVisit(chart, vitals, modal){    
    const vitalsignsChart = new Chart(chart, {
        type: 'line',
        data: {
            labels: vitals.data.map((row, index, data) => {return index + 1}),
            datasets: [
                {
            label: `Temperature Chart`,
            data: vitals.data.map(row => row.temperature.replace('C', '')),
            borderWidth: 4,
            backgroundColor: ["#0d6efd"],
            tension: 0.5,
            pointRadius: 5,
            pointBackgroundColor: (context) => {
                return context.dataset.data[context.dataIndex] > 37.2 ? "#dc3545" : "#0d6efd";
            }
            },
        ]
        },
        options: {
            scales: {
            y: {
                beginAtZero: false,
                suggestedMin: 35.0,
                suggestedMax: 41.0,
                ticks: {
                    stepSize: 0.1
                }
                }
            },
            responsive: true,
            maintainAspectRatio: false
        }
        }); 

        modal._element.addEventListener('hide.bs.modal', function() {
            vitalsignsChart.destroy()
            
        })
}

export {getVitalsignsChartByVisit}