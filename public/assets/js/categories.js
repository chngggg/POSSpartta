// =====================================================
// SPARTTAPOS - CATEGORY MANAGEMENT JS
// =====================================================

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

document.addEventListener("DOMContentLoaded", function () {
    // Search functionality
    const searchInput = document.getElementById("searchCategory");
    if (searchInput) {
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

    // Delete modal handling
    const deleteModal = new bootstrap.Modal(
        document.getElementById("deleteModal"),
    );
    const deleteButtons = document.querySelectorAll(".delete-category");
    const warningMessage = document.getElementById("warningMessage");
    const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");

    deleteButtons.forEach((btn) => {
        btn.addEventListener("click", function () {
            const categoryId = this.dataset.id;
            const categoryName = this.dataset.name;
            const hasSparepart = this.dataset.hasSparepart === "true";

            document.getElementById("deleteCategoryName").textContent =
                categoryName;
            document.getElementById("deleteForm").action =
                `/categories/${categoryId}`;

            if (hasSparepart) {
                warningMessage.style.display = "block";
                confirmDeleteBtn.disabled = true;
                confirmDeleteBtn.style.opacity = "0.5";
                confirmDeleteBtn.style.cursor = "not-allowed";
            } else {
                warningMessage.style.display = "none";
                confirmDeleteBtn.disabled = false;
                confirmDeleteBtn.style.opacity = "1";
                confirmDeleteBtn.style.cursor = "pointer";
            }

            deleteModal.show();
        });
    });
});

const deleteModal = document.getElementById("deleteModal");

deleteModal.addEventListener("hidden.bs.modal", function () {
    location.reload();
});

window.categories = {
    showToast,
};
