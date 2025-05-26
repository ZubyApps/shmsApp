// resources/js/partographCharts.js
import { Chart, registerables } from 'chart.js';
import annotationPlugin from 'chartjs-plugin-annotation';
import { httpRequest } from '../httpHelpers';
import 'chartjs-adapter-dayjs-4/dist/chartjs-adapter-dayjs-4.esm';

// Register Chart.js components and annotation plugin
Chart.register(...registerables, annotationPlugin);

// Custom plugin to draw lasting_seconds above bars
const lastingTextPlugin = {
    id: 'lastingText',
    afterDatasetsDraw(chart) {
        if (!chart.options.plugins.lastingText?.enableLastingText) return;

        const { ctx, data, scales } = chart;
        const dataset = data.datasets[0];
        if (!dataset || !dataset.data) return;

        dataset.data.forEach((point) => {
            // Skip if point.y is invalid or less than 1
            if (!point.y || point.y < 1) return;

            // Save context state for this point
            ctx.save();

            // Set font and alignment for this point
            ctx.font = 'bold 10px Arial';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'bottom';

            // Set color based on lasting > 60
            const lasting = !isNaN(point.lasting) && point.lasting !== null 
                ? `${point.lasting}secs` 
                : 'N/A';
                const pointLasting = parseInt(point.lasting)
                if (pointLasting > 60){
                    ctx.fillStyle = 'red';
                    ctx.font = 'bold 15px Arial';
                }

            // Draw the text
            const x = scales.x.getPixelForValue(point.x);
            const y = scales.y.getPixelForValue(point.y);
            ctx.fillText(lasting, x, y - 4);

            // Restore context state to avoid affecting subsequent drawings
            ctx.restore();
        });
    }
};

