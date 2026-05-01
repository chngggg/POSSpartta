// =====================================================
// SPARTTAPOS DASHBOARD - MODERN DARK THEME
// =====================================================

// Wait for DOM to be fully loaded
document.addEventListener("DOMContentLoaded", function () {
    initSalesChart();
    initCategoryChart();
    initSidebarToggle();
    initTooltips();
    addLoadingAnimation();

    // Auto refresh stats every 60 seconds
    setInterval(function () {
        if (typeof dashboard !== "undefined" && dashboard.updateStats) {
            dashboard.updateStats();
        }
        refreshCharts();
    }, 60000);
});

/**
 * Sales Chart - 7 Days Sales Trend with Real Data
 */
function initSalesChart() {
    const ctx = document.getElementById("salesChart");
    if (!ctx) return;

    // Gunakan data dari window (dikirim dari server)
    const salesData = window.salesChartData || [0, 0, 0, 0, 0, 0, 0];
    const labels = window.salesChartLabels || [
        "Senin",
        "Selasa",
        "Rabu",
        "Kamis",
        "Jumat",
        "Sabtu",
        "Minggu",
    ];

    console.log("Sales Data:", salesData);
    console.log("Labels:", labels);

    // Gradient fill
    const gradient = ctx.getContext("2d").createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, "rgba(212, 175, 55, 0.4)");
    gradient.addColorStop(1, "rgba(212, 175, 55, 0.02)");

    const config = {
        type: "line",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Penjualan (Rp)",
                    data: salesData,
                    backgroundColor: gradient,
                    borderColor: "#d4af37",
                    borderWidth: 3,
                    pointBackgroundColor: "#d4af37",
                    pointBorderColor: "#0a0a0a",
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    pointBorderWidth: 2,
                    tension: 0.4,
                    fill: true,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    labels: {
                        color: "#b0b0b0",
                        font: { size: 11, family: "Inter" },
                        usePointStyle: true,
                        boxWidth: 8,
                    },
                    position: "top",
                },
                tooltip: {
                    backgroundColor: "rgba(10, 10, 10, 0.9)",
                    titleColor: "#d4af37",
                    bodyColor: "#ffffff",
                    borderColor: "#d4af37",
                    borderWidth: 1,
                    callbacks: {
                        label: function (context) {
                            return (
                                "Penjualan: Rp " +
                                context.raw.toLocaleString("id-ID")
                            );
                        },
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: "rgba(255, 255, 255, 0.05)",
                        drawBorder: false,
                    },
                    ticks: {
                        color: "#b0b0b0",
                        callback: function (value) {
                            if (value >= 1000000) {
                                return (
                                    "Rp " + (value / 1000000).toFixed(1) + "jt"
                                );
                            } else if (value >= 1000) {
                                return "Rp " + (value / 1000).toFixed(0) + "rb";
                            }
                            return "Rp " + value.toLocaleString("id-ID");
                        },
                    },
                    title: {
                        display: true,
                        text: "Nominal Penjualan",
                        color: "#d4af37",
                        font: { size: 11 },
                    },
                },
                x: {
                    grid: { display: false },
                    ticks: { color: "#b0b0b0" },
                    title: {
                        display: true,
                        text: "Hari",
                        color: "#d4af37",
                        font: { size: 11 },
                    },
                },
            },
        },
    };

    window.salesChart = new Chart(ctx, config);
}

/**
 * Category Distribution Chart with Real Data
 */
