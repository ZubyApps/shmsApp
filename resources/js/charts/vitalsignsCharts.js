import { Chart, registerables } from "chart.js";
Chart.register(...registerables)

function getVitalsignsChartByVisit(chart, vitals, modal){   
    const feverBenchmark = document.querySelector('#feverBenchMark').value ?? 37.3 
    const vitalsignsChart = new Chart(chart, {
        type: 'line',
        data: {
            labels: vitals.data.map((row, index, data) => {return index + 1}),
            datasets: [
                {
            label: `Temperature Chart`,
            data: vitals.data.map(row => row.temperature?.replace('°C', '')),
            borderWidth: 4,
            backgroundColor: ["#0d6efd"],
            tension: 0.5,
            pointRadius: 5,
            pointBackgroundColor: (context) => {
                return context.dataset.data[context.dataIndex] >= feverBenchmark ? "#dc3545" : "#0d6efd";
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

function getAncVitalsignsChart(chart, vitals, modal){    
    const ancVitalsignsChart = new Chart(chart, {
        type: 'line',
        data: {
            labels: vitals.data.map((row, index, data) => {return index + 1}),
            datasets: [
                {
            label: `Blood Pressure Chart`,
            data: vitals.data.map(row => row.bloodPressure?.split('/')[0]),
            borderWidth: 4,
            backgroundColor: ["#0d6efd"],
            tension: 0.5,
            pointRadius: 5,
            pointBackgroundColor: (context) => {
                return context.dataset.data[context.dataIndex] > 139 ? "#dc3545" : "#0d6efd";
            }
            },
        ]
        },
        options: {
            scales: {
            y: {
                beginAtZero: false,
                suggestedMin: 50,
                suggestedMax: 250,
                ticks: {
                    stepSize: 10
                }
                }
            },
            responsive: true,
            maintainAspectRatio: false
        }
        }); 

        modal._element.addEventListener('hide.bs.modal', function() {
            ancVitalsignsChart.destroy()
            
        })
}

function getYearlySummaryChart(chart, data){
    const yearlySummaryChart = new Chart(chart, {
        type: 'bar',
        data: {
            labels: data.data.map((row, index, data) => {return row.month_name}),
            datasets: [
                {
                    label: `Total monthly Bills Made`,
                    data: data.data.map(row => row.bill),
                },
                {
                    label: `Total monthly Bills Paid`,
                    data: data.data.map(row => row.paid),
                },
                {
                    label: `Total monthly Expenses`,
                    data: data.data.map(row => row.expense),
                }
        ]
        },
        options: {
            scales: {
            y: {
                beginAtZero: true,
                }
            },
            responsive: true,
            maintainAspectRatio: false
        }
        });
    return yearlySummaryChart
}

function getYearlySummaryChart2(chart, data){
    const yearlySummaryChart2 = new Chart(chart, {
        type: 'bar',
        data: {
            labels: data.data.map((row, index, data) => {return row.month_name}),
            datasets: [
                {
                    label: `Total From Cash and HMO`,
                    data: data.data.map(row => +row.cashPaid + +row.paidHmo),
                },
                {
                    label: `Total monthly Expenses`,
                    data: data.data.map(row => row.expense),
                }
        ]
        },
        options: {
            scales: {
            y: {
                beginAtZero: true,
                }
            },
            responsive: true,
            maintainAspectRatio: false
        }
        });
    return yearlySummaryChart2
}

function getYearlySummaryChart3(chart, data){
    const yearlySummaryChart3 = new Chart(chart, {
        type: 'bar',
        data: {
            labels: data.data.map((row, index, data) => {return row.month_name}),
            datasets: [
                {
                    label: `Total Bill`,
                    data: data.data.map(row => +row.bill),
                },
                {
                    label: `Total Received`,
                    data: data.data.map(row => row.cashPaid),
                },
                {
                    label: `Total Expenses`,
                    data: data.data.map(row => row.expense),
                }
        ]
        },
        options: {
            scales: {
            y: {
                beginAtZero: true,
                }
            },
            responsive: true,
            maintainAspectRatio: false
        }
        });
    return yearlySummaryChart3
}

export {getVitalsignsChartByVisit, getAncVitalsignsChart, getYearlySummaryChart, getYearlySummaryChart2, getYearlySummaryChart3}