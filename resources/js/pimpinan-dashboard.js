window.pimpinanDash = function() {
    return {
        init() {
            this.$nextTick(() => {
                if(typeof window.pimpinanDashboardData !== 'undefined') {
                    this.buildCharts();
                }
            });
        },

        buildCharts() {
            const data   = window.pimpinanDashboardData;
            const isDark = document.documentElement.classList.contains('dark');

            // ---- Palette berdasarkan mode ----
            const palette = {
                text:        isDark ? '#94a3b8' : '#6b7280',
                grid:        isDark ? 'rgba(51,65,85,0.5)' : '#f3f4f6',
                tooltipBg:   isDark ? 'rgba(15,23,42,0.95)' : 'rgba(0,0,0,0.8)',
                cardBg:      isDark ? '#1e293b' : '#ffffff',
                numberText:  isDark ? '#f1f5f9' : '#1f2937',
                labelText:   isDark ? '#94a3b8' : '#6b7280',
                doughnutBorder: isDark ? '#1e293b' : '#ffffff',
            };

            Chart.defaults.font.family = "Inter, ui-sans-serif, system-ui, sans-serif";
            Chart.defaults.color       = palette.text;

            // ---- Helper gradien ----
            const getGradient = (ctx, chartArea, colorStart, colorEnd) => {
                const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                gradient.addColorStop(0, colorStart);
                gradient.addColorStop(1, colorEnd);
                return gradient;
            };

            // ---- Plugin teks tengah doughnut ----
            const centerTextPlugin = {
                id: 'centerText',
                beforeDraw: function(chart) {
                    if (chart.config.type !== 'doughnut') return;
                    const ctx   = chart.ctx;
                    const width = chart.width;
                    const textY = chart.chartArea.top + (chart.chartArea.bottom - chart.chartArea.top) / 2;

                    ctx.restore();

                    ctx.textBaseline = "middle";
                    ctx.textAlign    = "center";

                    ctx.font      = "bold 32px Inter, sans-serif";
                    ctx.fillStyle = palette.numberText;
                    ctx.fillText(data.totalAssets, width / 2, textY - 8);

                    ctx.font      = "600 10px Inter, sans-serif";
                    ctx.fillStyle = palette.labelText;
                    ctx.fillText("TOTAL ASET", width / 2, textY + 16);

                    ctx.save();
                }
            };

            // ---- Opsi umum doughnut ----
            const doughnutOptions = {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '80%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 20,
                            font: { size: 11, weight: '600' },
                            color: palette.text,
                        }
                    },
                    tooltip: {
                        backgroundColor: palette.tooltipBg,
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { size: 13, weight: 'bold' },
                        titleColor: '#f1f5f9',
                        bodyColor: '#94a3b8',
                    }
                }
            };

            // ====================================================
            // TREND BAR CHART
            // ====================================================
            const trendEl = document.getElementById('trendChart');
            if (trendEl) {
                new Chart(trendEl.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: data.trendLabels,
                        datasets: [{
                            label: 'Laporan Masuk',
                            data: data.trendValues,
                            backgroundColor: function(context) {
                                const chart = context.chart;
                                const {ctx, chartArea} = chart;
                                if (!chartArea) return isDark ? '#3b82f6' : '#005A8C';
                                return getGradient(ctx, chartArea,
                                    isDark ? '#1d4ed8' : '#005A8C',
                                    isDark ? '#60a5fa' : '#3b82f6'
                                );
                            },
                            borderRadius: 6,
                            barThickness: 'flex',
                            maxBarThickness: 32,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: palette.tooltipBg,
                                padding: 12,
                                cornerRadius: 8,
                                titleColor: '#f1f5f9',
                                bodyColor: '#94a3b8',
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    font: { size: 11 },
                                    color: palette.text,
                                },
                                grid: {
                                    color: palette.grid,
                                    drawBorder: false
                                },
                                border: { color: 'transparent' },
                            },
                            x: {
                                grid: { display: false },
                                ticks: {
                                    font: { size: 11, weight: '600' },
                                    color: palette.text,
                                },
                                border: { color: 'transparent' },
                            }
                        }
                    }
                });
            }

            // ====================================================
            // CONDITION DOUGHNUT
            // ====================================================
            const condEl = document.getElementById('conditionChart');
            if (condEl) {
                new Chart(condEl.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Baik', 'Rusak Ringan', 'Rusak Berat'],
                        datasets: [{
                            data: [data.baikAssets, data.rusakRinganAssets, data.rusakBeratAssets],
                            backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                            borderWidth: isDark ? 3 : 4,
                            borderColor: palette.doughnutBorder,
                            hoverOffset: 12,
                        }]
                    },
                    options: doughnutOptions,
                    plugins: [centerTextPlugin]
                });
            }

            // ====================================================
            // AGE DISTRIBUTION DOUGHNUT
            // ====================================================
            const ageEl = document.getElementById('ageChart');
            if (ageEl) {
                new Chart(ageEl.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: data.ageDistLabels,
                        datasets: [{
                            data: data.ageDistValues,
                            backgroundColor: ['#3b82f6', '#8b5cf6', '#ec4899', '#f97316'],
                            borderWidth: isDark ? 3 : 4,
                            borderColor: palette.doughnutBorder,
                            hoverOffset: 12,
                        }]
                    },
                    options: doughnutOptions,
                    plugins: [centerTextPlugin]
                });
            }
        }
    };
}