function initCategoryChart() {
    const ctx = document.getElementById("categoryChart");
    if (!ctx) return;

    const categoryLabels = window.categoryChartLabels || [
        "Mesin",
        "Kelistrikan",
        "Kaki-kaki",
        "Body",
        "Oli",
    ];
    const categoryValues = window.categoryChartValues || [0, 0, 0, 0, 0];

    console.log("Category Labels:", categoryLabels);
    console.log("Category Values:", categoryValues);

    const config = {
        type: "doughnut",
        data: {
            labels: categoryLabels,
            datasets: [
                {
                    data: categoryValues,
                    backgroundColor: [
                        "#d4af37",
                        "#f5c542",
                        "#f9e79f",
                        "#1abc9c",
                        "#16a085",
                        "#3498db",
                        "#e74c3c",
                    ],
                    borderColor: "transparent",
                    borderWidth: 0,
                    hoverOffset: 15,
                    cutout: "60%",
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: "bottom",
                    labels: {
                        color: "#b0b0b0",
                        font: { size: 11, family: "Inter" },
                        usePointStyle: true,
                        boxWidth: 10,
                        padding: 15,
                    },
                },
                tooltip: {
                    backgroundColor: "rgba(10, 10, 10, 0.9)",
                    titleColor: "#d4af37",
                    bodyColor: "#ffffff",
                    borderColor: "#d4af37",
                    borderWidth: 1,
                    callbacks: {
                        label: function (context) {
                            let total = context.dataset.data.reduce(
                                (a, b) => a + b,
                                0,
                            );
                            let percentage =
                                total > 0
                                    ? ((context.raw / total) * 100).toFixed(1)
                                    : 0;
                            return `${context.label}: ${context.raw} item (${percentage}%)`;
                        },
                    },
                },
            },
        },
    };

    window.categoryChart = new Chart(ctx, config);
}

/**
 * Refresh charts with latest data
 */
async function refreshCharts() {
    try {
        const response = await fetch("/api/dashboard/stats");
        const data = await response.json();

        if (window.salesChart && data.sales_data) {
            window.salesChart.data.datasets[0].data = data.sales_data;
            window.salesChart.update();
        }

        if (window.categoryChart && data.category_values) {
            window.categoryChart.data.datasets[0].data = data.category_values;
            window.categoryChart.update();
        }
    } catch (error) {
        console.error("Error refreshing charts:", error);
    }
}

/**
 * Sidebar Toggle Function
 */
function initSidebarToggle() {
    const sidebarCollapse = document.getElementById("sidebarCollapse");
    const sidebar = document.getElementById("sidebar");
    const content = document.getElementById("content");

    if (sidebarCollapse) {
        sidebarCollapse.addEventListener("click", function () {
            sidebar.classList.toggle("active");
            content.classList.toggle("active");
        });
    }
}

/**
 * Initialize Bootstrap Tooltips
 */
function initTooltips() {
    var tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]'),
    );
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Add Loading Animation to Cards
 */
function addLoadingAnimation() {
    const cards = document.querySelectorAll(".card-stats");
    cards.forEach((card, index) => {
        card.style.opacity = "0";
        setTimeout(() => {
            card.style.opacity = "1";
        }, index * 100);
    });
}

/**
 * Update Dashboard Stats via AJAX
 */
async function updateDashboardStats() {
    try {
        const response = await fetch("/api/dashboard/stats");
        const data = await response.json();

        animateValue("totalSparepart", data.total_sparepart || 0);
        animateValue("lowStockCount", data.low_stock_count || 0);

        if (document.getElementById("salesToday")) {
            document.getElementById("salesToday").innerText = formatRupiah(
                data.sales_today || 0,
            );
        }
        if (document.getElementById("totalTransactions")) {
            document.getElementById("totalTransactions").innerText =
                data.total_transactions || 0;
        }
    } catch (error) {
        console.error("Error updating dashboard stats:", error);
    }
}

/**
 * Animate Number Counter
 */
function animateValue(elementId, endValue) {
    const element = document.getElementById(elementId);
    if (!element) return;

    const startValue = parseInt(element.innerText.replace(/\D/g, "")) || 0;
    const duration = 500;
    const stepTime = 20;
    const steps = duration / stepTime;
    const increment = (endValue - startValue) / steps;
    let currentStep = 0;

    const timer = setInterval(() => {
        currentStep++;
        const currentValue = Math.round(startValue + increment * currentStep);
        element.innerText = currentValue;

        if (currentStep >= steps) {
            element.innerText = endValue;
            clearInterval(timer);
        }
    }, stepTime);
}

/**
 * Format Rupiah
 */
function formatRupiah(amount) {
    return "Rp " + amount.toLocaleString("id-ID");
}

// Export functions for global access
window.dashboard = {
    updateStats: updateDashboardStats,
    refreshCharts: refreshCharts,
    formatRupiah: formatRupiah,
};
