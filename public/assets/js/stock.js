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

    if (deleteModal) {
        const modal = new bootstrap.Modal(deleteModal);

        deleteButtons.forEach((btn) => {
            btn.addEventListener("click", function () {
                const id = this.dataset.id;
                const name = this.dataset.name;
                const form = document.getElementById("deleteForm");

                document.getElementById("deleteItemName").textContent = name;
                form.action = `/stock/opname/${id}`;

                modal.show();
            });
        });
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