// Custom plugin for observations chart
const observationsTextPlugin = {
    id: 'observationsText',
    afterDatasetsDraw(chart) {
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
    if (currentLabourRecordId !== labourRecordId && chartsInstance) {
        chartsInstance.destroyAllCharts();
        chartsInstance = null;
        currentLabourRecordId = null;
    }

    if (chartsInstance) {
        await chartsInstance.updateCharts();
        return chartsInstance;
    }

    const data = await httpRequest(
        `/partograph/load/chart`,
        'GET',
        { params: { labourRecordId } },
        'Failed to fetch chart data'
    );

    currentLabourRecordId = labourRecordId;

    const chartManager = {
        charts: {},
        updateFunctions: {},
        async updateCharts() {
            const freshData = await httpRequest(
                `/partograph/load/chart`,
                'GET',
                { params: { labourRecordId: currentLabourRecordId } },
                'Failed to fetch chart data'
            );
            this.updateFunctions.updateCervicalDescentData?.(freshData);
            this.updateFunctions.updateFetalHeartRateData?.(freshData);
            this.updateFunctions.updateContractionsData?.(freshData);
            this.updateFunctions.updateObservationsData?.(freshData);
            this.updateFunctions.updateBloodPressurePulseData?.(freshData);
            this.updateFunctions.updateTemperatureData?.(freshData);
        },
        destroyAllCharts() {
            Object.values(this.charts).forEach(chart => {
                if (chart) chart.destroy();
            });
            this.charts = {};
        }
    };

    // Helper function to get min/max dates for specific parameter types
    function getTimeBounds(data, relevantTypes) {
        const dates = data
            .filter(item => relevantTypes.includes(item.parameterType))
            .map(item => new Date(item.recordedAtRaw))
            .filter(date => !isNaN(date));
            console.log(data)
            console.log(!dates.length, relevantTypes)
            console.log(dates)
        if (!dates.length){ 
            const now = new Date();
            return { minDate: now, maxDate: new Date(now.getTime() + 14 * 60 * 60 * 1000) };
        };
        const minDate = dates.reduce((min, date) => (date < min ? date : min), dates[0]);
        const maxDate = new Date(minDate.getTime() + 14 * 60 * 60 * 1000); // 14 hours
        return { minDate, maxDate };
    }

    // Helper function to generate exact tick values
    function generateTicks(minDate, maxDate, intervalMinutes) {
        if (!minDate || !maxDate) return [];
        const ticks = [];
        let current = new Date(minDate);
        while (current <= maxDate) {
            ticks.push(new Date(current));
            current.setMinutes(current.getMinutes() + intervalMinutes);
        }
        return ticks;
    }

    // Cervical Descent Chart
    // function createCervicalDescentChart() {
    //     const cervixChart = modal._element.querySelector('#cervicalDescentChart');
    //     const { minDate, maxDate } = getTimeBounds(data, ['cervical_dilation', 'descent']);
    //     if (!minDate) return null;

    //     const CervicalDescentChart = new Chart(cervixChart, {
    //         type: 'line',
    //         data: {
    //             datasets: [
    //                 {
    //                     label: 'Cervical Dilation (cm)',
    //                     data: [],
    //                     borderWidth: 3,
    //                     backgroundColor: '#0d6efd',
    //                     pointBackgroundColor: '#0d6efd', // Fill color for star
    //                     pointBorderColor: '#0d6efd', // Border color for visibility
    //                     pointBorderWidth: 1, // Ensure star outline is visible
    //                     pointStyle: 'star',
    //                     tension: 0.5,
    //                     pointRadius: 10, // Slightly larger for better visibility
    //                     spanGaps: true
    //                 },
    //                 {
    //                     label: 'Descent (fifths)',
    //                     data: [],
    //                     borderWidth: 3,
    //                     backgroundColor: '#eebb3a',
    //                     tension: 0.5,
    //                     pointRadius: 5,
    //                     spanGaps: true
    //                 }
    //             ]
    //         },
    //         options: {
    //             scales: {
    //                 x: {
    //                     type: 'time',
    //                     time: {
    //                         unit: 'hour',
    //                         displayFormats: { hour: 'HH:mm' }
    //                     },
    //                     min: minDate,
    //                     max: maxDate,
    //                     ticks: {
    //                         source: 'labels',
    //                         callback: (value) => {
    //                             const date = new Date(value);
    //                             return `${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`;
    //                         }
    //                     },
    //                     afterBuildTicks: (scale) => {
    //                         scale.ticks = generateTicks(minDate, maxDate, 60).map(date => ({ value: date.getTime() }));
    //                     },
    //                     title: { display: true, text: 'Time' }
    //                 },
    //                 y: {
    //                     beginAtZero: true,
    //                     suggestedMin: 0,
    //                     suggestedMax: 10,
    //                     title: { display: true, text: 'Dilation (cm) / Descent (fifths)' },
    //                     ticks: { stepSize: 1 }
    //                 }
    //             },
    //             responsive: true,
    //             maintainAspectRatio: false,
    //             plugins: {
    //                 legend: { display: true },
    //                 tooltip: {
    //                     callbacks: {
    //                         label: function(context) {
    //                             const label = context.dataset.label;
    //                             const value = context.parsed.y;
    //                             return `${label}: ${value}${label.includes('Dilation') ? ' cm' : ' fifths'}`;
    //                         }
    //                     }
    //                 },
    //                 annotation: {
    //                     annotations: {
    //                         alertLine: {
    //                             type: 'line',
    //                             yMin: 4,
    //                             yMax: 8,
    //                             xMin: minDate,
    //                             xMax: new Date(minDate.getTime() + 4 * 60 * 60 * 1000),
    //                             borderColor: 'orange',
    //                             borderWidth: 2,
    //                             borderDash: [5, 5],
    //                             label: {
    //                                 display: true,
    //                                 content: 'Alert Line',
    //                                 position: 'center',
    //                                 backgroundColor: 'rgba(255, 165, 0, 0.8)',
    //                                 color: 'black',
    //                                 padding: 6,
    //                                 font: { size: 12 },
    //                                 borderRadius: 4,
    //                                 yAdjust: -10
    //                             }
    //                         },
    //                         actionLine: {
    //                             type: 'line',
    //                             yMin: 4,
    //                             yMax: 8,
    //                             xMin: new Date(minDate.getTime() + 4 * 60 * 60 * 1000),
    //                             xMax: new Date(minDate.getTime() + 8 * 60 * 60 * 1000),
    //                             borderColor: 'red',
    //                             borderWidth: 2,
    //                             borderDash: [5, 5],
    //                             label: {
    //                                 display: true,
    //                                 content: 'Action Line',
    //                                 position: 'center',
    //                                 backgroundColor: 'rgba(255, 0, 0, 0.8)',
    //                                 color: 'black',
    //                                 padding: 6,
    //                                 font: { size: 12 },
    //                                 borderRadius: 4,
    //                                 yAdjust: -10
    //                             }
    //                         }
    //                     }
    //                 }
    //             }
    //         }
    //     });

    //     function updateCervicalDescentData(data) {
    //         if (!data.length) return;
    //         const relevantData = data.filter(item =>
    //             item.parameterType === 'cervical_dilation' || item.parameterType === 'descent'
    //         );
    //         if (!relevantData.length) return;

    //         const { minDate } = getTimeBounds(relevantData, ['cervical_dilation', 'descent']);
    //         if (!minDate) return;

    //         const dilationData = [];
    //         const descentData = [];

    //         relevantData.forEach(item => {
    //             const date = new Date(item.recordedAtRaw);
    //             if (isNaN(date)) {
    //                 console.warn(`Invalid recordedAtRaw: ${item.recordedAtRaw}`);
    //                 return;
    //             }
    //             if (item.parameterType === 'cervical_dilation' && item.value && item.value.cm != null) {
    //                 dilationData.push({ x: date, y: parseFloat(item.value.cm) });
    //             } else if (item.parameterType === 'descent' && item.value && item.value.fifths) {
    //                 descentData.push({ x: date, y: parseInt(item.value.fifths.split('/')[0]) });
    //             }
    //         });

    //         dilationData.sort((a, b) => a.x - b.x);
    //         descentData.sort((a, b) => a.x - b.x);
    //         CervicalDescentChart.data.datasets[0].data = dilationData;
    //         CervicalDescentChart.data.datasets[1].data = descentData;
    //         CervicalDescentChart.update();
    //     }

    //     chartManager.updateFunctions.updateCervicalDescentData = updateCervicalDescentData;
    //     updateCervicalDescentData(data);
    //     return CervicalDescentChart;
    // }

    // Cervical Descent Chart
function createCervicalDescentChart() {
    const cervixChart = modal._element.querySelector('#cervicalDescentChart');
    const { minDate, maxDate } = getTimeBounds(data, ['cervical_dilation', 'descent']);

    const CervicalDescentChart = new Chart(cervixChart, {
        type: 'line',
        data: {
            datasets: [
                {
                    label: 'Cervical Dilation (cm)',
                    data: [],
                    borderWidth: 3,
                    backgroundColor: '#0d6efd',
                    pointBackgroundColor: '#0d6efd',
                    pointBorderColor: '#0d6efd',
                    pointBorderWidth: 4,
                    pointStyle: 'star',
                    tension: 0.5,
                    pointRadius: 10,
                    pointHoverRadius: 10,
                    pointHitRadius: 10,
                    spanGaps: true
                },
                {
                    label: 'Descent (fifths)',
                    data: [],
                    borderWidth: 3,
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
                    type: 'time',
                    time: {
                        displayFormats: { hour: 'HH:mm' }
                    },
                    min: minDate,
                    max: maxDate,
                    ticks: {
                        source: 'labels',
                        callback: (value) => {
                            const date = new Date(value);
                            return `${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`;
                        }
                    },
                    afterBuildTicks: (scale) => {
                        // Use current min/max from chart options
                        const currentMin = scale.chart.options.scales.x.min;
                        const currentMax = scale.chart.options.scales.x.max;
                        scale.ticks = generateTicks(currentMin, currentMax, 60).map(date => ({ value: date.getTime() }));
                    },
                    title: { display: true, text: 'Time' }
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
                            xMin: minDate,
                            xMax: new Date(minDate.getTime() + 4 * 60 * 60 * 1000),
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
                            xMin: new Date(minDate.getTime() + 4 * 60 * 60 * 1000),
                            xMax: new Date(minDate.getTime() + 8 * 60 * 60 * 1000),
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
        // console.log('updateCervicalDescentData called with:', data); // Debug
        // if (!data.length) {
        //     console.log('No data, skipping update');
        //     return;
        // }
        const relevantData = data.filter(item =>
            item.parameterType === 'cervical_dilation' || item.parameterType === 'descent'
        );
        // if (!relevantData.length) {
        //     console.log('No cervical_dilation/descent data, skipping update');
        //     return;
        // }

        const { minDate, maxDate } = getTimeBounds(relevantData, ['cervical_dilation', 'descent']);
        const dilationData = [];
        const descentData = [];

        relevantData.forEach(item => {
            const date = new Date(item.recordedAtRaw);
            if (isNaN(date)) {
                console.warn(`Invalid recordedAtRaw: ${item.recordedAtRaw}`);
                return;
            }
            if (item.parameterType === 'cervical_dilation' && item.value && item.value.cm != null) {
                dilationData.push({ x: date, y: parseFloat(item.value.cm) });
            } else if (item.parameterType === 'descent' && item.value && item.value.fifths) {
                descentData.push({ x: date, y: parseInt(item.value.fifths.split('/')[0]) });
            }
        });

        dilationData.sort((a, b) => a.x - b.x);
        descentData.sort((a, b) => a.x - b.x);
        // console.log('dilationData:', dilationData, 'descentData:', descentData); // Debug
        // Clone data to ensure reactivity
        CervicalDescentChart.data.datasets[0].data = [...dilationData];
        CervicalDescentChart.data.datasets[1].data = [...descentData];
        // Update x-axis
        CervicalDescentChart.options.scales.x.min = minDate;
        CervicalDescentChart.options.scales.x.max = maxDate;
        // Update annotations
        CervicalDescentChart.options.plugins.annotation.annotations.alertLine.xMin = minDate;
        CervicalDescentChart.options.plugins.annotation.annotations.alertLine.xMax = new Date(minDate.getTime() + 4 * 60 * 60 * 1000);
        CervicalDescentChart.options.plugins.annotation.annotations.actionLine.xMin = new Date(minDate.getTime() + 4 * 60 * 60 * 1000);
        CervicalDescentChart.options.plugins.annotation.annotations.actionLine.xMax = new Date(minDate.getTime() + 8 * 60 * 60 * 1000);
        // console.log('Updating chart with minDate:', minDate, 'maxDate:', maxDate); // Debug
        CervicalDescentChart?.update('active'); // Force full redraw
    }

    chartManager.updateFunctions.updateCervicalDescentData = updateCervicalDescentData;
    updateCervicalDescentData(data);
    return CervicalDescentChart;
}

    // Fetal Heart Rate Chart
    // function createFetalHeartRateChart() {
    //     const fhrChart = modal._element.querySelector('#fetalHeartRateChart');
    //     const { minDate, maxDate } = getTimeBounds(data, ['fetal_heart_rate']);
    //     if (!minDate) return null;

    //     const FetalHeartRateChart = new Chart(fhrChart, {
    //         type: 'line',
    //         data: {
    //             datasets: [
    //                 {
    //                     label: 'Fetal Heart Rate (bpm)',
    //                     data: [],
    //                     borderWidth: 3,
    //                     backgroundColor: '#28a745',
    //                     tension: 0.5,
    //                     pointRadius: 5,
    //                     spanGaps: true
    //                 }
    //             ]
    //         },
    //         options: {
    //             scales: {
    //                 x: {
    //                     type: 'time',
    //                     time: {
    //                         unit: 'minute',
    //                         displayFormats: { minute: 'HH:mm' }
    //                     },
    //                     min: minDate,
    //                     max: maxDate,
    //                     ticks: {
    //                         source: 'labels',
    //                         callback: (value) => {
    //                             const date = new Date(value);
    //                             return `${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`;
    //                         }
    //                     },
    //                     afterBuildTicks: (scale) => {
    //                         scale.ticks = generateTicks(minDate, maxDate, 30).map(date => ({ value: date.getTime() })); // Fixed to 30-minute intervals
    //                     },
    //                     title: { display: true, text: 'Time' }
    //                 },
    //                 y: {
    //                     suggestedMin: 60,
    //                     suggestedMax: 200,
    //                     title: { display: true, text: 'Heart Rate (bpm)' },
    //                     ticks: { stepSize: 10 }
    //                 }
    //             },
    //             responsive: true,
    //             maintainAspectRatio: false,
    //             plugins: {
    //                 legend: { display: true },
    //                 tooltip: {
    //                     callbacks: {
    //                         label: function(context) {
    //                             return `${context.dataset.label}: ${context.parsed.y} bpm`;
    //                         }
    //                     }
    //                 },
    //                 annotation: {
    //                     annotations: {
    //                         alertLine: {
    //                             type: 'line',
    //                             yMin: 160,
    //                             yMax: 160,
    //                             xMin: minDate,
    //                             xMax: maxDate,
    //                             borderColor: 'red',
    //                             borderWidth: 2,
    //                             borderDash: [5, 5],
    //                             label: {
    //                                 display: true,
    //                                 content: 'Fetal Distress Alert Line',
    //                                 position: 'center',
    //                                 backgroundColor: 'rgba(255, 0, 0, 0.8)',
    //                                 color: 'white',
    //                                 padding: 3,
    //                                 font: { size: 12 },
    //                                 borderRadius: 4,
    //                                 yAdjust: -10
    //                             }
    //                         },
    //                         actionLine: {
    //                             type: 'line',
    //                             yMin: 120,
    //                             yMax: 120,
    //                             xMin: minDate,
    //                             xMax: maxDate,
    //                             borderColor: 'red',
    //                             borderWidth: 2,
    //                             borderDash: [5, 5],
    //                             label: {
    //                                 display: true,
    //                                 content: 'Bradycardia Alert Line',
    //                                 position: 'center',
    //                                 backgroundColor: 'rgba(255, 0, 0, 0.8)',
    //                                 color: 'white',
    //                                 padding: 3,
    //                                 font: { size: 12 },
    //                                 borderRadius: 4,
    //                                 yAdjust: -10
    //                             }
    //                         }
    //                     }
    //                 }
    //             }
    //         }
    //     });

    //     function updateFetalHeartRateData(data) {
    //         if (!data.length) return;
    //         const relevantData = data.filter(item => item.parameterType === 'fetal_heart_rate');
    //         if (!relevantData.length) return;

    //         const { minDate } = getTimeBounds(relevantData, ['fetal_heart_rate']);
    //         if (!minDate) return;

    //         const fhrData = [];
    //         relevantData.forEach(item => {
    //             const date = new Date(item.recordedAtRaw);
    //             if (isNaN(date)) {
    //                 console.warn(`Invalid recordedAtRaw: ${item.recordedAtRaw}`);
    //                 return;
    //             }
    //             if (item.parameterType === 'fetal_heart_rate' && item.value && item.value.bpm != null) {
    //                 fhrData.push({ x: date, y: parseFloat(item.value.bpm) });
    //             }
    //         });

    //         fhrData.sort((a, b) => a.x - b.x);
    //         FetalHeartRateChart.data.datasets[0].data = fhrData;
    //         FetalHeartRateChart.update();
    //     }

    //     chartManager.updateFunctions.updateFetalHeartRateData = updateFetalHeartRateData;
    //     updateFetalHeartRateData(data);
    //     return FetalHeartRateChart;
    // }

    // Fetal Heart Rate Chart
function createFetalHeartRateChart() {
    const fhrChart = modal._element.querySelector('#fetalHeartRateChart');
    const { minDate, maxDate } = getTimeBounds(data, ['fetal_heart_rate']);

    const FetalHeartRateChart = new Chart(fhrChart, {
        type: 'line',
        data: {
            datasets: [
                {
                    label: 'Fetal Heart Rate (bpm)',
                    data: [],
                    borderWidth: 3,
                    // backgroundColor: '#28a745',
                    pointBackgroundColor: (context) => {
                        const y = context.dataset.data[context.dataIndex]?.y;
                        return y >= 160 || y <= 120 ? "#dc3545" : "#28a745";
                    },
                    tension: 0.5,
                    pointRadius: 5,
                    spanGaps: true
                }
            ]
        },
        options: {
            scales: {
                x: {
                    type: 'time',
                    time: { displayFormats: { minute: 'HH:mm' } },
                    min: minDate,
                    max: maxDate,
                    ticks: {
                        source: 'labels',
                        callback: (value) => {
                            const date = new Date(value);
                            return `${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`;
                        },
                        maxTicksLimit: 40
                    },
                    afterBuildTicks: (scale) => {
                        // Use current min/max from chart options
                        const currentMin = scale.chart.options.scales.x.min;
                        const currentMax = scale.chart.options.scales.x.max;
                        scale.ticks = generateTicks(currentMin, currentMax, 30).map(date => ({ value: date.getTime() }));
                    },
                    title: { display: true, text: 'Time' }
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
                            xMin: minDate,
                            xMax: maxDate,
                            borderColor: 'red',
                            borderWidth: 2,
                            borderDash: [5, 5],
                            label: {
                                display: true,
                                content: 'Fetal tachycardia Alert Line',
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
                            xMin: minDate,
                            xMax: maxDate,
                            borderColor: 'red',
                            borderWidth: 2,
                            borderDash: [5, 5],
                            label: {
                                display: true,
                                content: 'Fetal bradycardia Alert Line',
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
        // console.log('updateFetalHeartRateData called with:', data); // Debug
        // if (!data.length) {
        //     console.log('No data, skipping update');
        //     return;
        // }
        const relevantData = data.filter(item => item.parameterType === 'fetal_heart_rate');
        // if (!relevantData.length) {
        //     console.log('No fetal_heart_rate data, skipping update');
        //     return;
        // }

        const { minDate, maxDate } = getTimeBounds(relevantData, ['fetal_heart_rate']);
        const fhrData = [];
        relevantData.forEach(item => {
            const date = new Date(item.recordedAtRaw);
            if (isNaN(date)) {
                console.warn(`Invalid recordedAtRaw: ${item.recordedAtRaw}`);
                return;
            }
            if (item.parameterType === 'fetal_heart_rate' && item.value && item.value.bpm != null) {
                fhrData.push({ x: date, y: parseFloat(item.value.bpm) });
            }
        });

        fhrData.sort((a, b) => a.x - b.x);
        // console.log('fhrData:', fhrData); // Debug
        // Clone data to ensure reactivity
        FetalHeartRateChart.data.datasets[0].data = [...fhrData];
        // Update x-axis
        FetalHeartRateChart.options.scales.x.min = minDate;
        FetalHeartRateChart.options.scales.x.max = maxDate;
        // Update annotations
        FetalHeartRateChart.options.plugins.annotation.annotations.alertLine.xMin = minDate;
        FetalHeartRateChart.options.plugins.annotation.annotations.alertLine.xMax = maxDate;
        FetalHeartRateChart.options.plugins.annotation.annotations.actionLine.xMin = minDate;
        FetalHeartRateChart.options.plugins.annotation.annotations.actionLine.xMax = maxDate;
        // console.log('Updating chart with minDate:', minDate, 'maxDate:', maxDate); // Debug
        FetalHeartRateChart.update('active'); // Force full redraw
    }

    chartManager.updateFunctions.updateFetalHeartRateData = updateFetalHeartRateData;
    updateFetalHeartRateData(data);
    return FetalHeartRateChart;
}

    // Contractions Chart
    // function createContractionsChart() {
    //     const contractionsChart = modal._element.querySelector('#uterineContractionsChart');
    //     const { minDate, maxDate } = getTimeBounds(data, ['uterine_contractions']);
    //     if (!minDate) return null;

    //     const ContractionsChart = new Chart(contractionsChart, {
    //         type: 'bar',
    //         data: {
    //             datasets: [
    //                 {
    //                     label: 'Contractions (per 10 min)',
    //                     data: [],
    //                     backgroundColor: [],
    //                     borderWidth: 1,
    //                     barThickness: 10,
    //                 }
    //             ]
    //         },
    //         options: {
    //             scales: {
    //                 x: {
    //                     type: 'time',
    //                     time: {
    //                         unit: 'hour',
    //                         displayFormats: { hour: 'HH:mm' }
    //                     },
    //                     min: minDate,
    //                     max: maxDate,
    //                     ticks: {
    //                         source: 'labels',
    //                         callback: (value) => {
    //                             const date = new Date(value);
    //                             return `${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`;
    //                         }
    //                     },
    //                     afterBuildTicks: (scale) => {
    //                         scale.ticks = generateTicks(minDate, maxDate, 60).map(date => ({ value: date.getTime() }));
    //                     },
    //                     title: { display: true, text: 'Time' }
    //                 },
    //                 y: {
    //                     beginAtZero: true,
    //                     suggestedMin: 0,
    //                     suggestedMax: 7,
    //                     title: { display: true, text: 'Contractions (per 10 min)' },
    //                     ticks: { stepSize: 1 }
    //                 }
    //             },
    //             responsive: true,
    //             maintainAspectRatio: false,
    //             plugins: {
    //                 legend: { display: true },
    //                 tooltip: {
    //                     callbacks: {
    //                         label: function(context) {
    //                             const { y, lasting, strength } = context.raw;
    //                             const strengthText = { 'W': 'Weak', 'M': 'Moderate', 'S': 'Strong' }[strength] || strength;
    //                             return `Contractions: ${y}, Duration: ${lasting || 'N/A'}s, Strength: ${strengthText}`;
    //                         }
    //                     }
    //                 },
    //                 lastingText: {
    //                     enableLastingText: true
    //                 }
    //             }
    //         }
    //     });

    //     function updateContractionsData(data) {
    //         if (!data.length) return;
    //         const relevantData = data.filter(item => item.parameterType === 'uterine_contractions');
    //         if (!relevantData.length) return;

    //         const { minDate } = getTimeBounds(relevantData, ['uterine_contractions']);
    //         if (!minDate) return;

    //         const contractionsData = [];
    //         const backgroundColors = [];
    //         relevantData.forEach(item => {
    //             const date = new Date(item.recordedAtRaw);
    //             if (isNaN(date)) {
    //                 console.warn(`Invalid recordedAtRaw: ${item.recordedAtRaw}`);
    //                 return;
    //             }
    //             if (item.parameterType === 'uterine_contractions' && item.value && item.value.count_per_10min != null) {
    //                 const strength = item.value.strength[0] || 'W';
    //                 const color = {
    //                     'W': '#ff9999',
    //                     'M': '#ff3333',
    //                     'S': '#cc0000'
    //                 }[strength] || '#ff9999';
    //                 const lasting = item.value.lasting || null;
    //                 contractionsData.push({
    //                     x: date,
    //                     y: parseFloat(item.value.count_per_10min),
    //                     lasting: item.value.lasting_seconds || null,
    //                     strength
    //                 });
    //                 backgroundColors.push(color);
    //             }
    //         });

    //         contractionsData.sort((a, b) => a.x - b.x);
    //         ContractionsChart.data.datasets[0].data = contractionsData;
    //         ContractionsChart.data.datasets[0].backgroundColor = backgroundColors;
    //         ContractionsChart.update('active');
    //     }

    //     chartManager.updateFunctions.updateContractionsData = updateContractionsData;
    //     updateContractionsData(data);
    //     return ContractionsChart;
    // }

    // Observations Chart
    // function createObservationsChart() {
    //     const observationsChart = modal._element.querySelector('#observationsChart');
    //     const { minDate, maxDate } = getTimeBounds(data, ['urine', 'caput', 'position', 'moulding', 'oxytocin', 'fluid', 'drug']);
    //     if (!minDate) return null;

    //     const ObservationsChart = new Chart(observationsChart, {
    //         type: 'scatter',
    //         data: {
    //             datasets: [
    //                 {
    //                     label: 'Maternal & Fetal Observations',
    //                     data: [],
    //                     pointRadius: 0,
    //                     showLine: false
    //                 }
    //             ]
    //         },
    //         options: {
    //             scales: {
    //                 x: {
    //                     type: 'time',
    //                     time: {
    //                         unit: 'hour',
    //                         displayFormats: { hour: 'HH:mm' }
    //                     },
    //                     min: minDate,
    //                     max: maxDate,
    //                     ticks: {
    //                         source: 'labels',
    //                         callback: (value) => {
    //                             const date = new Date(value);
    //                             return `${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`;
    //                         }
    //                     },
    //                     afterBuildTicks: (scale) => {
    //                         scale.ticks = generateTicks(minDate, maxDate, 60).map(date => ({ value: date.getTime() }));
    //                     },
    //                     title: { display: true, text: 'Time' }
    //                 },
    //                 y: {
    //                     type: 'category',
    //                     labels: ['Drug', 'Fluid', 'Oxytocin', 'Moulding', 'Position', 'Caput', 'Urine'],
    //                     title: { display: true, text: 'Observations' },
    //                     reverse: true
    //                 }
    //             },
    //             responsive: true,
    //             maintainAspectRatio: false,
    //             plugins: {
    //                 legend: { display: false },
    //                 tooltip: {
    //                     callbacks: {
    //                         label: function(context) {
    //                             const point = context.raw;
    //                             switch (point.parameterType) {
    //                                 case 'urine':
    //                                     return `Urine: Protein ${point.value.protein || 'N/A'}, Glucose ${point.value.glucose || 'N/A'}, Volume ${point.value.volume || 'N/A'}mL`;
    //                                 case 'caput':
    //                                     return `Caput: ${point.value.degree || '0'}`;
    //                                 case 'position':
    //                                     return `Position: ${point.value.position || 'N/A'}`;
    //                                 case 'moulding':
    //                                     return `Moulding: ${point.value.degree || '0'}`;
    //                                 case 'oxytocin':
    //                                     return `Oxytocin: ${point.value.dosage || 'N/A'} units`;
    //                                 case 'fluid':
    //                                     return `Fluid: ${point.value.status || 'N/A'}`;
    //                                 case 'drug':
    //                                     return `Drug: ${point.value.type || 'N/A'}`;
    //                                 default:
    //                                     return 'Unknown';
    //                             }
    //                         }
    //                     }
    //                 },
    //                 observationsText: {
    //                     enableObservationsText: true
    //                 }
    //             }
    //         }
    //     });

    //     function updateObservationsData(data) {
    //         if (!data.length) return;
    //         const relevantTypes = ['urine', 'caput', 'position', 'moulding', 'oxytocin', 'fluid', 'drug'];
    //         const relevantData = data.filter(item => relevantTypes.includes(item.parameterType));
    //         if (!relevantData.length) return;

    //         const { minDate } = getTimeBounds(relevantData, relevantTypes);
    //         if (!minDate) return;

    //         const observationsData = [];
    //         relevantData.forEach(item => {
    //             const date = new Date(item.recordedAtRaw);
    //             if (isNaN(date)) {
    //                 console.warn(`Invalid recordedAtRaw: ${item.recordedAtRaw}`);
    //                 return;
    //             }
    //             if (item.value && item.parameterType) {
    //                 const yValue = {
    //                     urine: 'Urine',
    //                     caput: 'Caput',
    //                     position: 'Position',
    //                     moulding: 'Moulding',
    //                     oxytocin: 'Oxytocin',
    //                     fluid: 'Fluid',
    //                     drug: 'Drug'
    //                 }[item.parameterType];
    //                 observationsData.push({
    //                     x: date,
    //                     y: yValue,
    //                     parameterType: item.parameterType,
    //                     value: item.value
    //                 });
    //             }
    //         });

    //         observationsData.sort((a, b) => a.x - b.x);
    //         ObservationsChart.data.datasets[0].data = observationsData;
    //         ObservationsChart.update();
    //     }

    //     chartManager.updateFunctions.updateObservationsData = updateObservationsData;
    //     updateObservationsData(data);
    //     return ObservationsChart;
    // }

    // Blood Pressure and Pulse Chart (Single Y-axis)
    // function createBloodPressurePulseChart() {
    //     const bpPulseChart = modal._element.querySelector('#bloodPressurePulseChart');
    //     const { minDate, maxDate } = getTimeBounds(data, ['blood_pressure', 'pulse']);
    //     if (!minDate) return null;

    //     const BloodPressurePulseChart = new Chart(bpPulseChart, {
    //         type: 'line',
    //         data: {
    //             datasets: [
    //                 {
    //                     label: 'Systolic BP (mmHg)',
    //                     data: [],
    //                     borderWidth: 3,
    //                     borderColor: '#0d6efd',
    //                     backgroundColor: '#0d6efd',
    //                     tension: 0.5,
    //                     pointRadius: 5,
    //                     pointBackgroundColor: (context) => {
    //                         const point = context.dataset.data[context.dataIndex];
    //                         return point && point.y > 140 ? "#dc3545" : "#0d6efd";
    //                     },
    //                     spanGaps: true
    //                 },
    //                 {
    //                     label: 'Diastolic BP (mmHg)',
    //                     data: [],
    //                     borderWidth: 3,
    //                     borderColor: '#4682b4',
    //                     backgroundColor: '#4682b4',
    //                     tension: 0.5,
    //                     pointRadius: 5,
    //                     pointBackgroundColor: (context) => {
    //                         const point = context.dataset.data[context.dataIndex];
    //                         return point && point.y > 90 ? "#dc3545" : "#4682b4";
    //                     },
    //                     spanGaps: true
    //                 },
    //                 {
    //                     label: 'Pulse (bpm)',
    //                     data: [],
    //                     borderWidth: 3,
    //                     borderColor: '#00cc00',
    //                     backgroundColor: '#00cc00',
    //                     tension: 0.5,
    //                     pointRadius: 5,
    //                     pointBackgroundColor: (context) => {
    //                         const point = context.dataset.data[context.dataIndex];
    //                         return point && (point.y > 100 || point.y < 60) ? "#dc3545" : "#00cc00";
    //                     },
    //                     spanGaps: true
    //                 }
    //             ]
    //         },
    //         options: {
    //             scales: {
    //                 x: {
    //                     type: 'time',
    //                     time: {
    //                         unit: 'hour',
    //                         displayFormats: { hour: 'HH:mm' }
    //                     },
    //                     min: minDate,
    //                     max: maxDate,
    //                     ticks: {
    //                         source: 'labels',
    //                         callback: (value) => {
    //                             const date = new Date(value);
    //                             return `${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`;
    //                         }
    //                     },
    //                     afterBuildTicks: (scale) => {
    //                         scale.ticks = generateTicks(minDate, maxDate, 60).map(date => ({ value: date.getTime() }));
    //                     },
    //                     title: { display: true, text: 'Time' }
    //                 },
    //                 y: {
    //                     suggestedMin: 0,
    //                     suggestedMax: 200,
    //                     title: { display: true, text: 'BP (mmHg) / Pulse (bpm)' },
    //                     ticks: { stepSize: 10 }
    //                 }
    //             },
    //             responsive: true,
    //             maintainAspectRatio: false,
    //             plugins: {
    //                 legend: { display: true },
    //                 tooltip: {
    //                     callbacks: {
    //                         label: function(context) {
    //                             const label = context.dataset.label;
    //                             const value = context.parsed.y;
    //                             return `${label}: ${value}${label.includes('BP') ? ' mmHg' : ' bpm'}`;
    //                         }
    //                     }
    //                 },
    //                 annotation: {
    //                     annotations: {
    //                         systolicAlert: {
    //                             type: 'line',
    //                             yMin: 140,
    //                             yMax: 140,
    //                             xMin: minDate,
    //                             xMax: maxDate,
    //                             borderColor: 'red',
    //                             borderWidth: 2,
    //                             borderDash: [5, 5],
    //                             label: {
    //                                 display: true,
    //                                 content: 'Systolic Alert (140 mmHg)',
    //                                 position: 'center',
    //                                 backgroundColor: 'rgba(255, 0, 0, 0.8)',
    //                                 color: 'white',
    //                                 padding: 3,
    //                                 font: { size: 12 },
    //                                 borderRadius: 4,
    //                                 yAdjust: -10
    //                             }
    //                         },
    //                         diastolicAlert: {
    //                             type: 'line',
    //                             yMin: 90,
    //                             yMax: 90,
    //                             xMin: minDate,
    //                             xMax: maxDate,
    //                             borderColor: 'red',
    //                             borderWidth: 2,
    //                             borderDash: [5, 5],
    //                             label: {
    //                                 display: true,
    //                                 content: 'Diastolic Alert (90 mmHg)',
    //                                 position: 'center',
    //                                 backgroundColor: 'rgba(255, 0, 0, 0.8)',
    //                                 color: 'white',
    //                                 padding: 3,
    //                                 font: { size: 12 },
    //                                 borderRadius: 4,
    //                                 yAdjust: -10
    //                             }
    //                         },
    //                         pulseHighAlert: {
    //                             type: 'line',
    //                             yMin: 100,
    //                             yMax: 100,
    //                             xMin: minDate,
    //                             xMax: maxDate,
    //                             borderColor: 'red',
    //                             borderWidth: 2,
    //                             borderDash: [5, 5],
    //                             label: {
    //                                 display: true,
    //                                 content: 'Pulse High Alert (100 bpm)',
    //                                 position: 'center',
    //                                 backgroundColor: 'rgba(255, 0, 0, 0.8)',
    //                                 color: 'white',
    //                                 padding: 3,
    //                                 font: { size: 12 },
    //                                 borderRadius: 4,
    //                                 yAdjust: -10
    //                             }
    //                         },
    //                         pulseLowAlert: {
    //                             type: 'line',
    //                             yMin: 60,
    //                             yMax: 60,
    //                             xMin: minDate,
    //                             xMax: maxDate,
    //                             borderColor: 'red',
    //                             borderWidth: 2,
    //                             borderDash: [5, 5],
    //                             label: {
    //                                 display: true,
    //                                 content: 'Pulse Low Alert (60 bpm)',
    //                                 position: 'center',
    //                                 backgroundColor: 'rgba(255, 0, 0, 0.8)',
    //                                 color: 'white',
    //                                 padding: 3,
    //                                 font: { size: 12 },
    //                                 borderRadius: 4,
    //                                 yAdjust: -10
    //                             }
    //                         }
    //                     }
    //                 }
    //             }
    //         }
    //     });

    //     function updateBloodPressurePulseData(data) {
    //         if (!data.length) return;
    //         const relevantData = data.filter(item =>
    //             item.parameterType === 'blood_pressure' || item.parameterType === 'pulse'
    //         );
    //         if (!relevantData.length) return;

    //         const { minDate } = getTimeBounds(relevantData, ['blood_pressure', 'pulse']);
    //         if (!minDate) return;

    //         const systolicData = [];
    //         const diastolicData = [];
    //         const pulseData = [];

    //         relevantData.forEach(item => {
    //             const date = new Date(item.recordedAtRaw);
    //             if (isNaN(date)) {
    //                 console.warn(`Invalid recordedAtRaw: ${item.recordedAtRaw}`);
    //                 return;
    //             }
    //             if (item.parameterType === 'blood_pressure' && item.value) {
    //                 if (item.value.systolic != null) {
    //                     systolicData.push({ x: date, y: parseFloat(item.value.systolic) });
    //                 }
    //                 if (item.value.diastolic != null) {
    //                     diastolicData.push({ x: date, y: parseFloat(item.value.diastolic) });
    //                 }
    //             } else if (item.parameterType === 'pulse' && item.value && item.value.bpm != null) {
    //                 pulseData.push({ x: date, y: parseFloat(item.value.bpm) });
    //             }
    //         });

    //         systolicData.sort((a, b) => a.x - b.x);
    //         diastolicData.sort((a, b) => a.x - b.x);
    //         pulseData.sort((a, b) => a.x - b.x);

    //         BloodPressurePulseChart.data.datasets[0].data = systolicData;
    //         BloodPressurePulseChart.data.datasets[1].data = diastolicData;
    //         BloodPressurePulseChart.data.datasets[2].data = pulseData;
    //         BloodPressurePulseChart.update();
    //     }

    //     chartManager.updateFunctions.updateBloodPressurePulseData = updateBloodPressurePulseData;
    //     updateBloodPressurePulseData(data);
    //     return BloodPressurePulseChart;
    // }

    // Temperature Chart
    // function createTemperatureChart() {
    //     const feverBenchmark = document.querySelector('#feverBenchMark').value ?? 37.3;
    //     const tempChart = modal._element.querySelector('#temperatureChart');
    //     const { minDate, maxDate } = getTimeBounds(data, ['temperature']);
    //     if (!minDate) return null;

    //     const TemperatureChart = new Chart(tempChart, {
    //         type: 'line',
    //         data: {
    //             datasets: [
    //                 {
    //                     label: 'Temperature (°C)',
    //                     data: [],
    //                     borderWidth: 3,
    //                     backgroundColor: ["#0d6efd"],
    //                     tension: 0.5,
    //                     pointRadius: 5,
    //                     spanGaps: true,
    //                     pointBackgroundColor: (context) => {
    //                         const tempPoint = context.dataset.data[context.dataIndex];
    //                         const tempValue = tempPoint && typeof tempPoint === 'object' ? tempPoint.y : tempPoint;
    //                         return tempValue >= feverBenchmark ? "#dc3545" : "#0d6efd";
    //                     },
    //                 }
    //             ]
    //         },
    //         options: {
    //             scales: {
    //                 x: {
    //                     type: 'time',
    //                     time: {
    //                         unit: 'hour',
    //                         displayFormats: { hour: 'HH:mm' }
    //                     },
    //                     min: minDate,
    //                     max: maxDate,
    //                     ticks: {
    //                         source: 'labels',
    //                         callback: (value) => {
    //                             const date = new Date(value);
    //                             return `${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`;
    //                         }
    //                     },
    //                     afterBuildTicks: (scale) => {
    //                         scale.ticks = generateTicks(minDate, maxDate, 60).map(date => ({ value: date.getTime() }));
    //                     },
    //                     title: { display: true, text: 'Time' }
    //                 },
    //                 y: {
    //                     suggestedMin: 35,
    //                     suggestedMax: 40,
    //                     title: { display: true, text: 'Temperature (°C)' },
    //                     ticks: { stepSize: 0.5 }
    //                 }
    //             },
    //             responsive: true,
    //             maintainAspectRatio: false,
    //             plugins: {
    //                 legend: { display: true },
    //                 tooltip: {
    //                     callbacks: {
    //                         label: function(context) {
    //                             return `${context.dataset.label}: ${context.parsed.y} °C`;
    //                         }
    //                     }
    //                 }
    //             }
    //         }
    //     });

    //     function updateTemperatureData(data) {
    //         if (!data.length) return;
    //         const relevantData = data.filter(item => item.parameterType === 'temperature');
    //         if (!relevantData.length) return;

    //         const { minDate } = getTimeBounds(relevantData, ['temperature']);
    //         if (!minDate) return;

    //         const tempData = [];
    //         relevantData.forEach(item => {
    //             const date = new Date(item.recordedAtRaw);
    //             if (isNaN(date)) {
    //                 console.warn(`Invalid recordedAtRaw: ${item.recordedAtRaw}`);
    //                 return;
    //             }
    //             if (item.parameterType === 'temperature' && item.value && item.value.celsius != null) {
    //                 tempData.push({ x: date, y: parseFloat(item.value.celsius) });
    //             }
    //         });

    //         tempData.sort((a, b) => a.x - b.x);
    //         TemperatureChart.data.datasets[0].data = tempData;
    //         TemperatureChart.update();
    //     }

    //     chartManager.updateFunctions.updateTemperatureData = updateTemperatureData;
    //     updateTemperatureData(data);
    //     return TemperatureChart;
    // }

    // Contractions Chart
// function createContractionsChart() {
//     const contractionsChart = modal._element.querySelector('#uterineContractionsChart');
//     const { minDate, maxDate } = getTimeBounds(data, ['uterine_contractions']);

//     const ContractionsChart = new Chart(contractionsChart, {
//         type: 'line',
//         data: {
//             datasets: [
//                 {
//                     label: 'Contractions (per 10 min)',
//                     data: [],
//                     borderWidth: 3,
//                     backgroundColor: '#ff00ff',
//                     tension: 0.5,
//                     pointRadius: 5,
//                     spanGaps: true
//                 }
//             ]
//         },
//         options: {
//             scales: {
//                 x: {
//                     type: 'time',
//                     time: { displayFormats: { minute: 'HH:mm' } },
//                     min: minDate,
//                     max: maxDate,
//                     ticks: {
//                         source: 'labels',
//                         callback: (value) => {
//                             const date = new Date(value);
//                             return `${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`;
//                         },
//                         maxTicksLimit: 40
//                     },
//                     afterBuildTicks: (scale) => {
//                         const currentMin = scale.chart.options.scales.x.min;
//                         const currentMax = scale.chart.options.scales.x.max;
//                         scale.ticks = generateTicks(currentMin, currentMax, 30).map(date => ({ value: date.getTime() }));
//                     },
//                     title: { display: true, text: 'Time' }
//                 },
//                 y: {
//                     suggestedMin: 0,
//                     suggestedMax: 5,
//                     title: { display: true, text: 'Contractions (per 10 min)' },
//                     ticks: { stepSize: 1 }
//                 }
//             },
//             responsive: true,
//             maintainAspectRatio: false,
//             plugins: {
//                 legend: { display: true },
//                 tooltip: {
//                     callbacks: {
//                         label: function(context) {
//                             return `${context.dataset.label}: ${context.parsed.y}`;
//                         }
//                     }
//                 }
//             }
//         }
//     });

//     function updateContractionsData(data) {
//         console.log('updateContractionsData called with:', data);
//         if (!data.length) {
//             console.log('No data, skipping update');
//             return;
//         }
//         const relevantData = data.filter(item => item.parameterType === 'uterine_contractions');
//         if (!relevantData.length) {
//             console.log('No uterine_contractions data, skipping update');
//             return;
//         }

//         const { minDate, maxDate } = getTimeBounds(relevantData, ['uterine_contractions']);
//         const contractionData = [];
//         relevantData.forEach(item => {
//             const date = new Date(item.recordedAtRaw);
//             if (isNaN(date)) {
//                 console.warn(`Invalid recordedAtRaw: ${item.recordedAtRaw}`);
//                 return;
//             }
//             if (item.parameterType === 'uterine_contractions' && item.value && item.value.count != null) {
//                 contractionData.push({ x: date, y: parseInt(item.value.count) });
//             }
//         });

//         contractionData.sort((a, b) => a.x - b.x);
//         console.log('contractionData:', contractionData);
//         ContractionsChart.data.datasets[0].data = [...contractionData];
//         ContractionsChart.options.scales.x.min = minDate;
//         ContractionsChart.options.scales.x.max = maxDate;
//         console.log('Updating chart with minDate:', minDate, 'maxDate:', maxDate);
//         ContractionsChart.update('active');
//     }

//     chartManager.updateFunctions.updateContractionsData = updateContractionsData;
//     updateContractionsData(data);
//     return ContractionsChart;
// }

// Observations Chart
// function createObservationsChart() {
//     const observationsChart = modal._element.querySelector('#observationsChart');
//     const { minDate, maxDate } = getTimeBounds(data, ['urine', 'caput', 'position', 'moulding', 'oxytocin', 'fluid', 'drug']);

//     const ObservationsChart = new Chart(observationsChart, {
//         type: 'line',
//         data: {
//             datasets: [
//                 { label: 'Urine', data: [], borderWidth: 3, backgroundColor: '#17a2b8', pointRadius: 5, spanGaps: true },
//                 { label: 'Caput', data: [], borderWidth: 3, backgroundColor: '#dc3545', pointRadius: 5, spanGaps: true },
//                 { label: 'Position', data: [], borderWidth: 3, backgroundColor: '#28a745', pointRadius: 5, spanGaps: true },
//                 { label: 'Moulding', data: [], borderWidth: 3, backgroundColor: '#ffc107', pointRadius: 5, spanGaps: true },
//                 { label: 'Oxytocin', data: [], borderWidth: 3, backgroundColor: '#6f42c1', pointRadius: 5, spanGaps: true },
//                 { label: 'Fluid', data: [], borderWidth: 3, backgroundColor: '#007bff', pointRadius: 5, spanGaps: true },
//                 { label: 'Drug', data: [], borderWidth: 3, backgroundColor: '#fd7e14', pointRadius: 5, spanGaps: true }
//             ]
//         },
//         options: {
//             scales: {
//                 x: {
//                     type: 'time',
//                     time: { displayFormats: { hour: 'HH:mm' } },
//                     min: minDate,
//                     max: maxDate,
//                     ticks: {
//                         source: 'labels',
//                         callback: (value) => {
//                             const date = new Date(value);
//                             return `${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`;
//                         }
//                     },
//                     afterBuildTicks: (scale) => {
//                         const currentMin = scale.chart.options.scales.x.min;
//                         const currentMax = scale.chart.options.scales.x.max;
//                         scale.ticks = generateTicks(currentMin, currentMax, 60).map(date => ({ value: date.getTime() }));
//                     },
//                     title: { display: true, text: 'Time' }
//                 },
//                 y: {
//                     display: false
//                 }
//             },
//             responsive: true,
//             maintainAspectRatio: false,
//             plugins: {
//                 legend: { display: true },
//                 tooltip: {
//                     callbacks: {
//                         label: function(context) {
//                             return `${context.dataset.label}: ${context.parsed.y || 'N/A'}`;
//                         }
//                     }
//                 },
//                 observationsText: {}
//             }
//         }
//     });

//     function updateObservationsData(data) {
//         console.log('updateObservationsData called with:', data);
//         if (!data.length) {
//             console.log('No data, skipping update');
//             return;
//         }
//         const relevantData = data.filter(item =>
//             ['urine', 'caput', 'position', 'moulding', 'oxytocin', 'fluid', 'drug'].includes(item.parameterType)
//         );
//         if (!relevantData.length) {
//             console.log('No observations data, skipping update');
//             return;
//         }

//         const { minDate, maxDate } = getTimeBounds(relevantData, ['urine', 'caput', 'position', 'moulding', 'oxytocin', 'fluid', 'drug']);
//         const datasets = [
//             { type: 'urine', data: [] },
//             { type: 'caput', data: [] },
//             { type: 'position', data: [] },
//             { type: 'moulding', data: [] },
//             { type: 'oxytocin', data: [] },
//             { type: 'fluid', data: [] },
//             { type: 'drug', data: [] }
//         ];

//         relevantData.forEach(item => {
//             const date = new Date(item.recordedAtRaw);
//             if (isNaN(date)) {
//                 console.warn(`Invalid recordedAtRaw: ${item.recordedAtRaw}`);
//                 return;
//             }
//             const dataset = datasets.find(ds => ds.type === item.parameterType);
//             if (dataset && item.value) {
//                 dataset.data.push({ x: date, y: item.value });
//             }
//         });

//         datasets.forEach(ds => ds.data.sort((a, b) => a.x - b.x));
//         console.log('observationsData:', datasets);
//         datasets.forEach((ds, index) => {
//             ObservationsChart.data.datasets[index].data = [...ds.data];
//         });
//         ObservationsChart.options.scales.x.min = minDate;
//         ObservationsChart.options.scales.x.max = maxDate;
//         console.log('Updating chart with minDate:', minDate, 'maxDate:', maxDate);
//         ObservationsChart.update('active');
//     }

//     chartManager.updateFunctions.updateObservationsData = updateObservationsData;
//     updateObservationsData(data);
//     return ObservationsChart;
// }

// Contractions Chart
function createContractionsChart() {
    const contractionsChart = modal._element.querySelector('#uterineContractionsChart');
    const { minDate, maxDate } = getTimeBounds(data, ['uterine_contractions']);

    const ContractionsChart = new Chart(contractionsChart, {
        type: 'bar',
        data: {
            datasets: [
                {
                    label: 'Contractions (per 10 min)',
                    data: [],
                    borderWidth: 3,
                    backgroundColor: '#ff00ff',
                    tension: 0.5,
                    pointRadius: 5,
                    spanGaps: true
                }
            ]
        },
        options: {
            scales: {
                x: {
                    type: 'time',
                    time: { displayFormats: { minute: 'HH:mm' } },
                    min: minDate,
                    max: maxDate,
                    ticks: {
                        source: 'labels',
                        callback: (value) => {
                            const date = new Date(value);
                            return `${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`;
                        },
                        maxTicksLimit: 40
                    },
                    afterBuildTicks: (scale) => {
                        const currentMin = scale.chart.options.scales.x.min;
                        const currentMax = scale.chart.options.scales.x.max;
                        scale.ticks = generateTicks(currentMin, currentMax, 30).map(date => ({ value: date.getTime() }));
                    },
                    title: { display: true, text: 'Time' }
                },
                y: {
                    suggestedMin: 0,
                    suggestedMax: 5,
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
                            return `${context.dataset.label}: ${context.parsed.y}`;
                        }
                    }
                }
            }
        }
    });

    function updateContractionsData(data) {
        console.log('updateContractionsData called with:', JSON.stringify(data, null, 2));
        if (!data || !Array.isArray(data) || !data.length) {
            console.log('No valid data, skipping update');
            return;
        }
        const relevantData = data.filter(item => item.parameterType === 'uterine_contractions' && item.recordedAtRaw);
        console.log('relevantData:', JSON.stringify(relevantData, null, 2));
        if (!relevantData.length) {
            console.log('No uterine_contractions data, skipping update');
            return;
        }

        const { minDate, maxDate } = getTimeBounds(relevantData, ['uterine_contractions']);
        const contractionData = [];
        relevantData.forEach(item => {
            const date = new Date(item.recordedAtRaw);
            if (isNaN(date)) {
                console.warn(`Invalid recordedAtRaw: ${item.recordedAtRaw}`);
                return;
            }
            if (item.value && typeof item.value === 'object' && 'count_per_10min' in item.value && item.value.count_per_10min != null) {
                const count = parseInt(item.value.count_per_10min);
                if (!isNaN(count)) {
                    contractionData.push({ x: date, y: count });
                } else {
                    console.warn(`Invalid count value: ${item.value.count_per_10min}`);
                }
            } else {
                console.warn(`Missing or invalid value for item:`, JSON.stringify(item, null, 2));
            }
        });

        contractionData.sort((a, b) => a.x - b.x);
        console.log('contractionData:', JSON.stringify(contractionData, null, 2));
        if (contractionData.length) {
            ContractionsChart.data.datasets[0].data = [...contractionData];
            ContractionsChart.options.scales.x.min = minDate;
            ContractionsChart.options.scales.x.max = maxDate;
            console.log('Updating chart with minDate:', minDate, 'maxDate:', maxDate);
            ContractionsChart.update('active');
        } else {
            console.log('No valid contraction data to update chart');
        }
    }

    chartManager.updateFunctions.updateContractionsData = updateContractionsData;
    updateContractionsData(data);
    return ContractionsChart;
}

// Observations Chart
// function createObservationsChart() {
//     const observationsChart = modal._element.querySelector('#observationsChart');
//     const { minDate, maxDate } = getTimeBounds(data, ['urine', 'caput', 'position', 'moulding', 'oxytocin', 'fluid', 'drug']);

//     const ObservationsChart = new Chart(observationsChart, {
//         type: 'line',
//         data: {
//             datasets: [
//                 { label: 'Urine', data: [], borderWidth: 3, backgroundColor: '#17a2b8', pointRadius: 5, spanGaps: true },
//                 { label: 'Caput', data: [], borderWidth: 3, backgroundColor: '#dc3545', pointRadius: 5, spanGaps: true },
//                 { label: 'Position', data: [], borderWidth: 3, backgroundColor: '#28a745', pointRadius: 5, spanGaps: true },
//                 { label: 'Moulding', data: [], borderWidth: 3, backgroundColor: '#ffc107', pointRadius: 5, spanGaps: true },
//                 { label: 'Oxytocin', data: [], borderWidth: 3, backgroundColor: '#6f42c1', pointRadius: 5, spanGaps: true },
//                 { label: 'Fluid', data: [], borderWidth: 3, backgroundColor: '#007bff', pointRadius: 5, spanGaps: true },
//                 { label: 'Drug', data: [], borderWidth: 3, backgroundColor: '#fd7e14', pointRadius: 5, spanGaps: true }
//             ]
//         },
//         options: {
//             scales: {
//                 x: {
//                     type: 'time',
//                     time: { displayFormats: { hour: 'HH:mm' } },
//                     min: minDate,
//                     max: maxDate,
//                     ticks: {
//                         source: 'labels',
//                         callback: (value) => {
//                             const date = new Date(value);
//                             return `${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`;
//                         }
//                     },
//                     afterBuildTicks: (scale) => {
//                         const currentMin = scale.chart.options.scales.x.min;
//                         const currentMax = scale.chart.options.scales.x.max;
//                         scale.ticks = generateTicks(currentMin, currentMax, 60).map(date => ({ value: date.getTime() }));
//                     },
//                     title: { display: true, text: 'Time' }
//                 },
//                 y: {
//                     display: false
//                 }
//             },
//             responsive: true,
//             maintainAspectRatio: false,
//             plugins: {
//                 legend: { display: true },
//                 tooltip: {
//                     callbacks: {
//                         label: function(context) {
//                             return `${context.dataset.label}: ${context.parsed.y || context.raw.y || 'N/A'}`;
//                         }
//                     }
//                 },
//                 observationsText: {}
//             }
//         }
//     });

//     function updateObservationsData(data) {
//         console.log('updateObservationsData called with:', JSON.stringify(data, null, 2));
//         if (!data || !Array.isArray(data) || !data.length) {
//             console.log('No valid data, skipping update');
//             return;
//         }
//         const relevantData = data.filter(item =>
//             ['urine', 'caput', 'position', 'moulding', 'oxytocin', 'fluid', 'drug'].includes(item.parameterType) && item.recordedAtRaw
//         );
//         console.log('relevantData:', JSON.stringify(relevantData, null, 2));
//         if (!relevantData.length) {
//             console.log('No observations data, skipping update');
//             return;
//         }

//         const { minDate, maxDate } = getTimeBounds(relevantData, ['urine', 'caput', 'position', 'moulding', 'oxytocin', 'fluid', 'drug']);
//         const datasets = [
//             { type: 'urine', data: [] },
//             { type: 'caput', data: [] },
//             { type: 'position', data: [] },
//             { type: 'moulding', data: [] },
//             { type: 'oxytocin', data: [] },
//             { type: 'fluid', data: [] },
//             { type: 'drug', data: [] }
//         ];

//         relevantData.forEach(item => {
//             const date = new Date(item.recordedAtRaw);
//             if (isNaN(date)) {
//                 console.warn(`Invalid recordedAtRaw: ${item.recordedAtRaw}`);
//                 return;
//             }
//             const dataset = datasets.find(ds => ds.type === item.parameterType);
//             if (dataset && item.value != null) {
//                 // Store value as-is (string, number, or object) for observationsText plugin
//                 dataset.data.push({ x: date, y: item.value });
//             } else {
//                 console.warn(`Missing or invalid value for item:`, JSON.stringify(item, null, 2));
//             }
//         });

//         datasets.forEach(ds => ds.data.sort((a, b) => a.x - b.x));
//         console.log('observationsData:', JSON.stringify(datasets, null, 2));
//         datasets.forEach((ds, index) => {
//             ObservationsChart.data.datasets[index].data = [...ds.data];
//         });
//         ObservationsChart.options.scales.x.min = minDate;
//         ObservationsChart.options.scales.x.max = maxDate;
//         ObservationsChart.options.plugins.observationsText.minDate = minDate;
//         ObservationsChart.options.plugins.observationsText.maxDate = maxDate;
//         console.log('Updating chart with minDate:', minDate, 'maxDate:', maxDate);
//         ObservationsChart.update('active');
//     }

//     chartManager.updateFunctions.updateObservationsData = updateObservationsData;
//     updateObservationsData(data);
//     return ObservationsChart;
// }

// Contractions Chart
// function createContractionsChart() {
//     const contractionsChart = modal._element.querySelector('#uterineContractionsChart');
//     const ContractionsChart = new Chart(contractionsChart, {
//         type: 'bar',
//         data: {
//             datasets: [
//                 {
//                     label: 'Contractions (per 10 min)',
//                     data: [],
//                     backgroundColor: [],
//                     borderWidth: 1,
//                     barThickness: 10,
//                 }
//             ]
//         },
//         options: {
//             scales: {
//                 x: {
//                     type: 'linear',
//                     title: { display: true, text: 'Hours since first observation' },
//                     min: 0,
//                     max: 18,
//                     ticks: { stepSize: 1 }
//                 },
//                 y: {
//                     beginAtZero: true,
//                     suggestedMin: 0,
//                     suggestedMax: 7,
//                     title: { display: true, text: 'Contractions (per 10 min)' },
//                     ticks: { stepSize: 1 }
//                 }
//             },
//             responsive: true,
//             maintainAspectRatio: false,
//             plugins: {
//                 legend: { display: true },
//                 tooltip: {
//                     callbacks: {
//                         label: function(context) {
//                             const { y, lasting, strength } = context.raw;
//                             const strengthText = { 'W': 'Weak', 'M': 'Moderate', 'S': 'Strong' }[strength] || strength;
//                             return `Contractions: ${y}, Duration: ${lasting || 'N/A'}s, Strength: ${strengthText}`;
//                         }
//                     }
//                 },
//                 lastingText: {
//                     enableLastingText: true // Enable plugin for this chart
//                 }
//             }
//         }
//     });

//     function updateContractionsData(data) {
//         console.log('updateContractionsData called with:', JSON.stringify(data, null, 2));
//         if (!data || !Array.isArray(data) || !data.length) {
//             console.log('No valid data, skipping update');
//             return;
//         }

//         const relevantData = data.filter(item => item.parameterType === 'uterine_contractions' && item.recordedAtRaw);
//         console.log('relevantData:', JSON.stringify(relevantData, null, 2));
//         if (!relevantData.length) {
//             console.log('No uterine_contractions data, skipping update');
//             return;
//         }

//         const dates = relevantData.map(item => new Date(item.recordedAtRaw)).filter(date => !isNaN(date));
//         if (!dates.length) {
//             console.log('No valid dates, skipping update');
//             return;
//         }
//         const minDate = dates.reduce((min, date) => (date < min ? date : min), dates[0]);

//         const contractionsData = [];
//         const backgroundColors = [];
//         let maxHours = 0;

//         relevantData.forEach(item => {
//             const date = new Date(item.recordedAtRaw);
//             if (isNaN(date)) {
//                 console.warn(`Invalid recordedAtRaw: ${item.recordedAtRaw}`);
//                 return;
//             }
//             const hours = (date - minDate) / (1000 * 60 * 60);
//             maxHours = Math.max(maxHours, hours);
//             if (item.parameterType === 'uterine_contractions' && item.value && item.value.count_per_10min != null) {
//                 const strength = item.value.strength ? item.value.strength[0] : 'W';
//                 const color = {
//                     'W': '#ff9999',
//                     'M': '#ff3333',
//                     'S': '#cc0000'
//                 }[strength] || '#ff9999';
//                 const count = parseFloat(item.value.count_per_10min);
//                 if (!isNaN(count)) {
//                     contractionsData.push({
//                         x: hours,
//                         y: count,
//                         lasting: item.value.lasting_seconds || null,
//                         strength
//                     });
//                     backgroundColors.push(color);
//                 } else {
//                     console.warn(`Invalid count_per_10min value: ${item.value.count_per_10min}`);
//                 }
//             } else {
//                 console.warn(`Missing or invalid value for item:`, JSON.stringify(item, null, 2));
//             }
//         });

//         contractionsData.sort((a, b) => a.x - b.x);
//         console.log('contractionsData:', JSON.stringify(contractionsData, null, 2));
//         console.log('backgroundColors:', backgroundColors);
//         if (contractionsData.length) {
//             ContractionsChart.data.datasets[0].data = [...contractionsData];
//             ContractionsChart.data.datasets[0].backgroundColor = [...backgroundColors];
//             ContractionsChart.options.scales.x.max = Math.max(maxHours + 1, 18);
//             console.log('Updating chart with maxHours:', maxHours);
//             ContractionsChart.update('active');
//         } else {
//             console.log('No valid contraction data to update chart');
//         }
//     }

//     chartManager.updateFunctions.updateContractionsData = updateContractionsData;
//     updateContractionsData(data);
//     return ContractionsChart;
// }

function createContractionsChart() {
    const contractionsChart = modal._element.querySelector('#uterineContractionsChart');
    const { minDate, maxDate } = getTimeBounds(data, ['uterine_contractions']);
    // if (!contractionsChart) {
    //     console.error('Contractions canvas #uterineContractionsChart not found');
    //     return null;
    // }


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
                    type: 'time',
                    time: { displayFormats: { minute: 'HH:mm' } },
                    min: minDate,
                    max: maxDate,
                    ticks: {
                        source: 'labels',
                        callback: (value) => {
                            const date = new Date(value);
                            return `${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`;
                        },
                        maxTicksLimit: 40
                    },
                    afterBuildTicks: (scale) => {
                        const currentMin = scale.chart.options.scales.x.min;
                        const currentMax = scale.chart.options.scales.x.max;
                        scale.ticks = generateTicks(currentMin, currentMax, 30).map(date => ({ value: date.getTime() }));
                    },
                    title: { display: true, text: 'Time' }
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
        console.log('updateContractionsData called with:', JSON.stringify(data, null, 2));
        if (!contractionsChart || !contractionsChart.isConnected) {
            console.warn('Contractions canvas is detached or null, skipping update');
            return;
        }
        // if (!data || !Array.isArray(data) || !data.length) {
        //     console.log('No valid data, skipping update');
        //     return;
        // }

        const relevantData = data.filter(item => item.parameterType === 'uterine_contractions' && item.recordedAtRaw);
        console.log('relevantData:', JSON.stringify(relevantData, null, 2));
        // if (!relevantData.length) {
        //     console.log('No uterine_contractions data, skipping update');
        //     return;
        // }

        const dates = relevantData.map(item => new Date(item.recordedAtRaw)).filter(date => !isNaN(date));
        // if (!dates.length) {
        //     console.log('No valid dates, skipping update');
        //     return;
        // }
        const { minDate, maxDate } = getTimeBounds(relevantData, ['uterine_contractions']);
        const contractionsData = [];
        const backgroundColors = [];
        const getColourShade = (strength, countPer10Min) => {
            if (countPer10Min < 3){
                return strength == 'W' ? '#a6bff2' : strength == 'M' ? '#4d7ee5' : '#1a4bb2'
            }
            if (countPer10Min > 2 && countPer10Min < 6){
                return strength == 'W' ? '#a9f7c1' : strength == 'M' ? '#3eec74' : '#1ca447'
            }
            if (countPer10Min > 5){
                return strength == 'W' ? '#f5c0bc' : strength == 'M' ? '#e14137' : '#b2231a'
            }
            return '#000000'
        }
        relevantData.forEach(item => {
            const date = new Date(item.recordedAtRaw);
            if (isNaN(date)) {
                console.warn(`Invalid recordedAtRaw: ${item.recordedAtRaw}`);
                return;
            }
            if (item.parameterType === 'uterine_contractions' && item.value && item.value.count_per_10min != null) {
                const strength = item.value.strength ? item.value.strength[0] : 'W';
                const count = parseFloat(item.value.count_per_10min);
                const color = getColourShade(strength, count)
                // const color = {
                //     'W': '#ff9999',
                //     'M': '#ff3333',
                //     'S': '#cc0000'
                // }[strength] || '#ff9999';
                if (!isNaN(count)) {
                    contractionsData.push({
                        x: date,
                        y: count,
                        lasting: item.value.lasting_seconds || null,
                        strength
                    });
                    backgroundColors.push(color);
                } else {
                    console.warn(`Invalid count_per_10min value: ${item.value.count_per_10min}`);
                }
            } else {
                console.warn(`Missing or invalid value for item:`, JSON.stringify(item, null, 2));
            }
        });

        contractionsData.sort((a, b) => a.x - b.x);
        console.log('contractionsData:', JSON.stringify(contractionsData, null, 2));
        console.log('backgroundColors:', backgroundColors);
        // if (contractionsData.length) {
        ContractionsChart.data.datasets[0].data = [...contractionsData];
        ContractionsChart.data.datasets[0].backgroundColor = [...backgroundColors];
        // Update min/max from options
        ContractionsChart.options.scales.x.min = minDate;
        ContractionsChart.options.scales.x.max = maxDate;
        // const minDate = ContractionsChart.options.scales.x.min;
        // const maxDate = ContractionsChart.options.scales.x.max;
        console.log('Updating chart with minDate:', minDate, 'maxDate:', maxDate);
        ContractionsChart.update('active');
        // } else {
        //     console.log('No valid contraction data to update chart');
        // }
    }

    chartManager.updateFunctions.updateContractionsData = updateContractionsData;
    updateContractionsData(data);
    return ContractionsChart;
}

// // Observations Chart
// function createObservationsChart() {
//     const observationsChart = modal._element.querySelector('#observationsChart');
//     const ObservationsChart = new Chart(observationsChart, {
//         type: 'scatter',
//         data: {
//             datasets: [
//                 {
//                     label: 'Maternal & Fetal Observations',
//                     data: [],
//                     pointRadius: 0, // No default points, use text
//                     showLine: false
//                 }
//             ]
//         },
//         options: {
//             scales: {
//                 x: {
//                     type: 'linear',
//                     title: { display: true, text: 'Hours since first observation' },
//                     min: 0,
//                     max: 18,
//                     ticks: { stepSize: 1 }
//                 },
//                 y: {
//                     type: 'category',
//                     labels: ['Drug', 'Fluid', 'Oxytocin', 'Moulding', 'Position', 'Caput', 'Urine'],
//                     title: { display: true, text: 'Observations' },
//                     reverse: true // Top-to-bottom order
//                 }
//             },
//             responsive: true,
//             maintainAspectRatio: false,
//             plugins: {
//                 legend: { display: false },
//                 tooltip: {
//                     callbacks: {
//                         label: function(context) {
//                             const point = context.raw;
//                             switch (point.parameterType) {
//                                 case 'urine':
//                                     return `Urine: Protein ${point.value.protein || 'N/A'}, Glucose ${point.value.glucose || 'N/A'}, Volume ${point.value.volume || 'N/A'}mL`;
//                                 case 'caput':
//                                     return `Caput: ${point.value.degree || '0'}`;
//                                 case 'position':
//                                     return `Position: ${point.value.position || 'N/A'}`;
//                                 case 'moulding':
//                                     return `Moulding: ${point.value.degree || '0'}`;
//                                 case 'oxytocin':
//                                     return `Oxytocin: ${point.value.dosage || 'N/A'} units`;
//                                 case 'fluid':
//                                     return `Fluid: ${point.value.status || 'N/A'}`;
//                                 case 'drug':
//                                     return `Drug: ${point.value.type || 'N/A'}`;
//                                 default:
//                                     return 'Unknown';
//                             }
//                         }
//                     }
//                 },
//                 observationsText: {
//                     enableObservationsText: true // Enable plugin for this chart
//                 }
//             }
//         }
//     });

//     function updateObservationsData(data) {
//         console.log('updateObservationsData called with:', JSON.stringify(data, null, 2));
//         if (!data || !Array.isArray(data) || !data.length) {
//             console.log('No valid data, skipping update');
//             return;
//         }

//         const relevantTypes = ['urine', 'caput', 'position', 'moulding', 'oxytocin', 'fluid', 'drug'];
//         const relevantData = data.filter(item => relevantTypes.includes(item.parameterType) && item.recordedAtRaw);
//         console.log('relevantData:', JSON.stringify(relevantData, null, 2));
//         if (!relevantData.length) {
//             console.log('No observations data, skipping update');
//             return;
//         }

//         const dates = relevantData.map(item => new Date(item.recordedAtRaw)).filter(date => !isNaN(date));
//         if (!dates.length) {
//             console.log('No valid dates, skipping update');
//             return;
//         }
//         const minDate = dates.reduce((min, date) => (date < min ? date : min), dates[0]);

//         const observationsData = [];
//         let maxHours = 0;

//         relevantData.forEach(item => {
//             const date = new Date(item.recordedAtRaw);
//             if (isNaN(date)) {
//                 console.warn(`Invalid recordedAtRaw: ${item.recordedAtRaw}`);
//                 return;
//             }
//             const hours = (date - minDate) / (1000 * 60 * 60);
//             maxHours = Math.max(maxHours, hours);
//             if (item.value && item.parameterType) {
//                 const yValue = {
//                     urine: 'Urine',
//                     caput: 'Caput',
//                     position: 'Position',
//                     moulding: 'Moulding',
//                     oxytocin: 'Oxytocin',
//                     fluid: 'Fluid',
//                     drug: 'Drug'
//                 }[item.parameterType];
//                 observationsData.push({
//                     x: hours,
//                     y: yValue,
//                     parameterType: item.parameterType,
//                     value: item.value
//                 });
//             } else {
//                 console.warn(`Missing or invalid value for item:`, JSON.stringify(item, null, 2));
//             }
//         });

//         observationsData.sort((a, b) => a.x - b.x);
//         console.log('observationsData:', JSON.stringify(observationsData, null, 2));
//         if (observationsData.length) {
//             ObservationsChart.data.datasets[0].data = [...observationsData];
//             ObservationsChart.options.scales.x.max = Math.max(maxHours + 1, 18);
//             console.log('Updating chart with maxHours:', maxHours);
//             ObservationsChart.update('active');
//         } else {
//             console.log('No valid observations data to update chart');
//         }
//     }

//     chartManager.updateFunctions.updateObservationsData = updateObservationsData;
//     updateObservationsData(data);
//     return ObservationsChart;
// }

// Observations Chart
function createObservationsChart() {
    const observationsChart = modal._element.querySelector('#observationsChart');
    if (!observationsChart) {
        console.error('Observations canvas #observationsChart not found');
        return null;
    }

    const { minDate, maxDate } = getTimeBounds(data, ['urine', 'caput', 'position', 'moulding', 'oxytocin', 'fluid', 'drug']);

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
                    type: 'time',
                    time: { displayFormats: { minute: 'HH:mm' } },
                    min: minDate,
                    max: maxDate,
                    ticks: {
                        source: 'labels',
                        callback: (value) => {
                            const date = new Date(value);
                            return `${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`;
                        }
                    },
                    afterBuildTicks: (scale) => {
                        const currentMin = scale.chart.options.scales.x.min;
                        const currentMax = scale.chart.options.scales.x.max;
                        scale.ticks = generateTicks(currentMin, currentMax, 60).map(date => ({ value: date.getTime() }));
                    },
                    title: { display: true, text: 'Time' }
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
        // console.log('updateObservationsData called with:', JSON.stringify(data, null, 2));
        if (!observationsChart || !observationsChart.isConnected) {
            console.warn('Observations canvas is detached or null, skipping update');
            return;
        }
        // if (!data || !Array.isArray(data) || !data.length) {
        //     console.log('No valid data, skipping update');
        //     return;
        // }

        const relevantTypes = ['urine', 'caput', 'position', 'moulding', 'oxytocin', 'fluid', 'drug'];
        const relevantData = data.filter(item => relevantTypes.includes(item.parameterType) && item.recordedAtRaw);
        // console.log('relevantData:', JSON.stringify(relevantData, null, 2));
        // if (!relevantData.length) {
        //     console.log('No observations data, skipping update');
        //     return;
        // }

        const { minDate, maxDate } = getTimeBounds(relevantData, relevantTypes);
        const observationsData = [];

        relevantData.forEach(item => {
            const date = new Date(item.recordedAtRaw);
            if (isNaN(date)) {
                console.warn(`Invalid recordedAtRaw: ${item.recordedAtRaw}`);
                return;
            }
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
                    x: date.getTime(),
                    y: yValue,
                    parameterType: item.parameterType,
                    value: item.value
                });
            } else {
                console.warn(`Missing or invalid value for item:`, JSON.stringify(item, null, 2));
            }
        });

        observationsData.sort((a, b) => a.x - b.x);
        // console.log('observationsData:', JSON.stringify(observationsData, null, 2));
        // if (observationsData.length) {
            ObservationsChart.data.datasets[0].data = [...observationsData];
            ObservationsChart.options.scales.x.min = minDate;
            ObservationsChart.options.scales.x.max = maxDate;
            // console.log('Updating chart with minDate:', minDate, 'maxDate:', maxDate);
            ObservationsChart.update('active');
        // } else {
        //     console.log('No valid observations data to update chart');
        // }
    }

    chartManager.updateFunctions.updateObservationsData = updateObservationsData;
    updateObservationsData(data);
    return ObservationsChart;
}

// Blood Pressure/Pulse Chart
function createBloodPressurePulseChart() {
    const bpChart = modal._element.querySelector('#bloodPressurePulseChart');
    const { minDate, maxDate } = getTimeBounds(data, ['blood_pressure', 'pulse']);

    const BloodPressurePulseChart = new Chart(bpChart, {
        type: 'line',
        data: {
            datasets: [
                {
                    label: 'Systolic BP (mmHg)',
                    data: [],
                    borderWidth: 3,
                    backgroundColor: '#dc3545',
                    tension: 0.5,
                    pointRadius: 5,
                    spanGaps: true
                },
                {
                    label: 'Diastolic BP (mmHg)',
                    data: [],
                    borderWidth: 3,
                    backgroundColor: '#007bff',
                    tension: 0.5,
                    pointRadius: 5,
                    spanGaps: true
                },
                {
                    label: 'Pulse (bpm)',
                    data: [],
                    borderWidth: 3,
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
                    type: 'time',
                    time: { displayFormats: { hour: 'HH:mm' } },
                    min: minDate,
                    max: maxDate,
                    ticks: {
                        source: 'labels',
                        callback: (value) => {
                            const date = new Date(value);
                            return `${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`;
                        }
                    },
                    afterBuildTicks: (scale) => {
                        const currentMin = scale.chart.options.scales.x.min;
                        const currentMax = scale.chart.options.scales.x.max;
                        scale.ticks = generateTicks(currentMin, currentMax, 60).map(date => ({ value: date.getTime() }));
                    },
                    title: { display: true, text: 'Time' }
                },
                y: {
                    suggestedMin: 40,
                    suggestedMax: 200,
                    title: { display: true, text: 'BP (mmHg) / Pulse (bpm)' },
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
                                xMin: minDate,
                                xMax: maxDate,
                                borderColor: 'red',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                label: {
                                    display: true,
                                    content: 'Systolic Alert Line',
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
                                yMin: 60,
                                yMax: 60,
                                xMin: minDate,
                                xMax: maxDate,
                                borderColor: 'red',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                label: {
                                    display: true,
                                    content: 'Diastolic Alert Line',
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
        // console.log('updateBloodPressurePulseData called with:', data);
        // if (!data.length) {
        //     console.log('No data, skipping update');
        //     return;
        // }
        const relevantData = data.filter(item => ['blood_pressure', 'pulse'].includes(item.parameterType));
        // if (!relevantData.length) {
        //     console.log('No blood_pressure/pulse data, skipping update');
        //     return;
        // }

        const { minDate, maxDate } = getTimeBounds(relevantData, ['blood_pressure', 'pulse']);
        const systolicData = [];
        const diastolicData = [];
        const pulseData = [];

        relevantData.forEach(item => {
            const date = new Date(item.recordedAtRaw);
            if (isNaN(date)) {
                console.warn(`Invalid recordedAtRaw: ${item.recordedAtRaw}`);
                return;
            }
            if (item.parameterType === 'blood_pressure' && item.value) {
                if (item.value.systolic != null) {
                    systolicData.push({ x: date, y: parseFloat(item.value.systolic) });
                }
                if (item.value.diastolic != null) {
                    diastolicData.push({ x: date, y: parseFloat(item.value.diastolic) });
                }
            } else if (item.parameterType === 'pulse' && item.value && item.value.bpm != null) {
                pulseData.push({ x: date, y: parseFloat(item.value.bpm) });
            }
        });

        systolicData.sort((a, b) => a.x - b.x);
        diastolicData.sort((a, b) => a.x - b.x);
        pulseData.sort((a, b) => a.x - b.x);
        // console.log('systolicData:', systolicData, 'diastolicData:', diastolicData, 'pulseData:', pulseData);
        BloodPressurePulseChart.data.datasets[0].data = [...systolicData];
        BloodPressurePulseChart.data.datasets[1].data = [...diastolicData];
        BloodPressurePulseChart.data.datasets[2].data = [...pulseData];
        BloodPressurePulseChart.options.scales.x.min = minDate;
        BloodPressurePulseChart.options.scales.x.max = maxDate;
        // console.log('Updating chart with minDate:', minDate, 'maxDate:', maxDate);
        BloodPressurePulseChart.update('active');
    }

    chartManager.updateFunctions.updateBloodPressurePulseData = updateBloodPressurePulseData;
    updateBloodPressurePulseData(data);
    return BloodPressurePulseChart;
}

// Temperature Chart
function createTemperatureChart() {
    const feverBenchmark = document.querySelector('#feverBenchMark').value ?? 37.3
    const tempChart = modal._element.querySelector('#temperatureChart');
    const { minDate, maxDate } = getTimeBounds(data, ['temperature']);

    const TemperatureChart = new Chart(tempChart, {
        type: 'line',
        data: {
            datasets: [
                {
                    label: 'Temperature (°C)',
                    data: [],
                    borderWidth: 3,
                    backgroundColor: '#0d6efd',
                    pointBackgroundColor: (context) => {
                        return context.dataset.data[context.dataIndex]?.y >= feverBenchmark ? "#dc3545" : "#0d6efd";
                    },
                    tension: 0.5,
                    pointRadius: 5,
                    spanGaps: true
                }
            ]
        },
        options: {
            scales: {
                x: {
                    type: 'time',
                    time: { displayFormats: { hour: 'HH:mm' } },
                    min: minDate,
                    max: maxDate,
                    ticks: {
                        source: 'labels',
                        callback: (value) => {
                            const date = new Date(value);
                            return `${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`;
                        }
                    },
                    afterBuildTicks: (scale) => {
                        const currentMin = scale.chart.options.scales.x.min;
                        const currentMax = scale.chart.options.scales.x.max;
                        scale.ticks = generateTicks(currentMin, currentMax, 60).map(date => ({ value: date.getTime() }));
                    },
                    title: { display: true, text: 'Time' }
                },
                y: {
                    suggestedMin: 31,
                    suggestedMax: 41,
                    title: { display: true, text: 'Temperature (°C)' },
                    ticks: { stepSize: 0.5 }
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
                },
                annotation: {
                        annotations: {
                            alertLine: {
                                type: 'line',
                                yMin: 37.5,
                                yMax: 37.5,
                                xMin: minDate,
                                xMax: maxDate,
                                borderColor: 'red',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                label: {
                                    display: true,
                                    content: 'Hyperthermia Alert Line',
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
                                yMin: 35,
                                yMax: 35,
                                xMin: minDate,
                                xMax: maxDate,
                                borderColor: 'red',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                label: {
                                    display: true,
                                    content: 'Hypothermia Alert Line',
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

    function updateTemperatureData(data) {
        // console.log('updateTemperatureData called with:', data);
        // if (!data.length) {
        //     console.log('No data, skipping update');
        //     return;
        // }
        const relevantData = data.filter(item => item.parameterType === 'temperature');
        // if (!relevantData.length) {
        //     console.log('No temperature data, skipping update');
        //     return;
        // }

        const { minDate, maxDate } = getTimeBounds(relevantData, ['temperature']);
        const tempData = [];
        relevantData.forEach(item => {
            const date = new Date(item.recordedAtRaw);
            if (isNaN(date)) {
                console.warn(`Invalid recordedAtRaw: ${item.recordedAtRaw}`);
                return;
            }
            if (item.parameterType === 'temperature' && item.value && item.value.celsius != null) {
                tempData.push({ x: date, y: parseFloat(item.value.celsius) });
            }
        });

        tempData.sort((a, b) => a.x - b.x);
        // console.log('tempData:', tempData);
        TemperatureChart.data.datasets[0].data = [...tempData];
        TemperatureChart.options.scales.x.min = minDate;
        TemperatureChart.options.scales.x.max = maxDate;
        // console.log('Updating chart with minDate:', minDate, 'maxDate:', maxDate);
        TemperatureChart.update('active');
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