/**
 * AgriSync — Dashboard & Analytics Charts Initializer (TASK-088)
 * Uses Chart.js 4.x to render price trends, crop volume distributions, and SDG impact metrics.
 */

document.addEventListener('DOMContentLoaded', function () {
    const priceCanvas = document.getElementById('priceTrendChart');
    const cropDistCanvas = document.getElementById('cropDistChart');
    const districtDistCanvas = document.getElementById('districtDistChart');

    if (!priceCanvas && !cropDistCanvas && !districtDistCanvas) {
        return;
    }

    // Fetch live analytics data
    fetch('/api/get_analytics.php')
        .then(response => response.json())
        .then(res => {
            if (!res.success || !res.data) return;
            const data = res.data;

            // 1. Price Trends Line Chart
            if (priceCanvas && data.price_trends) {
                new Chart(priceCanvas, {
                    type: 'line',
                    data: {
                        labels: data.price_trends.labels,
                        datasets: data.price_trends.datasets.map(ds => ({
                            label: ds.label,
                            data: ds.data,
                            borderColor: ds.borderColor,
                            backgroundColor: ds.backgroundColor,
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2.5,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }))
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { boxWidth: 12, font: { family: 'Inter' } } },
                            tooltip: { callbacks: { label: (ctx) => ` Rs. ${ctx.parsed.y}/kg` } }
                        },
                        scales: {
                            y: {
                                beginAtZero: false,
                                ticks: { callback: (val) => `Rs. ${val}` },
                                grid: { color: '#f1f1f1' }
                            },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }

            // 2. Crop Volume Distribution Doughnut Chart
            if (cropDistCanvas && data.crop_distribution) {
                const labels = data.crop_distribution.map(item => item.crop_type);
                const values = data.crop_distribution.map(item => item.volume_kg);
                const colors = ['#2D6A4F', '#40916C', '#52B788', '#74C69D', '#95D5B2', '#D8F3DC'];

                new Chart(cropDistCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values,
                            backgroundColor: colors,
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { boxWidth: 12, font: { family: 'Inter' } } },
                            tooltip: { callbacks: { label: (ctx) => ` ${ctx.label}: ${ctx.parsed} kg` } }
                        },
                        cutout: '65%'
                    }
                });
            }

            // 3. District Volume Distribution Bar Chart
            if (districtDistCanvas && data.district_distribution) {
                const dLabels = data.district_distribution.map(item => item.district);
                const dValues = data.district_distribution.map(item => item.volume_kg);

                new Chart(districtDistCanvas, {
                    type: 'bar',
                    data: {
                        labels: dLabels,
                        datasets: [{
                            label: 'Available Supply (kg)',
                            data: dValues,
                            backgroundColor: '#2D6A4F',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { callbacks: { label: (ctx) => ` ${ctx.parsed.y} kg` } }
                        },
                        scales: {
                            y: { beginAtZero: true, grid: { color: '#f1f1f1' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }
        })
        .catch(err => console.error('Error loading analytics charts:', err));
});
