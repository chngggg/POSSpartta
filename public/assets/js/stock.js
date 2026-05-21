// =====================================================
// SPARTTAPOS - STOCK OPNAME JS
// =====================================================

document.addEventListener("DOMContentLoaded", function () {
    initStockOpname();
});

function initStockOpname() {
    initDeleteHandler();
    initPhysicalStockInput();
    initFormSubmit();
}

/**
 * Initialize delete handler for stock opname
 */
function initDeleteHandler() {
    const deleteButtons = document.querySelectorAll(".delete-item");
    const deleteModal = document.getElementById("deleteModal");

    if (!deleteModal || deleteButtons.length === 0) return;

    const modal = new bootstrap.Modal(deleteModal);
    const deleteItemName = document.getElementById("deleteItemName");
    const deleteForm = document.getElementById("deleteForm");
    const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");

    if (!confirmDeleteBtn || !deleteForm) return;

    let currentDeleteId = null;

    deleteButtons.forEach((btn) => {
        btn.removeEventListener("click", handleDeleteClick);
        btn.addEventListener("click", handleDeleteClick);
    });

    function handleDeleteClick(e) {
        e.preventDefault();
        currentDeleteId = this.dataset.id;
        const name = this.dataset.name;

        if (deleteItemName) {
            deleteItemName.textContent = name;
        }

        if (deleteForm) {
            deleteForm.action = `/stock-opname/${currentDeleteId}`;
        }

        modal.show();
    }

    confirmDeleteBtn.removeEventListener("click", handleConfirmDelete);
    confirmDeleteBtn.addEventListener("click", handleConfirmDelete);

    async function handleConfirmDelete() {
        if (!currentDeleteId) return;

        confirmDeleteBtn.disabled = true;
        confirmDeleteBtn.innerHTML =
            '<i class="fas fa-spinner fa-spin me-1"></i> Menghapus...';

        try {
            const response = await fetch(`/stock-opname/${currentDeleteId}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]',
                    ).content,
                    Accept: "application/json",
                    "Content-Type": "application/json",
                },
            });

            const data = await response.json();

            if (data.success) {
                showToast(data.message, "success");
                modal.hide();
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showToast(data.message || "Gagal menghapus data", "error");
                confirmDeleteBtn.disabled = false;
                confirmDeleteBtn.innerHTML =
                    '<i class="fas fa-trash-alt me-1"></i> Hapus';
            }
        } catch (error) {
            console.error("Error:", error);
            showToast("Terjadi kesalahan pada server", "error");
            confirmDeleteBtn.disabled = false;
            confirmDeleteBtn.innerHTML =
                '<i class="fas fa-trash-alt me-1"></i> Hapus';
        }
    }
}

/**
 * Initialize physical stock input with auto-calculate difference
 */
function initPhysicalStockInput() {
    const inputs = document.querySelectorAll(".physical-stock");

    inputs.forEach((input) => {
        input.addEventListener("input", function () {
            const systemStock = parseInt(this.dataset.system) || 0;
            const physicalStock = parseInt(this.value) || 0;
            const difference = physicalStock - systemStock;

            const row = this.closest("tr");
            const diffCell = row ? row.querySelector(".diff-cell") : null;

            if (diffCell) {
                diffCell.textContent =
                    difference >= 0 ? `+${difference}` : `${difference}`;
                diffCell.className = `diff-cell ${difference > 0 ? "diff-up" : difference < 0 ? "diff-down" : "diff-zero"}`;
            }
        });
    });
}

/**
 * Initialize form submit validation
 */
function initFormSubmit() {
    const form = document.getElementById("stockOpnameForm");
    if (!form) return;

    form.addEventListener("submit", function (e) {
        const submitter = e.submitter;
        const status = submitter ? submitter.value : null;

        if (status === "draft") {
            return true;
        }

        // Finalisasi: validate all physical stock inputs
        const inputs = document.querySelectorAll(".physical-stock");
        let hasEmpty = false;

        inputs.forEach((input) => {
            const val = parseInt(input.value);
            if (isNaN(val)) {
                hasEmpty = true;
                input.classList.add("is-invalid");
            } else {
                input.classList.remove("is-invalid");
            }
        });

        if (hasEmpty) {
            e.preventDefault();
            showToast(
                "Mohon isi semua stok fisik sebelum finalisasi!",
                "error",
            );
        }
    });
}

/**
 * Format number with thousand separator
 */
function formatNumber(num) {
    return num.toLocaleString("id-ID");
}

/**
 * Format currency to Rupiah
 */
function formatRupiah(amount) {
    return "Rp " + amount.toLocaleString("id-ID");
}

/**
 * Show toast notification
 */
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

// Export functions for global access
window.stock = {
    showToast: showToast,
    formatRupiah: formatRupiah,
    formatNumber: formatNumber,
};
