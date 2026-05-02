// =====================================================
// SPARTTAPOS - PROFILE SETTINGS JS
// =====================================================

document.addEventListener("DOMContentLoaded", function () {
    initProfileSettings();
});

function initProfileSettings() {
    initPasswordToggle();
    initAvatarUpload();
    initFormSubmit();
}

function initPasswordToggle() {
    const toggleBtns = document.querySelectorAll(".password-toggle");

    toggleBtns.forEach((btn) => {
        btn.addEventListener("click", function () {
            const input = this.previousElementSibling;
            const type =
                input.getAttribute("type") === "password" ? "text" : "password";
            input.setAttribute("type", type);

            const icon = this.querySelector("i");
            if (type === "text") {
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        });
    });
}

function initAvatarUpload() {
    const avatarInput = document.getElementById("avatar_input");
    const uploadBtn = document.getElementById("upload_btn");

    if (uploadBtn && avatarInput) {
        uploadBtn.addEventListener("click", function () {
            avatarInput.click();
        });

        avatarInput.addEventListener("change", function (e) {
            const file = e.target.files[0];
            if (file) {
                previewAvatar(file);
                uploadAvatar(file);
            }
        });
    }
}

function previewAvatar(file) {
    const reader = new FileReader();
    reader.onload = function (e) {
        const img = document.getElementById("avatar_img");
        const placeholder = document.querySelector(".avatar-placeholder");

        if (img) {
            img.src = e.target.result;
            img.style.display = "block";
            if (placeholder) placeholder.style.display = "none";
        }
    };
    reader.readAsDataURL(file);
}

function uploadAvatar(file) {
    const formData = new FormData();
    formData.append("avatar", file);
    formData.append(
        "_token",
        document.querySelector('meta[name="csrf-token"]').content,
    );

    fetch("/settings/upload-avatar", {
        method: "POST",
        body: formData,
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                showToast(data.message, "success");
            } else {
                showToast(data.message, "error");
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            showToast("Gagal upload avatar", "error");
        });
}

function initFormSubmit() {
    const form = document.getElementById("profileForm");
    const changePasswordForm = document.getElementById("changePasswordForm");

    if (form) {
        form.addEventListener("submit", function (e) {
            const name = document.getElementById("name").value;
            const email = document.getElementById("email").value;

            if (!name || !email) {
                e.preventDefault();
                showToast("Nama dan Email wajib diisi!", "error");
                return false;
            }

            showToast("Menyimpan perubahan...", "info");
        });
    }

    if (changePasswordForm) {
        changePasswordForm.addEventListener("submit", function (e) {
            const newPassword = document.getElementById("new_password").value;
            const confirmPassword =
                document.getElementById("confirm_password").value;

            if (newPassword !== confirmPassword) {
                e.preventDefault();
                showToast("Konfirmasi password tidak cocok!", "error");
                return false;
            }

            if (newPassword.length < 6) {
                e.preventDefault();
                showToast("Password minimal 6 karakter!", "error");
                return false;
            }
        });
    }
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
