// resources/js/partographCharts.js
import { Chart, registerables } from 'chart.js';
import annotationPlugin from 'chartjs-plugin-annotation';
import { httpRequest } from '../httpHelpers';

// Register Chart.js components and annotation plugin
Chart.register(...registerables, annotationPlugin);

// Custom plugin to draw lasting_seconds above bars
const lastingTextPlugin = {
    id: 'lastingText',
    afterDatasetsDraw(chart) {
         // Check if plugin is enabled for this chart
        if (!chart.options.plugins.lastingText?.enableLastingText) return;
        const { ctx, data, scales } = chart;
        const dataset = data.datasets[0];
        if (!dataset || !dataset.data) return;

        ctx.save();
        ctx.font = 'bold 10px Arial';
        ctx.fillStyle = 'black';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'bottom';

        dataset.data.forEach((point, index) => {
            if (!point.y || point.y < 1) return; // Skip bars with count < 1

            const lasting = point.lasting !== null ? point.lasting+'secs' : 'N/A';
            const x = scales.x.getPixelForValue(point.x);
            const y = scales.y.getPixelForValue(point.y);
            ctx.fillText(lasting, x, y - 4); // Draw text 5px above bar
        });

        ctx.restore();
    }
};

// Register the custom plugin
Chart.register(lastingTextPlugin);

// Store the current labourRecordId to track changes
let currentLabourRecordId = null;
let chartsInstance = null;

