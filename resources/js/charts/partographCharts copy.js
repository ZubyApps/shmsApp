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

            const lasting = point.lasting !== null ? point.lasting + 'secs' : 'N/A';
            const x = scales.x.getPixelForValue(point.x);
            const y = scales.y.getPixelForValue(point.y);
            ctx.fillText(lasting, x, y - 4); // Draw text 4px above bar
        });

        ctx.restore();
    }
};

// Custom plugin for observations chart
const observationsTextPlugin = {
    id: 'observationsText',
    afterDatasetsDraw(chart) {
        // Check if plugin is enabled for this chart
        if (!chart.options.plugins.observationsText?.enableObservationsText) return;

        const { ctx, data, scales } = chart;
        const dataset = data.datasets[0];
        if (!dataset || !dataset.data) return;

        ctx.save();
        ctx.font = '13px Arial';
        ctx.fillStyle = 'red';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';

        dataset.data.forEach((point) => {
            const x = scales.x.getPixelForValue(point.x);
            const y = scales.y.getPixelForValue(point.y);
            let text = '';

            switch (point.parameterType) {
                case 'urine':
                    text = point.value.protein ? `P:${point.value.protein} G:${point.value.glucose} V:${point.value.volume+'ml(s)'}` : 'N/A';
                    break;
                case 'caput':
                    text = point.value.degree || '0';
                    break;
                case 'position':
                    text = point.value.position || 'N/A';
                    break;
                case 'moulding':
                    text = point.value.degree || '0';
                    break;
                case 'oxytocin':
                    text = point.value.dosage ? `${point.value.dosage}u` : 'N/A';
                    break;
                case 'fluid':
                    text = point.value.status || 'N/A';
                    break;
                case 'drug':
                    text = point.value.type || 'N/A';
                    break;
                default:
                    text = 'N/A';
            }

            ctx.fillText(text, x, y);
        });

        ctx.restore();
    }
};

