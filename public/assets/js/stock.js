// =====================================================
// SPARTTAPOS - STOCK OPNAME JS
// =====================================================

document.addEventListener("DOMContentLoaded", function () {
    initStockOpname();
});

function initStockOpname() {
    initDeleteHandler();
    initFormSubmit();
    initPhysicalStockInput();
}

function initDeleteHandler() {
    const deleteButtons = document.querySelectorAll(".delete-item");
    const deleteModal = document.getElementById("deleteModal");

    console.log("Delete buttons found:", deleteButtons.length);

    if (!deleteModal) {
        console.error("Delete modal not found");
        return;
    }

    const modal = new bootstrap.Modal(deleteModal);
    const deleteItemName = document.getElementById("deleteItemName");
    const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");

    if (!confirmDeleteBtn) {
        console.error("Confirm delete button not found");
        return;
    }

    let currentDeleteId = null;

    // Ketika tombol hapus diklik
    deleteButtons.forEach((btn) => {
        btn.removeEventListener("click", handleDeleteClick);
        btn.addEventListener("click", handleDeleteClick);
    });

    function handleDeleteClick(e) {
        e.preventDefault();
        currentDeleteId = this.dataset.id;
        const name = this.dataset.name;

        console.log("Delete clicked - ID:", currentDeleteId, "Name:", name);

        if (deleteItemName) {
            deleteItemName.textContent = name;
        }

        modal.show();
    }

    // Ketika tombol konfirmasi hapus diklik
    confirmDeleteBtn.removeEventListener("click", handleConfirmDelete);
    confirmDeleteBtn.addEventListener("click", handleConfirmDelete);

    async function handleConfirmDelete() {
        if (!currentDeleteId) return;

        // Disable button dan change text
        confirmDeleteBtn.disabled = true;
        confirmDeleteBtn.innerHTML =
            '<i class="fas fa-spinner fa-spin me-1"></i> Menghapus...';

        try {
            const response = await fetch(`/stock/opname/${currentDeleteId}`, {
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
            console.log("Response:", data);

            if (data.success) {
                showToast(data.message, "success");
                modal.hide();

                // Auto reload setelah 1.5 detik
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

function initFormSubmit() {
    const form = document.getElementById("stockOpnameForm");
    if (!form) return;

    form.addEventListener("submit", function (e) {
        const items = document.querySelectorAll(".physical-stock-input");
        let hasError = false;

        items.forEach((input) => {
            const value = parseInt(input.value);
            if (isNaN(value) || value < 0) {
                input.classList.add("is-invalid");
                hasError = true;
            } else {
                input.classList.remove("is-invalid");
            }
        });

        if (hasError) {
            e.preventDefault();
            showToast("Mohon isi semua stok fisik dengan benar", "error");
        }
    });
}

function initPhysicalStockInput() {
    const inputs = document.querySelectorAll(".physical-stock-input");

    inputs.forEach((input) => {
        input.addEventListener("change", function () {
            const systemStock = parseInt(this.dataset.systemStock);
            const physicalStock = parseInt(this.value) || 0;
            const difference = physicalStock - systemStock;
            const diffElement =
                this.closest(".item-row")?.querySelector(".difference-value");

            if (diffElement) {
                diffElement.textContent = formatNumber(difference);
                diffElement.className = `difference-value ${difference >= 0 ? "diff-up" : "diff-down"}`;
            }
        });
    });
}

function formatNumber(num) {
    return num.toLocaleString("id-ID");
}

function formatRupiah(amount) {
    return "Rp " + amount.toLocaleString("id-ID");
}

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

// Export functions
window.stock = {
    showToast,
    formatRupiah,
    formatNumber,
};
