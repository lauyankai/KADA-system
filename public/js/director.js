// Helper function for status colors
function getStatusColor(status) {
    switch(status.toLowerCase()) {
        case 'deposit':
        case 'approved':
            return 'success';
        case 'pending':
            return 'warning';
        case 'withdrawal':
        case 'rejected':
            return 'danger';
        default:
            return 'secondary';
    }
}

// Initialize dashboard charts
function initDashboardCharts(membershipTrends, financialTrends) {
    // Membership Growth Chart
    const membershipCtx = document.getElementById('membershipChart').getContext('2d');
    const membershipData = membershipTrends;

    new Chart(membershipCtx, {
        type: 'line',
        data: {
            labels: membershipData.map(item => {
                const date = new Date(item.month + '-01');
                return date.toLocaleDateString('ms-MY', { month: 'short', year: 'numeric' });
            }),
            datasets: [
                {
                    label: 'Ahli Baru',
                    data: membershipData.map(item => item.new_members),
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderWidth: 1.5,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Jumlah Ahli',
                    data: membershipData.map(item => item.total_members),
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    borderWidth: 1.5,
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 1.8,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + ' orang';
                        }
                    },
                    titleFont: { size: 11 },
                    bodyFont: { size: 11 },
                    padding: 8
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        font: { size: 10 },
                        maxRotation: 0,
                        autoSkip: true,
                        maxTicksLimit: 6
                    },
                    border: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        borderDash: [2, 2],
                        drawBorder: false
                    },
                    ticks: {
                        font: { size: 10 },
                        maxTicksLimit: 5,
                        padding: 5,
                        callback: function(value) {
                            return value + ' orang';
                        }
                    },
                    border: {
                        display: false
                    }
                }
            },
            layout: {
                padding: 0
            }
        }
    });

    // Financial Trends Chart
    const ctx = document.getElementById('financialTrendChart').getContext('2d');
    const loanGradient = ctx.createLinearGradient(0, 0, 0, 400);
    loanGradient.addColorStop(0, 'rgba(37, 99, 235, 0.15)');
    loanGradient.addColorStop(1, 'rgba(37, 99, 235, 0.01)');

    const savingsGradient = ctx.createLinearGradient(0, 0, 0, 400);
    savingsGradient.addColorStop(0, 'rgba(16, 185, 129, 0.15)');
    savingsGradient.addColorStop(1, 'rgba(16, 185, 129, 0.01)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: financialTrends.labels,
            datasets: [{
                label: 'Pembiayaan',
                data: financialTrends.loans,
                borderColor: '#2563eb',
                backgroundColor: loanGradient,
                borderWidth: 3,
                pointRadius: 0,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: '#ffffff',
                pointHoverBorderColor: '#2563eb',
                pointHoverBorderWidth: 3,
                tension: 0.3,
                fill: true
            }, {
                label: 'Simpanan',
                data: financialTrends.savings,
                borderColor: '#10b981',
                backgroundColor: savingsGradient,
                borderWidth: 3,
                pointRadius: 0,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: '#ffffff',
                pointHoverBorderColor: '#10b981',
                pointHoverBorderWidth: 3,
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 1.5,
            layout: {
                padding: {
                    top: 10,
                    right: 10,
                    bottom: 10,
                    left: 10
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    position: 'top',
                    align: 'right',
                    labels: {
                        boxWidth: 10,
                        boxHeight: 10,
                        useBorderRadius: true,
                        borderRadius: 2,
                        font: {
                            size: 13,
                            weight: '500',
                            family: "'Inter', sans-serif"
                        }
                    }
                },
                tooltip: {
                    backgroundColor: '#ffffff',
                    titleColor: '#111827',
                    titleFont: {
                        size: 13,
                        weight: '600',
                        family: "'Inter', sans-serif"
                    },
                    bodyColor: '#6b7280',
                    bodyFont: {
                        size: 12,
                        family: "'Inter', sans-serif"
                    },
                    padding: {
                        top: 10,
                        right: 15,
                        bottom: 20,
                        left: 10
                    },
                    borderColor: 'rgba(0, 0, 0, 0.1)',
                    borderWidth: 1,
                    displayColors: true,
                    boxWidth: 8,
                    boxHeight: 8,
                    usePointStyle: true,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': RM ';
                            }
                            label += context.parsed.y.toLocaleString('en-MY', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            size: 12,
                            family: "'Inter', sans-serif"
                        },
                        color: '#6b7280',
                        padding: 10
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.03)',
                        drawBorder: false,
                        lineWidth: 1
                    },
                    ticks: {
                        font: {
                            size: 12,
                            family: "'Inter', sans-serif"
                        },
                        color: '#6b7280',
                        padding: 10,
                        callback: function(value) {
                            if (value >= 1000000) {
                                return 'RM ' + (value / 1000000).toFixed(2) + ' M';
                            } else if (value >= 1000) {
                                return 'RM ' + (value / 1000).toFixed(2) + ' K';
                            }
                            return 'RM ' + value;
                        }
                    }
                }
            }
        }
    });
} 