async function getPartographCharts(modal, labourRecordId) {
    // Check if labourRecordId has changed (i.e., different patient)
    if (currentLabourRecordId !== labourRecordId && chartsInstance) {
        chartsInstance.destroyAllCharts();
        chartsInstance = null;
        currentLabourRecordId = null;
    }

    // If chartsInstance already exists, just update the charts
    if (chartsInstance) {
        await chartsInstance.updateCharts();
        return chartsInstance;
    }

    // Fetch initial data
    const data = await httpRequest(
        `/partograph/load/chart`,
        'GET',
        { params: { labourRecordId } },
        'Failed to fetch chart data'
    );

    // Store the current labourRecordId
    currentLabourRecordId = labourRecordId;

    // Object to hold update functions and charts
    const chartManager = {
        charts: {},
        updateFunctions: {},
        async updateCharts() {
            // Fetch fresh data
            const freshData = await httpRequest(
                `/partograph/load/chart`,
                'GET',
                { params: { labourRecordId: currentLabourRecordId } },
                'Failed to fetch chart data'
            );
           
            // Call each update function with the fresh data
            this.updateFunctions.updateCervicalDescentData(freshData);
            this.updateFunctions.updateFetalHeartRateData(freshData);
            this.updateFunctions.updateContractionsData(freshData);
        },
        destroyAllCharts() {
            Object.values(this.charts).forEach(chart => chart.destroy());
            this.charts = {};
        }
    };

    // Function to create Cervical Descent chart
    function createCervicalDescentChart() {
        const cervixChart = modal._element.querySelector('#cervicalDescentChart');
        const CervicalDescentChart = new Chart(cervixChart, {
            type: 'line',
            data: {
                datasets: [
                    {
                        label: 'Cervical Dilation (cm)',
                        data: [],
                        borderWidth: 1,
                        borderColor: '#0d6efd',
                        backgroundColor: '#0d6efd',
                        tension: 0.5,
                        pointRadius: 5,
                        spanGaps: true
                    },
                    {
                        label: 'Descent (fifths)',
                        data: [],
                        borderWidth: 1,
                        borderColor: '#eebb3a',
                        backgroundColor: '#eebb3a',
                        tension: 0.5,
                        pointRadius: 5,
                        spanGaps: true
                    }
                ]
            },
            options: {
                scales: {
                    x: {
                        type: 'linear',
                        title: { display: true, text: 'Hours since first observation' },
                        min: 0,
                        max: 18,
                        ticks: { stepSize: 1 }
                    },
                    y: {
                        beginAtZero: true,
                        suggestedMin: 0,
                        suggestedMax: 10,
                        title: { display: true, text: 'Dilation (cm) / Descent (fifths)' },
                        ticks: { stepSize: 1 }
                    }
                },
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.dataset.label;
                                const value = context.parsed.y;
                                return `${label}: ${value}${label.includes('Dilation') ? ' cm' : ' fifths'}`;
                            }
                        }
                    },
                    annotation: {
                        annotations: {
                            alertLine: {
                                type: 'line',
                                yMin: 4,
                                yMax: 8,
                                xMin: 0,
                                xMax: 4,
                                borderColor: 'orange',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                label: {
                                    display: true,
                                    content: 'Alert Line',
                                    position: 'center',
                                    backgroundColor: 'rgba(255, 165, 0, 0.8)',
                                    color: 'black',
                                    padding: 6,
                                    font: { size: 12 },
                                    borderRadius: 4,
                                    yAdjust: -10
                                }
                            },
                            actionLine: {
                                type: 'line',
                                yMin: 4,
                                yMax: 8, 
                                xMin: 4,
                                xMax: 8,
                                borderColor: 'red',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                label: {
                                    display: true,
                                    content: 'Action Line',
                                    position: 'center',
                                    backgroundColor: 'rgba(255, 0, 0, 0.8)',
                                    color: 'black',
                                    padding: 6,
                                    font: { size: 12 },
                                    borderRadius: 4,
                                    yAdjust: -10
                                }
                            }
                        }
                    }
                }
            }
        });

        function updateCervicalDescentData(data) {
            if (!data.length) return;

            const relevantData = data.filter(item =>
                item.parameterType === 'cervical_dilation' || item.parameterType === 'descent'
            );
            if (!relevantData.length) return;

            const dates = relevantData.map(item => new Date(item.recordedAtRaw)).filter(date => !isNaN(date));
            if (!dates.length) return;
            const minDate = dates.reduce((min, date) => (date < min ? date : min), dates[0]);

            const dilationData = [];
            const descentData = [];

            relevantData.forEach(item => {
                const date = new Date(item.recordedAtRaw);
                if (isNaN(date)) {
                    console.warn(`Invalid recordedAtRaw: ${item.recordedAtRaw}`);
                    return;
                }
                const hours = (date - minDate) / (1000 * 60 * 60);
                if (item.parameterType === 'cervical_dilation' && item.value && item.value.cm != null) {
                    dilationData.push({ x: hours, y: parseFloat(item.value.cm) });
                } else if (item.parameterType === 'descent' && item.value && item.value.fifths) {
                    descentData.push({ x: hours, y: parseInt(item.value.fifths.split('/')[0]) });
                }
            });

            dilationData.sort((a, b) => a.x - b.x);
            descentData.sort((a, b) => a.x - b.x);
            CervicalDescentChart.data.datasets[0].data = dilationData;
            CervicalDescentChart.data.datasets[1].data = descentData;
            CervicalDescentChart.update();
        }

        chartManager.updateFunctions.updateCervicalDescentData = updateCervicalDescentData;
        updateCervicalDescentData(data);
        return CervicalDescentChart;
    }

    function createFetalHeartRateChart() {
        const fhrChart = modal._element.querySelector('#fetalHeartRateChart');
        const FetalHeartRateChart = new Chart(fhrChart, {
            type: 'line',
            data: {
                datasets: [
                    {
                        label: 'Fetal Heart Rate (bpm)',
                        data: [],
                        borderWidth: 1,
                        borderColor: '#28a745',
                        backgroundColor: '#28a745',
                        tension: 0.5,
                        pointRadius: 5,
                        spanGaps: true
                    }
                ]
            },
            options: {
                scales: {
                    x: {
                        type: 'linear',
                        title: { display: true, text: 'Hours since first observation' },
                        min: 0,
                        max: 18,
                        ticks: { stepSize: 1 }
                    },
                    y: {
                        suggestedMin: 60,
                        suggestedMax: 180,
                        title: { display: true, text: 'Heart Rate (bpm)' },
                        ticks: { stepSize: 10 }
                    }
                },
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${context.parsed.y} bpm`;
                            }
                        }
                    }
                }
            }
        });

        function updateFetalHeartRateData(data) {
            if (!data.length) return;

            const relevantData = data.filter(item => item.parameterType === 'fetal_heart_rate');
            if (!relevantData.length) return;

            const dates = relevantData.map(item => new Date(item.recordedAtRaw)).filter(date => !isNaN(date));
            if (!dates.length) return;
            const minDate = dates.reduce((min, date) => (date < min ? date : min), dates[0]);
            const fhrData = [];
            let maxHours = 0;

            relevantData.forEach(item => {
                const date = new Date(item.recordedAtRaw);
                if (isNaN(date)) {
                    console.warn(`Invalid recordedAtRaw: ${item.recordedAtRaw}`);
                    return;
                }
                const hours = (date - minDate) / (1000 * 60 * 60);
                maxHours = Math.max(maxHours, hours);
                if (item.parameterType === 'fetal_heart_rate' && item.value && item.value.bpm != null) {
                    fhrData.push({ x: hours, y: parseFloat(item.value.bpm) });
                }
            });

            fhrData.sort((a, b) => a.x - b.x);
            // FetalHeartRateChart.options.scales.x.max = maxHours + 1;
            FetalHeartRateChart.data.datasets[0].data = fhrData;
            FetalHeartRateChart.update();
        }

        chartManager.updateFunctions.updateFetalHeartRateData = updateFetalHeartRateData;
        updateFetalHeartRateData(data);
        return FetalHeartRateChart;
    }

    function createContractionsChart() {
        const contractionsChart = modal._element.querySelector('#uterineContractionsChart');
        const ContractionsChart = new Chart(contractionsChart, {
            type: 'bar',
            data: {
                datasets: [
                    {
                        label: 'Contractions (per 10 min)',
                        data: [],
                        backgroundColor: [],
                        borderWidth: 1,
                        barThickness: 10,
                    }
                ]
            },
            options: {
                scales: {
                    x: {
                        type: 'linear',
                        title: { display: true, text: 'Hours since first observation' },
                        min: 0,
                        max: 18,
                        ticks: { stepSize: 1 }
                    },
                    y: {
                        beginAtZero: true,
                        suggestedMin: 0,
                        suggestedMax: 7,
                        title: { display: true, text: 'Contractions (per 10 min)' },
                        ticks: { stepSize: 1 }
                    }
                },
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const { y, lasting, strength } = context.raw;
                                const strengthText = { 'W': 'Weak', 'M': 'Moderate', 'S': 'Strong' }[strength] || strength;
                                return `Contractions: ${y}, Duration: ${lasting || 'N/A'}s, Strength: ${strengthText}`;
                            }
                        }
                    },
                    lastingText: {
                        enableLastingText: true // Enable plugin for this chart
                    }
                }
            }
        });

        function updateContractionsData(data) {
            if (!data.length) return;

            const relevantData = data.filter(item => item.parameterType === 'uterine_contractions');
            if (!relevantData.length) return;

            const dates = relevantData.map(item => new Date(item.recordedAtRaw)).filter(date => !isNaN(date));
            if (!dates.length) return;
            const minDate = dates.reduce((min, date) => (date < min ? date : min), dates[0]);

            const contractionsData = [];
            const backgroundColors = [];
            let maxHours = 0;

            relevantData.forEach(item => {
                const date = new Date(item.recordedAtRaw);
                if (isNaN(date)) {
                    console.warn(`Invalid recordedAtRaw: ${item.recordedAtRaw}`);
                    return;
                }
                const hours = (date - minDate) / (1000 * 60 * 60);
                maxHours = Math.max(maxHours, hours);
                if (item.parameterType === 'uterine_contractions' && item.value && item.value.count_per_10min != null) {
                    const strength = item.value.strength[0] || 'W';
                    const color = {
                        'W': '#ff9999',
                        'M': '#ff3333',
                        'S': '#cc0000'
                    }[strength] || '#ff9999';
                    const lasting = item.value.lasting || null;
                    contractionsData.push({
                        x: hours,
                        y: parseFloat(item.value.count_per_10min),
                        lasting: item.value.lasting_seconds || null,
                        strength
                    });
                    backgroundColors.push(color);
                }
            });

            contractionsData.sort((a, b) => a.x - b.x);
            // ContractionsChart.options.scales.x.max = maxHours + 1;
            ContractionsChart.data.datasets[0].data = contractionsData;
            ContractionsChart.data.datasets[0].backgroundColor = backgroundColors;
            ContractionsChart.update();
        }

        chartManager.updateFunctions.updateContractionsData = updateContractionsData;
        updateContractionsData(data);
        return ContractionsChart;
    }

    // Initialize all charts
    chartManager.charts = {
        cervicalDescent: createCervicalDescentChart(),
        fetalHeartRate: createFetalHeartRateChart(),
        contractions: createContractionsChart()
    };

    // Destroy charts on modal hide
    modal._element.addEventListener('hide.bs.modal', function() {
        chartManager.destroyAllCharts();
        currentLabourRecordId = null;
        chartsInstance = null;
    });

    chartsInstance = chartManager;
    return chartManager;
}

export { getPartographCharts };