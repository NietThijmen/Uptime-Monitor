// HELPER JAVASCRIPT FOR THE x-chart COMPONENT.
let isChartJsLoaded = false;
window.addEventListener('DOMContentLoaded', () => {
    // look for elements with data-chart
    const charts = document.querySelectorAll('[data-chart]')
    if (charts.length > 0) {
        importLibrary();
    }
})

const importLibrary = () => {
    if (isChartJsLoaded) return;

    import('chart.js/auto').then((chart) => {
        window.charts = chart;
        const event = new Event('chartjs:loaded');
        window.dispatchEvent(event);
    })
}

window.addEventListener('chartjs:loaded', () => {
    isChartJsLoaded = true;
    const charts = document.querySelectorAll('[data-chart]');
    charts.forEach((element) => {
        loadChart(element);
    })
});

// fix for livewire re-rendering
window.addEventListener('load', () => {
    if(!isChartJsLoaded) return;
    console.info('Livewire re-rendered');
    const charts = document.querySelectorAll('[data-chart]');
    charts.forEach((element) => {
        if (element.chart) {
            element.chart.destroy();
        }
        loadChart(element);
    })
});

const loadChart = (element) => {
    console.info(`Loading chart`);
    const data = JSON.parse(element.dataset.chart);
    const ctx = element.getContext('2d');
    const chart = new window.charts.Chart(ctx, {
        type: data.type,
        data: {
            labels: data.labels,
            datasets: data.datasets
        },
        options: data.options
    });
    element.chart = chart;
}
