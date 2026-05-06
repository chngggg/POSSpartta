// =====================================================
// SPARTTAPOS - SPAREPART MANAGEMENT JS
// =====================================================

document.addEventListener("DOMContentLoaded", function () {
    console.log("Spareparts JS loaded - Version 2.0");
    initDeleteSparepart();
    initSearchSparepart();
    initStockAlert();
});

/**
 * Initialize delete sparepart functionality
 */
function initDeleteSparepart() {
    const deleteModalElement = document.getElementById("deleteModal");
    if (!deleteModalElement) {
        console.error("Delete modal element not found!");
        return;
    }

    let deleteModal;
    try {
        deleteModal = new bootstrap.Modal(deleteModalElement);
    } catch (e) {
        console.error("Failed to initialize modal:", e);
        return;
    }

    const deleteButtons = document.querySelectorAll(".delete-sparepart");

    if (deleteButtons.length === 0) {
        console.warn("No delete buttons found");
        return;
    }

    deleteButtons.forEach((btn) => {
        btn.removeEventListener("click", handleDeleteClick);
        btn.addEventListener("click", handleDeleteClick);
    });

    function handleDeleteClick(e) {
        e.preventDefault();
        e.stopPropagation();

        const sparepartId = this.getAttribute("data-id");
        const sparepartName = this.getAttribute("data-name");

        if (!sparepartId) {
            showToast("Error: ID sparepart tidak ditemukan", "error");
            return;
        }

        const nameSpan = document.getElementById("deleteSparepartName");
        const deleteForm = document.getElementById("deleteForm");

        if (nameSpan) {
            nameSpan.textContent = sparepartName || "Sparepart";
        }

        if (deleteForm) {
            deleteForm.setAttribute("action", `/spareparts/${sparepartId}`);
        } else {
            showToast("Error: Form tidak ditemukan", "error");
            return;
        }

        deleteModal.show();
    }

    // =========================
    // 🔄 AUTO RELOAD LOGIC
    // =========================

    let isSubmitting = false;

    const deleteForm = document.getElementById("deleteForm");

    if (deleteForm) {
        deleteForm.addEventListener("submit", function () {
            isSubmitting = true;

            const btn = document.getElementById("confirmDeleteBtn");
            if (btn) {
                btn.innerHTML = "Menghapus...";
                btn.disabled = true;
            }

            // fallback reload (kalau tidak redirect dari Laravel)
            setTimeout(() => {
                location.reload();
            }, 800);
        });
    }

    // reload hanya jika modal ditutup tanpa submit (Batal)
    deleteModalElement.addEventListener("hidden.bs.modal", function () {
        if (!isSubmitting) {
            location.reload();
        }
    });
}

/**
 * Initialize search functionality
 */
function initSearchSparepart() {
    const searchInput = document.getElementById("searchSparepart");
    if (!searchInput) return;

    let searchTimeout;

    searchInput.addEventListener("input", function () {
        clearTimeout(searchTimeout);

        searchTimeout = setTimeout(() => {
            const keyword = this.value;
            const currentUrl = new URL(window.location.href);

            if (keyword) {
                currentUrl.searchParams.set("search", keyword);
            } else {
                currentUrl.searchParams.delete("search");
            }

            window.location.href = currentUrl.toString();
        }, 500);
    });
}

/**
 * Initialize stock alert
 */
function initStockAlert() {
    const lowStockItems = document.querySelectorAll(
        ".stock-critical, .stock-low",
    );

    lowStockItems.forEach((item) => {
        const stock = parseInt(item.getAttribute("data-stock"));
        const minStock = parseInt(item.getAttribute("data-min-stock"));
        const name = item.getAttribute("data-name");

        if (stock <= minStock && stock > 0) {
            showLowStockToast(name, stock, minStock);
        }
    });
}

/**
 * Show low stock toast notification
 */
function showLowStockToast(name, stock, minStock) {
    const toast = document.createElement("div");
    toast.className = "toast-notification toast-warning";
    toast.innerHTML = `
        <i class="fas fa-exclamation-triangle me-2"></i>
        Stok ${name} menipis! Tersisa ${stock} pcs (Min: ${minStock})
    `;
    document.body.appendChild(toast);

    setTimeout(() => toast.classList.add("show"), 100);
    setTimeout(() => {
        toast.classList.remove("show");
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}

/**
 * Show general toast notification
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

// Export untuk global access
window.sparepart = {
    showToast,
    showLowStockToast,
    initDeleteSparepart,
    initSearchSparepart,
};
