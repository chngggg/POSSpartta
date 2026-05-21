// =====================================================
// SPARTTAPOS DASHBOARD - MODERN DARK THEME
// =====================================================

let salesChart = null;
let categoryChart = null;

// Wait for DOM to be fully loaded
document.addEventListener("DOMContentLoaded", function () {
    console.log("DOM Loaded - Initializing Dashboard");

    // Inisialisasi chart
    initSalesChart();
    initCategoryChart();
    initSidebarToggle();
    initTooltips();
    addLoadingAnimation();
    initPeriodFilter();

    // Auto refresh stats every 60 seconds
    setInterval(function () {
        refreshDashboardData();
    }, 60000);
});

/**
 * Sales Chart - 7 Days Sales Trend with Real Data
 */
function initSalesChart() {
    const canvas = document.getElementById("salesChart");
    if (!canvas) {
        console.error("salesChart canvas not found!");
        return;
    }

    // Hancurkan chart lama jika ada
    if (salesChart) {
        salesChart.destroy();
        salesChart = null;
    }

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

    console.log("Initializing Sales Chart with:", { labels, salesData });

    const ctx = canvas.getContext("2d");
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, "rgba(212, 175, 55, 0.4)");
    gradient.addColorStop(1, "rgba(212, 175, 55, 0.02)");

    salesChart = new Chart(ctx, {
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
                        font: { size: 11 },
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
                    grid: { color: "rgba(255, 255, 255, 0.05)" },
                    ticks: {
                        color: "#b0b0b0",
                        callback: function (value) {
                            if (value >= 1000000)
                                return (
                                    "Rp " + (value / 1000000).toFixed(1) + "jt"
                                );
                            if (value >= 1000)
                                return "Rp " + (value / 1000).toFixed(0) + "rb";
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
                        text: "Periode",
                        color: "#d4af37",
                        font: { size: 11 },
                    },
                },
            },
        },
    });

    console.log("Sales Chart initialized successfully");
}

/**
 * Category Distribution Chart with Real Data
 */
function initCategoryChart() {
    const canvas = document.getElementById("categoryChart");
    if (!canvas) {
        console.error("categoryChart canvas not found!");
        return;
    }

    // Hancurkan chart lama jika ada
    if (categoryChart) {
        categoryChart.destroy();
        categoryChart = null;
    }

    const categoryLabels = window.categoryChartLabels || [
        "Mesin",
        "Kelistrikan",
        "Kaki-kaki",
        "Body",
        "Oli",
    ];
    const categoryValues = window.categoryChartValues || [0, 0, 0, 0, 0];

    console.log("Initializing Category Chart with:", {
        categoryLabels,
        categoryValues,
    });

    categoryChart = new Chart(canvas, {
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
                        font: { size: 11 },
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
    });

    console.log("Category Chart initialized successfully");
}

/**
 * Initialize Period Filter (Minggu, Bulan, Tahun)
 */
function initPeriodFilter() {
    const periodBtns = document.querySelectorAll("[data-period]");
    const chartTitle = document.getElementById("chartTitle");

    console.log("Period buttons found:", periodBtns.length);

    if (periodBtns.length === 0) {
        console.warn("No period buttons found!");
        return;
    }

    periodBtns.forEach((btn) => {
        btn.addEventListener("click", async function () {
            // Update active state
            periodBtns.forEach((b) => b.classList.remove("active"));
            this.classList.add("active");

            const period = this.getAttribute("data-period");
            const title = this.getAttribute("data-title");

            if (chartTitle && title) {
                chartTitle.innerHTML = `<i class="fas fa-chart-line me-2"></i> ${title}`;
            }

            console.log("Fetching data for period:", period);

            try {
                const response = await fetch(
                    `/api/dashboard/stats?period=${period}`,
                );
                const data = await response.json();

                console.log("Period data response:", data);

                if (data && data.sales_data) {
                    if (salesChart) {
                        salesChart.data.labels = data.sales_labels;
                        salesChart.data.datasets[0].data = data.sales_data;
                        salesChart.update();
                    }
                }

                if (data && data.category_labels && data.category_values) {
                    if (categoryChart) {
                        categoryChart.data.labels = data.category_labels;
                        categoryChart.data.datasets[0].data =
                            data.category_values;
                        categoryChart.update();
                    }
                }

                showToast(
                    `Menampilkan data ${period === "week" ? "Mingguan" : period === "month" ? "Bulanan" : "Tahunan"}`,
                    "success",
                );
            } catch (error) {
                console.error("Error fetching period data:", error);
                showToast("Gagal memuat data", "error");
            }
        });
    });
}

