// =====================================================
// SPARTTAPOS - USER MANAGEMENT JS
// =====================================================

document.addEventListener("DOMContentLoaded", function () {
    initUserManagement();
});

function initUserManagement() {
    // Initialize toggle status
    initToggleStatus();

    // Initialize delete user
    initDeleteUser();

    // Initialize form validation (if on create/edit page)
    initFormValidation();
}

/**
 * Initialize toggle status functionality
 */
function initToggleStatus() {
    const toggles = document.querySelectorAll(".toggle-status");

    if (toggles.length === 0) {
        console.log("No toggle status found");
        return;
    }

    console.log("Toggle status found:", toggles.length);

    toggles.forEach((toggle) => {
        // Remove existing listener to avoid duplicate
        toggle.removeEventListener("change", handleToggleChange);
        toggle.addEventListener("change", handleToggleChange);
    });

    async function handleToggleChange() {
        const userId = this.dataset.id;
        const isChecked = this.checked;

        // Cari status text
        const toggleWrapper = this.closest(".toggle-wrapper");
        const statusText = toggleWrapper
            ? toggleWrapper.querySelector(".toggle-status-text")
            : null;
        const originalState = !isChecked;

        console.log("Toggle clicked - User ID:", userId, "Checked:", isChecked);

        // Disable toggle during request
        this.disabled = true;

        try {
            const response = await fetch(`/users/${userId}/toggle-status`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]',
                    ).content,
                },
            });

            const data = await response.json();

            if (data.success) {
                // Update UI
                if (statusText) {
                    statusText.textContent = data.is_active
                        ? "Aktif"
                        : "Nonaktif";
                    statusText.className = `toggle-status-text ${data.is_active ? "status-active" : "status-inactive"}`;
                }

                // Update checkbox state
                this.checked = data.is_active;

                // Show success toast
                showToast(data.message, "success");
            } else {
                // Revert toggle state
                this.checked = originalState;
                showToast(data.message, "error");
            }
        } catch (error) {
            // Revert toggle state on error
            this.checked = originalState;
            showToast("Terjadi kesalahan pada server", "error");
            console.error("Error:", error);
        } finally {
            // Re-enable toggle
            this.disabled = false;
        }
    }
}

/**
 * Initialize delete user functionality
 */
function initDeleteUser() {
    const deleteModalElement = document.getElementById("deleteModal");

    if (!deleteModalElement) {
        console.log("Delete modal not found");
        return;
    }

    console.log("Delete modal found, initializing...");

    const deleteModal = new bootstrap.Modal(deleteModalElement);
    const deleteButtons = document.querySelectorAll(".delete");

    console.log("Delete buttons found:", deleteButtons.length);

    deleteButtons.forEach((btn) => {
        // Remove existing listener to avoid duplicate
        btn.removeEventListener("click", handleDeleteClick);
        btn.addEventListener("click", handleDeleteClick);
    });

    function handleDeleteClick() {
        const userId = this.dataset.id;
        const userName = this.dataset.name;

        console.log(
            "Delete button clicked - User ID:",
            userId,
            "Name:",
            userName,
        );

        const userNameSpan = document.getElementById("deleteUserName");
        const deleteForm = document.getElementById("deleteForm");

        if (userNameSpan) {
            userNameSpan.textContent = userName;
        }

        if (deleteForm) {
            deleteForm.action = `/users/${userId}`;
            console.log("Form action set to:", deleteForm.action);
        }

        deleteModal.show();
    }
}

/**
 * Initialize form validation for create/edit pages
 */
function initFormValidation() {
    const userForm = document.getElementById("userForm");
    if (!userForm) return;

    userForm.addEventListener("submit", function (e) {
        const password = document.getElementById("password");
        const passwordConfirmation = document.getElementById(
            "password_confirmation",
        );

        // Check if password fields exist and password is filled
        if (password && password.value) {
            if (password.value.length < 6) {
                e.preventDefault();
                showToast("Password minimal 6 karakter", "error");
                password.focus();
                return false;
            }

            if (
                passwordConfirmation &&
                password.value !== passwordConfirmation.value
            ) {
                e.preventDefault();
                showToast("Konfirmasi password tidak cocok", "error");
                passwordConfirmation.focus();
                return false;
            }
        }

        return true;
    });
}

/**
 * Show toast notification
 */
function showToast(message, type = "success") {
    // Remove existing toasts
    const existingToasts = document.querySelectorAll(".toast-notification");
    existingToasts.forEach((toast) => toast.remove());

    const toast = document.createElement("div");
    toast.className = `toast-notification toast-${type}`;
    toast.innerHTML = `
        <i class="fas ${type === "success" ? "fa-check-circle" : "fa-exclamation-circle"} me-2"></i>
        ${message}
    `;

    document.body.appendChild(toast);

    // Show animation
    setTimeout(() => toast.classList.add("show"), 10);

    // Auto hide after 3 seconds
    setTimeout(() => {
        toast.classList.remove("show");
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Export for global access
window.userManagement = {
    showToast,
    initToggleStatus,
    initDeleteUser,
};
