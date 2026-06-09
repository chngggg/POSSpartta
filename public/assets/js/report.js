// =====================================================
// SPARTTAPOS - REPORT JS
// =====================================================

document.addEventListener("DOMContentLoaded", function () {
    // Inisialisasi form submit
    const form = document.querySelector(".form-card form");
    if (form) {
        form.addEventListener("submit", function () {
            const btn = this.querySelector('button[type="submit"]');
            if (btn) {
                btn.innerHTML =
                    '<i class="fas fa-spinner fa-spin me-2"></i> Loading...';
                btn.disabled = true;
            }
        });
    }

    // Show toast notification
    window.showToast = function (message, type = "success") {
        const existingToasts = document.querySelectorAll(".toast-notification");
        existingToasts.forEach((toast) => toast.remove());

        const toast = document.createElement("div");
        toast.className = `toast-notification toast-${type}`;
        toast.innerHTML = `<i class="fas ${type === "success" ? "fa-check-circle" : "fa-exclamation-circle"} me-2"></i>${message}`;
        document.body.appendChild(toast);

        setTimeout(() => toast.classList.add("show"), 10);
        setTimeout(() => toast.remove(), 3000);
    };
});