// Register the custom plugins
Chart.register(lastingTextPlugin, observationsTextPlugin);

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
            this.updateFunctions.updateObservationsData(freshData);
            this.updateFunctions.updateBloodPressurePulseData(freshData);
            this.updateFunctions.updateTemperatureData(freshData);
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
                        borderWidth: 3,
                        // borderColor: '#0d6efd',
                        backgroundColor: '#0d6efd',
                        tension: 0.5,
                        pointRadius: 5,
                        spanGaps: true
                    },
                    {
                        label: 'Descent (fifths)',
                        data: [],
                        borderWidth: 3,
                        // borderColor: '#eebb3a',
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
                        borderWidth: 3,
                        // borderColor: '#28a745',
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
                        ticks: { stepSize: 0.5 }
                    },
                    y: {
                        suggestedMin: 60,
                        suggestedMax: 200,
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
                    },
                    annotation: {
                        annotations: {
                            alertLine: {
                                type: 'line',
                                yMin: 160,
                                yMax: 160,
                                xMin: 0,
                                xMax: 18,
                                borderColor: 'red',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                label: {
                                    display: true,
                                    content: 'Fetal Distress Alert Line',
                                    position: 'center',
                                    backgroundColor: 'rgba(255, 0, 0, 0.8)',
                                    color: 'white',
                                    padding: 3,
                                    font: { size: 12 },
                                    borderRadius: 4,
                                    yAdjust: -10
                                }
                            },
                            actionLine: {
                                type: 'line',
                                yMin: 120,
                                yMax: 120, 
                                xMin: 0,
                                xMax: 18,
                                borderColor: 'red',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                 label: {
                                    display: true,
                                    content: 'Bradycardia Alert Line',
                                    position: 'center',
                                    backgroundColor: 'rgba(255, 0, 0, 0.8)',
                                    color: 'white',
                                    padding: 3,
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

    function createObservationsChart() {
        const observationsChart = modal._element.querySelector('#observationsChart');
        const ObservationsChart = new Chart(observationsChart, {
            type: 'scatter',
            data: {
                datasets: [
                    {
                        label: 'Maternal & Fetal Observations',
                        data: [],
                        pointRadius: 0, // No default points, use text
                        showLine: false
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
                        type: 'category',
                        labels: ['Drug', 'Fluid', 'Oxytocin', 'Moulding', 'Position', 'Caput', 'Urine'],
                        title: { display: true, text: 'Observations' },
                        reverse: true // Top-to-bottom order
                    }
                },
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const point = context.raw;
                                switch (point.parameterType) {
                                    case 'urine':
                                        return `Urine: Protein ${point.value.protein || 'N/A'}, Glucose ${point.value.glucose || 'N/A'}, Volume ${point.value.volume || 'N/A'}mL`;
                                    case 'caput':
                                        return `Caput: ${point.value.degree || '0'}`;
                                    case 'position':
                                        return `Position: ${point.value.position || 'N/A'}`;
                                    case 'moulding':
                                        return `Moulding: ${point.value.degree || '0'}`;
                                    case 'oxytocin':
                                        return `Oxytocin: ${point.value.dosage || 'N/A'} units`;
                                    case 'fluid':
                                        return `Fluid: ${point.value.status || 'N/A'}`;
                                    case 'drug':
                                        return `Drug: ${point.value.type || 'N/A'}`;
                                    default:
                                        return 'Unknown';
                                }
                            }
                        }
                    },
                    observationsText: {
                        enableObservationsText: true // Enable plugin for this chart
                    }
                }
            }
        });

        function updateObservationsData(data) {
            if (!data.length) return;

            const relevantTypes = ['urine', 'caput', 'position', 'moulding', 'oxytocin', 'fluid', 'drug'];
            const relevantData = data.filter(item => relevantTypes.includes(item.parameterType));
            if (!relevantData.length) return;

            const dates = relevantData.map(item => new Date(item.recordedAtRaw)).filter(date => !isNaN(date));
            if (!dates.length) return;
            const minDate = dates.reduce((min, date) => (date < min ? date : min), dates[0]);

            const observationsData = [];
            let maxHours = 0;

            relevantData.forEach(item => {
                const date = new Date(item.recordedAtRaw);
                if (isNaN(date)) {
                    console.warn(`Invalid recordedAtRaw: ${item.recordedAtRaw}`);
                    return;
                }
                const hours = (date - minDate) / (1000 * 60 * 60);
                maxHours = Math.max(maxHours, hours);
                if (item.value && item.parameterType) {
                    const yValue = {
                        urine: 'Urine',
                        caput: 'Caput',
                        position: 'Position',
                        moulding: 'Moulding',
                        oxytocin: 'Oxytocin',
                        fluid: 'Fluid',
                        drug: 'Drug'
                    }[item.parameterType];
                    observationsData.push({
                        x: hours,
                        y: yValue,
                        parameterType: item.parameterType,
                        value: item.value
                    });
                    // console.log(`Observation at ${hours}h: type=${item.parameterType}, value=`, item.value); // Debug log
                }
            });

            observationsData.sort((a, b) => a.x - b.x);
            ObservationsChart.data.datasets[0].data = observationsData;
            ObservationsChart.update();
        }

        chartManager.updateFunctions.updateObservationsData = updateObservationsData;
        updateObservationsData(data);
        return ObservationsChart;
    }

    function createBloodPressurePulseChart() {
        const bpPulseChart = modal._element.querySelector('#bloodPressurePulseChart');
        const BloodPressurePulseChart = new Chart(bpPulseChart, {
            type: 'line',
            data: {
                datasets: [
                    {
                        label: 'Systolic BP (mmHg)',
                        data: [],
                        borderWidth: 3,
                        // borderColor: '#ff0000',
                        backgroundColor: '#0d6efd',
                        tension: 0.5,
                        pointRadius: 5,
                        pointBackgroundColor: (context) => {
                            const point = context.dataset.data[context.dataIndex];
                            return point && point.y > 140 ? "#dc3545" : "#0d6efd";
                        },
                        spanGaps: true,
                        yAxisID: 'y-bp'
                    },
                    {
                        label: 'Diastolic BP (mmHg)',
                        data: [],
                        borderWidth: 3,
                        // borderColor: '#0000ff',
                        backgroundColor: '#0d6efd',
                        tension: 0.5,
                        pointRadius: 5,
                        pointBackgroundColor: (context) => {
                            const point = context.dataset.data[context.dataIndex];
                            return point && point.y > 90 ? "#dc3545" : "#0d6efd";
                        },
                        spanGaps: true,
                        yAxisID: 'y-bp'
                    },
                    {
                        label: 'Pulse (bpm)',
                        data: [],
                        borderWidth: 3,
                        // borderColor: '#00cc00',
                        backgroundColor: '#00cc00',
                        tension: 0.5,
                        pointRadius: 5,
                        spanGaps: true,
                        yAxisID: 'y-pulse'
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
                    'y-bp': {
                        position: 'left',
                        suggestedMin: 40,
                        suggestedMax: 200,
                        title: { display: true, text: 'Blood Pressure (mmHg)' },
                        ticks: { stepSize: 20 }
                    },
                    'y-pulse': {
                        position: 'right',
                        suggestedMin: 40,
                        suggestedMax: 120,
                        title: { display: true, text: 'Pulse (bpm)' },
                        ticks: { stepSize: 10 },
                        grid: { drawOnChartArea: false } // Avoid overlapping grid lines
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
                                return `${label}: ${value}${label.includes('BP') ? ' mmHg' : ' bpm'}`;
                            }
                        }
                    },
                    annotation: {
                        annotations: {
                            alertLine: {
                                type: 'line',
                                yMin: 140,
                                yMax: 140,
                                xMin: 0,
                                xMax: 18,
                                borderColor: 'red',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                label: {
                                    display: true,
                                    content: 'Preeclampsia Systolic Alert Line',
                                    position: 'center',
                                    backgroundColor: 'rgba(255, 0, 0, 0.8)',
                                    color: 'white',
                                    padding: 3,
                                    font: { size: 12 },
                                    borderRadius: 4,
                                    yAdjust: -10
                                }
                            },
                            actionLine: {
                                type: 'line',
                                yMin: 90,
                                yMax: 90, 
                                xMin: 0,
                                xMax: 18,
                                borderColor: 'red',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                label: {
                                    display: true,
                                    content: 'Preeclampsia Diastolic Alert Line',
                                    position: 'center',
                                    backgroundColor: 'rgba(255, 0, 0, 0.8)',
                                    color: 'white',
                                    padding: 3,
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

        function updateBloodPressurePulseData(data) {
            if (!data.length) return;

            const relevantData = data.filter(item =>
                item.parameterType === 'blood_pressure' || item.parameterType === 'pulse'
            );
            if (!relevantData.length) return;

            const dates = relevantData.map(item => new Date(item.recordedAtRaw)).filter(date => !isNaN(date));
            if (!dates.length) return;
            const minDate = dates.reduce((min, date) => (date < min ? date : min), dates[0]);

            const systolicData = [];
            const diastolicData = [];
            const pulseData = [];

            relevantData.forEach(item => {
                const date = new Date(item.recordedAtRaw);
                if (isNaN(date)) {
                    console.warn(`Invalid recordedAtRaw: ${item.recordedAtRaw}`);
                    return;
                }
                const hours = (date - minDate) / (1000 * 60 * 60);
                if (item.parameterType === 'blood_pressure' && item.value) {
                    if (item.value.systolic != null) {
                        systolicData.push({ x: hours, y: parseFloat(item.value.systolic) });
                    }
                    if (item.value.diastolic != null) {
                        diastolicData.push({ x: hours, y: parseFloat(item.value.diastolic) });
                    }
                } else if (item.parameterType === 'pulse' && item.value && item.value.bpm != null) {
                    pulseData.push({ x: hours, y: parseFloat(item.value.bpm) });
                }
            });

            systolicData.sort((a, b) => a.x - b.x);
            diastolicData.sort((a, b) => a.x - b.x);
            pulseData.sort((a, b) => a.x - b.x);

            BloodPressurePulseChart.data.datasets[0].data = systolicData;
            BloodPressurePulseChart.data.datasets[1].data = diastolicData;
            BloodPressurePulseChart.data.datasets[2].data = pulseData;
            BloodPressurePulseChart.update();
        }

        chartManager.updateFunctions.updateBloodPressurePulseData = updateBloodPressurePulseData;
        updateBloodPressurePulseData(data);
        return BloodPressurePulseChart;
    }

    function createTemperatureChart() {
        const feverBenchmark = document.querySelector('#feverBenchMark').value ?? 37.3 
        const tempChart = modal._element.querySelector('#temperatureChart');
        const TemperatureChart = new Chart(tempChart, {
            type: 'line',
            data: {
                datasets: [
                    {
                        label: 'Temperature (°C)',
                        data: [],
                        borderWidth: 3,
                        // borderColor: '#0d6efd',
                        backgroundColor: ["#0d6efd"],
                        tension: 0.5,
                        pointRadius: 5,
                        spanGaps: true,
                        pointBackgroundColor: (context) => {
                            const tempPoint = context.dataset.data[context.dataIndex];
                            const tempValue = tempPoint && typeof tempPoint === 'object' ? tempPoint.y : tempPoint;
                            return tempValue >= feverBenchmark ? "#dc3545" : "#0d6efd";
                        },
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
                        suggestedMin: 35,
                        suggestedMax: 40,
                        title: { display: true, text: 'Temperature (°C)' },
                        ticks: { stepSize: 0.1 }
                    }
                },
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${context.parsed.y} °C`;
                            }
                        }
                    }
                }
            }
        });

        function updateTemperatureData(data) {
            if (!data.length) return;

            const relevantData = data.filter(item => item.parameterType === 'temperature');
            if (!relevantData.length) return;

            const dates = relevantData.map(item => new Date(item.recordedAtRaw)).filter(date => !isNaN(date));
            if (!dates.length) return;
            const minDate = dates.reduce((min, date) => (date < min ? date : min), dates[0]);

            const tempData = [];

            relevantData.forEach(item => {
                const date = new Date(item.recordedAtRaw);
                if (isNaN(date)) {
                    console.warn(`Invalid recordedAtRaw: ${item.recordedAtRaw}`);
                    return;
                }
                const hours = (date - minDate) / (1000 * 60 * 60);
                if (item.parameterType === 'temperature' && item.value && item.value.celsius != null) {
                    tempData.push({ x: hours, y: parseFloat(item.value.celsius) });
                }
            });

            tempData.sort((a, b) => a.x - b.x);
            TemperatureChart.data.datasets[0].data = tempData;
            TemperatureChart.update();
        }

        chartManager.updateFunctions.updateTemperatureData = updateTemperatureData;
        updateTemperatureData(data);
        return TemperatureChart;
    }

    // Initialize all charts
    chartManager.charts = {
        cervicalDescent: createCervicalDescentChart(),
        fetalHeartRate: createFetalHeartRateChart(),
        contractions: createContractionsChart(),
        observations: createObservationsChart(),
        bloodPressurePulse: createBloodPressurePulseChart(),
        temperature: createTemperatureChart()
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