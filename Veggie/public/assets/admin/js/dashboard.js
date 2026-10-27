(function () {
    'use strict';

    var colors = ['#16a34a', '#3b82f6', '#f59e0b', '#8b5cf6', '#ef4444'];

    function parseData(element, attribute) {
        try {
            return JSON.parse(element.getAttribute(attribute) || '[]');
        } catch (error) {
            return [];
        }
    }

    function formatCurrency(value) {
        return Number(value || 0).toLocaleString('vi-VN') + ' VNĐ';
    }

    function formatMonth(value) {
        var parts = String(value).split('-');
        return parts.length === 2 ? 'T' + Number(parts[1]) + '/' + parts[0] : value;
    }

    function initRevenueChart() {
        var canvas = document.getElementById('dashboardRevenueChart');

        if (!canvas || typeof Chart === 'undefined') {
            return;
        }

        var labels = parseData(canvas, 'data-labels').map(formatMonth);
        var values = parseData(canvas, 'data-values').map(Number);

        new Chart(canvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Doanh thu',
                    data: values,
                    backgroundColor: '#22a95a',
                    borderColor: '#168a48',
                    borderWidth: 0,
                    hoverBackgroundColor: '#178f4a'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: { display: false },
                layout: { padding: { top: 4, right: 4, bottom: 0, left: 0 } },
                tooltips: {
                    displayColors: false,
                    callbacks: {
                        label: function (tooltipItem) {
                            return formatCurrency(tooltipItem.yLabel);
                        }
                    }
                },
                scales: {
                    xAxes: [{
                        gridLines: { display: false, drawBorder: false },
                        ticks: { fontColor: '#8a94a6', fontSize: 11 }
                    }],
                    yAxes: [{
                        gridLines: { color: '#edf0f4', drawBorder: false, zeroLineColor: '#edf0f4' },
                        ticks: {
                            beginAtZero: true,
                            fontColor: '#8a94a6',
                            fontSize: 11,
                            padding: 10,
                            callback: function (value) {
                                if (value >= 1000000000) return (value / 1000000000) + ' tỷ';
                                if (value >= 1000000) return (value / 1000000) + ' tr';
                                if (value >= 1000) return (value / 1000) + 'k';
                                return value;
                            }
                        }
                    }]
                }
            }
        });
    }

    function initCategoryChart() {
        var canvas = document.getElementById('dashboardCategoryChart');

        if (!canvas || typeof Chart === 'undefined') {
            return;
        }

        new Chart(canvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: parseData(canvas, 'data-labels'),
                datasets: [{
                    data: parseData(canvas, 'data-counts').map(Number),
                    backgroundColor: colors,
                    borderColor: '#ffffff',
                    borderWidth: 3,
                    hoverBorderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutoutPercentage: 70,
                legend: { display: false },
                tooltips: {
                    callbacks: {
                        label: function (tooltipItem, data) {
                            var label = data.labels[tooltipItem.index] || '';
                            var value = data.datasets[0].data[tooltipItem.index] || 0;
                            return label + ': ' + value + ' sản phẩm';
                        }
                    }
                }
            }
        });
    }

    function initDashboard() {
        initRevenueChart();
        initCategoryChart();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDashboard);
    } else {
        initDashboard();
    }
})();