/**
 * Refresh dashboard data
 */
async function refreshDashboardData() {
    try {
        const response = await fetch("/api/dashboard/stats");
        const data = await response.json();

        if (salesChart && data.sales_data) {
            salesChart.data.datasets[0].data = data.sales_data;
            salesChart.update();
        }

        if (categoryChart && data.category_values) {
            categoryChart.data.datasets[0].data = data.category_values;
            categoryChart.update();
        }
    } catch (error) {
        console.error("Error refreshing dashboard data:", error);
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

// =====================================================
// TARGET PENJUALAN FUNCTIONS
// =====================================================

window.openTargetModal = function () {
    console.log("openTargetModal called");
    const modalElement = document.getElementById("targetModal");
    if (modalElement) {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    } else {
        console.error("Modal element not found!");
    }
};

window.updateTarget = async function () {
    console.log("updateTarget called");
    const targetInput = document.getElementById("targetSalesInput");
    if (!targetInput) {
        console.error("targetSalesInput not found");
        return;
    }

    const targetValue = parseInt(targetInput.value);

    if (isNaN(targetValue) || targetValue < 0) {
        showToast("Masukkan target yang valid!", "error");
        return;
    }

    const saveButton = document.querySelector("#targetModal .btn-gold");
    const originalText = saveButton.innerHTML;
    saveButton.innerHTML =
        '<i class="fas fa-spinner fa-spin me-2"></i> Menyimpan...';
    saveButton.disabled = true;

    try {
        const response = await fetch("/dashboard/update-target", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]',
                ).content,
            },
            body: JSON.stringify({ target_sales: targetValue }),
        });

        const data = await response.json();

        if (data.success) {
            const formattedTarget = formatRupiah(targetValue);

            const targetDisplay = document.getElementById("targetSalesDisplay");
            if (targetDisplay) targetDisplay.innerText = formattedTarget;

            const targetLabel = document.querySelector(
                ".target-card .target-label",
            );
            if (targetLabel)
                targetLabel.innerText = `Target: ${formattedTarget}`;

            showToast(data.message || "Target berhasil diupdate!", "success");

            const modalElement = document.getElementById("targetModal");
            if (modalElement) {
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) modal.hide();
            }

            // Refresh dashboard data
            setTimeout(() => refreshDashboardData(), 500);
        } else {
            showToast(data.message || "Gagal update target", "error");
        }
    } catch (error) {
        console.error("Error:", error);
        showToast("Terjadi kesalahan pada server", "error");
    } finally {
        saveButton.innerHTML = originalText;
        saveButton.disabled = false;
    }
};

function showToast(message, type = "success") {
    const existingToasts = document.querySelectorAll(".toast-notification");
    existingToasts.forEach((toast) => toast.remove());

    const toast = document.createElement("div");
    toast.className = `toast-notification toast-${type}`;
    toast.innerHTML = `<i class="fas ${type === "success" ? "fa-check-circle" : "fa-exclamation-circle"} me-2"></i> ${message}`;
    document.body.appendChild(toast);

    setTimeout(() => toast.classList.add("show"), 10);
    setTimeout(() => {
        toast.classList.remove("show");
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

function formatRupiah(amount) {
    return "Rp " + amount.toLocaleString("id-ID");
}

// Export for global access
window.dashboard = {
    refreshDashboardData,
    formatRupiah,
    updateTarget: window.updateTarget,
    openTargetModal: window.openTargetModal,
};
