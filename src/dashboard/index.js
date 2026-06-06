/**
 * Dashboard Scripts
 */

import './dashboard.less';
import Chart from 'chart.js/auto';

class DDWCPOS_Dashboard {
    constructor() {
        this.init();
    }

    init() {
        if (typeof ddwcposDashboardData === 'undefined') {
            console.error('ddwcposDashboardData is undefined');
            return;
        }
        
        this.initDateRangeToggle();
        this.initCharts();
    }

    initDateRangeToggle() {
        const picker = document.querySelector('.ddwcpos-date-range-picker'); // Fixed selector
        const dropdown = document.getElementById('ddwcpos-date-range-dropdown');
        const presets = document.querySelectorAll('.ddwcpos-date-preset');
        const applyCustom = document.querySelector('.ddwcpos-apply-custom-range');
        const inputField = document.getElementById('ddwcpos-selected-range');
        const form = document.querySelector('.ddwcpos-date-filter-form');

        if (picker && dropdown) {
            // Toggle dropdown
            picker.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropdown.classList.toggle('show');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!picker.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.remove('show');
                }
            });

            // Handle preset selection
            presets.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const range = btn.getAttribute('data-range');
                    if (inputField) inputField.value = range;
                    if (form) form.submit();
                });
            });

            // Handle custom range apply
            if (applyCustom) {
                applyCustom.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    if (inputField) inputField.value = 'custom';
                    if (form) form.submit();
                });
            }
        }
    }

    initCharts() {
        this.renderRevenueChart();
        this.renderOutletsChart();
        this.renderPaymentMethodsChart();
    }

    formatDate(dateStr, format) {
        if (!dateStr) return '';
        const date = new Date(dateStr);
        
        if (format === 'quarter') {
            const quarter = Math.ceil((date.getMonth() + 1) / 3);
            return `Q${quarter} ${date.getFullYear()}`;
        }
        
        if (format === 'month') {
            return new Intl.DateTimeFormat('en-US', { month: 'short', year: 'numeric' }).format(date);
        }
        
        return new Intl.DateTimeFormat('en-US', { month: 'short', day: 'numeric' }).format(date);
    }

    getDateFormat() {
        const range = ddwcposDashboardData.dateRange || {};
        const start = new Date(range.from);
        const end = new Date(range.to);
        const diffDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
        
        if (diffDays > 365) return 'quarter';
        if (diffDays > 90) return 'month';
        return 'day';
    }

    renderRevenueChart() {
        const ctx = document.getElementById('ddwcpos-revenue-chart');
        if (!ctx) return;

        const chartData = ddwcposDashboardData.revenueChart || [];
        
        if (chartData.length === 0 || chartData.every(item => parseFloat(item.revenue) === 0)) {
            this.renderEmptyState(ctx, ddwcposDashboardData.i18n.noData);
            return;
        }

        const format = this.getDateFormat();
        const labels = chartData.map(item => this.formatDate(item.date, format));
        const data = chartData.map(item => item.revenue);
        const options = this.getLineChartOptions(ddwcposDashboardData.i18n);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: ddwcposDashboardData.i18n.revenue,
                    data: data,
                    borderColor: '#0256ff',
                    backgroundColor: 'rgba(2, 86, 255, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2,
                    pointBackgroundColor: '#0256ff',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 1,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: options
        });
    }

    renderOutletsChart() {
        const ctx = document.getElementById('ddwcpos-outlets-chart');
        if (!ctx) return;

        const chartData = ddwcposDashboardData.outletsChart || [];
        
        if (chartData.length === 0) {
            this.renderEmptyState(ctx, ddwcposDashboardData.i18n.noData, 'bar');
            return;
        }

        const labels = chartData.map(item => item.outlet);
        const data = chartData.map(item => item.revenue);
        const colors = this.generateColors(chartData.length);
        const currency = ddwcposDashboardData.currency || '';

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: ddwcposDashboardData.i18n.revenue,
                    data: data,
                    backgroundColor: colors.backgrounds,
                    borderRadius: 4,
                    barPercentage: 0.6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#ffffff',
                        titleColor: '#374151',
                        bodyColor: '#374151',
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: true,
                        usePointStyle: true,
                        padding: 12,
                        boxPadding: 4,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += currency + context.parsed.y.toLocaleString();
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        border: {
                            display: false,
                            dash: [4, 4]
                        },
                        grid: {
                            color: '#f0f0f1'
                        },
                        ticks: {
                            callback: function(value) {
                                return currency + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    renderPaymentMethodsChart() {
        const ctx = document.getElementById('ddwcpos-payment-methods-chart');
        if (!ctx) return;

        const chartData = ddwcposDashboardData.paymentMethodsChart || [];
        
        if (chartData.length === 0) {
            this.renderEmptyState(ctx, ddwcposDashboardData.i18n.noData, 'donut');
            return;
        }

        const labels = chartData.map(item => item.payment_method);
        const data = chartData.map(item => item.amount);
        const colors = this.generateColors(chartData.length);

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors.backgrounds,
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: this.getDonutChartOptions(ddwcposDashboardData.i18n)
        });
    }

    renderEmptyState(canvas, message, type = 'chart') {
        const container = canvas.parentElement;
        container.innerHTML = `
            <div class="ddwcpos-empty-state">
                <div class="ddwcpos-empty-content">
                    <p>${message}</p>
                </div>
            </div>
        `;
        container.style.display = 'flex';
        container.style.alignItems = 'center';
        container.style.justifyContent = 'center';
        container.style.backgroundColor = '#f9fafb';
        container.style.borderRadius = '8px';
    }

    getLineChartOptions(i18n) {
        const currency = ddwcposDashboardData.currency || '';
        return {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    align: 'end',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        boxWidth: 6,
                        boxHeight: 6,
                        padding: 20,
                        font: {
                            family: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif',
                            size: 12
                        },
                        color: '#6b7280'
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: '#ffffff',
                    titleColor: '#111827',
                    bodyColor: '#6b7280',
                    borderColor: '#e5e7eb',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true,
                    usePointStyle: true,
                    boxWidth: 8,
                    boxHeight: 8,
                    boxPadding: 4,
                    padding: 12,
                    titleFont: {
                        size: 13,
                        weight: '600'
                    },
                    bodyFont: {
                        size: 12,
                        weight: '400'
                    },
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += currency + context.parsed.y.toLocaleString();
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    display: true,
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#9ca3af',
                        font: {
                            size: 11
                        }
                    },
                    border: {
                        display: false
                    }
                },
                y: {
                    display: true,
                    border: {
                        display: false,
                        dash: [4, 4]
                    },
                    beginAtZero: true,
                    grid: {
                        color: '#f3f4f6'
                    },
                    ticks: {
                        color: '#9ca3af',
                        font: {
                            size: 11
                        },
                        callback: function(value) {
                            return currency + value.toLocaleString();
                        }
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            },
            elements: {
                point: {
                    radius: 0,
                    hoverRadius: 6,
                    hitRadius: 10
                }
            }
        };
    }

    getDonutChartOptions(i18n) {
        const currency = ddwcposDashboardData.currency || '';
        return {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        boxWidth: 8,
                        boxHeight: 8,
                        padding: 15,
                        font: {
                            size: 12
                        },
                        color: '#6b7280'
                    }
                },
                tooltip: {
                    backgroundColor: '#ffffff',
                    titleColor: '#111827',
                    bodyColor: '#6b7280',
                    borderColor: '#e5e7eb',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true,
                    usePointStyle: true,
                    boxWidth: 8,
                    boxHeight: 8,
                    boxPadding: 4,
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1) + '%';
                            return `${label}: ${currency}${value.toLocaleString()} (${percentage})`;
                        }
                    }
                }
            },
            cutout: '70%',
            layout: {
                padding: 0
            }
        };
    }

    generateColors(count) {
        const backgrounds = [
            '#0256ff', '#3b82f6', '#60a5fa', '#93c5fd', '#dbeafe', 
            '#1e40af', '#1d4ed8', '#2563eb', '#1e3a8a', '#312e81'
        ];
        
        const colors = [];
        for (let i = 0; i < count; i++) {
            colors.push(backgrounds[i % backgrounds.length]);
        }
        
        return {
            backgrounds: colors
        };
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new DDWCPOS_Dashboard();
});
