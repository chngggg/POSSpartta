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
    // Cek apakah modal ada
    const deleteModalElement = document.getElementById("deleteModal");
    if (!deleteModalElement) {
        console.error("Delete modal element not found!");
        return;
    }

    // Inisialisasi modal Bootstrap
    let deleteModal;
    try {
        deleteModal = new bootstrap.Modal(deleteModalElement);
        console.log("Modal initialized successfully");
    } catch (e) {
        console.error("Failed to initialize modal:", e);
        return;
    }

    // Dapatkan semua tombol hapus
    const deleteButtons = document.querySelectorAll(".delete-sparepart");
    console.log("Delete buttons found:", deleteButtons.length);

    if (deleteButtons.length === 0) {
        console.warn("No delete buttons found with class .delete-sparepart");
        return;
    }

    // Tambahkan event listener ke setiap tombol hapus
    deleteButtons.forEach((btn, index) => {
        // Hapus event listener lama jika ada (untuk menghindari duplikasi)
        btn.removeEventListener("click", handleDeleteClick);
        // Tambahkan event listener baru
        btn.addEventListener("click", handleDeleteClick);
    });

    // Handler function untuk tombol hapus
    function handleDeleteClick(e) {
        e.preventDefault();
        e.stopPropagation();

        const sparepartId = this.getAttribute("data-id");
        const sparepartName = this.getAttribute("data-name");

        console.log(
            `Delete button clicked - ID: ${sparepartId}, Name: ${sparepartName}`,
        );

        if (!sparepartId) {
            console.error("Missing data-id attribute");
            showToast("Error: ID sparepart tidak ditemukan", "error");
            return;
        }

        // Update modal content
        const nameSpan = document.getElementById("deleteSparepartName");
        const deleteForm = document.getElementById("deleteForm");

        if (nameSpan) {
            nameSpan.textContent = sparepartName || "Sparepart";
        }

        if (deleteForm) {
            const actionUrl = `/spareparts/${sparepartId}`;
            deleteForm.setAttribute("action", actionUrl);
            console.log("Form action set to:", actionUrl);
        } else {
            console.error("Delete form not found!");
            showToast("Error: Form tidak ditemukan", "error");
            return;
        }

        // Tampilkan modal
        deleteModal.show();
    }
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
