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
    loadTargetFromStorage();

    // Auto refresh stats every 60 seconds
    setInterval(function () {
        if (typeof dashboard !== "undefined" && dashboard.updateStats) {
            dashboard.updateStats();
        }
        refreshCharts();
        refreshTargetData();
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

// =====================================================
// TARGET PENJUALAN FUNCTIONS
// =====================================================

// Fungsi untuk membuka modal target
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

// Fungsi untuk update target
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

    // Tampilkan loading
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
            body: JSON.stringify({
                target_sales: targetValue,
            }),
        });

        const data = await response.json();
        console.log("Response:", data);

        if (data.success) {
            // Update semua tampilan target di halaman
            const formattedTarget = formatRupiah(targetValue);

            // Update target display di card utama
            const targetDisplay = document.getElementById("targetSalesDisplay");
            if (targetDisplay) targetDisplay.innerText = formattedTarget;

            // Update target label di card
            const targetLabel = document.querySelector(
                ".target-card .target-label",
            );
            if (targetLabel) {
                targetLabel.innerText = `Target: ${formattedTarget}`;
            }

            // Update current target di modal
            const currentTargetSpan = document.getElementById("currentTarget");
            if (currentTargetSpan) {
                currentTargetSpan.innerHTML = formattedTarget;
            }

            // Hitung ulang persentase
            const monthlyTotalText = document.querySelector(
                "#monthlyTotalDisplay, .target-card div:last-child strong",
            );
            let monthlyTotal = 0;

            // Ambil monthly total dari berbagai kemungkinan source
            if (data.monthly_total !== undefined) {
                monthlyTotal = data.monthly_total;
            } else {
                const monthlyTotalElement = document.getElementById(
                    "monthlyTotalDisplay",
                );
                if (monthlyTotalElement) {
                    monthlyTotal =
                        parseInt(
                            monthlyTotalElement.innerText.replace(/\D/g, ""),
                        ) || 0;
                } else {
                    const monthlyText = document.querySelector(
                        ".target-card div:last-child strong",
                    );
                    if (monthlyText) {
                        monthlyTotal =
                            parseInt(
                                monthlyText.innerText.replace(/\D/g, ""),
                            ) || 0;
                    }
                }
            }

            // Update progress bar
            const percentage =
                monthlyTotal > 0
                    ? Math.min((monthlyTotal / targetValue) * 100, 100)
                    : 0;
            const progressBar = document.querySelector(".progress-bar");
            if (progressBar) {
                progressBar.style.width = percentage + "%";
                progressBar.setAttribute("aria-valuenow", percentage);
            }

            // Update persentase teks
            const percentageText = document.querySelector(
                ".target-card .mb-2 strong",
            );
            if (percentageText) {
                percentageText.innerHTML = percentage.toFixed(1) + "%";
            }

            showToast(data.message || "Target berhasil diupdate!", "success");

            // Tutup modal
            const modalElement = document.getElementById("targetModal");
            if (modalElement) {
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) modal.hide();
            }

            // Refresh data dashboard
            setTimeout(() => {
                if (window.dashboard && window.dashboard.updateStats) {
                    window.dashboard.updateStats();
                }
                if (window.refreshCharts) {
                    window.refreshCharts();
                }
            }, 500);
        } else {
            showToast(data.message || "Gagal update target", "error");
        }
    } catch (error) {
        console.error("Error:", error);
        showToast("Terjadi kesalahan pada server: " + error.message, "error");
    } finally {
        // Reset button
        saveButton.innerHTML = originalText;
        saveButton.disabled = false;
    }
};

// Fungsi show toast notification
function showToast(message, type = "success") {
    const existingToasts = document.querySelectorAll(".toast-notification");
    existingToasts.forEach((toast) => toast.remove());

    const toast = document.createElement("div");
    toast.className = `toast-notification toast-${type}`;
    toast.innerHTML = `
            <i class="fas ${type === "success" ? "fa-check-circle" : "fa-exclamation-circle"} me-2"></i>
            ${message}
        `;
    document.body.appendChild(toast);

    setTimeout(() => toast.classList.add("show"), 10);
    setTimeout(() => {
        toast.classList.remove("show");
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Period filter functionality
document.querySelectorAll("[data-period]").forEach((btn) => {
    btn.addEventListener("click", function () {
        document.querySelectorAll("[data-period]").forEach((b) => {
            b.classList.remove("active");
        });
        this.classList.add("active");

        const period = this.getAttribute("data-period");
        console.log("Switch to period:", period);

        fetch(`/api/dashboard/stats?period=${period}`)
            .then((response) => response.json())
            .then((data) => {
                if (window.salesChart) {
                    window.salesChart.data.datasets[0].data =
                        data.sales_data || [];
                    window.salesChart.update();
                }
                if (window.categoryChart) {
                    window.categoryChart.data.datasets[0].data =
                        data.category_values || [];
                    window.categoryChart.update();
                }
            })
            .catch((error) => console.error("Error:", error));
    });
});

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
    refreshTargetData: refreshTargetData,
    formatRupiah: formatRupiah,
    updateTarget: window.updateTarget,
    openTargetModal: window.openTargetModal,
};
