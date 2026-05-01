// =====================================================
// SPARTTAPOS NOTIFICATION SYSTEM - IMPROVED
// =====================================================

class NotificationSystem {
    constructor() {
        this.pollingInterval = null;
        this.init();
    }

    init() {
        this.loadNotifications();
        this.startPolling();
        this.attachEventListeners();
    }

    startPolling() {
        // Poll for new notifications every 30 seconds
        this.pollingInterval = setInterval(() => {
            this.loadNotifications(true);
        }, 30000);
    }

    async loadNotifications(silent = false) {
        try {
            const response = await fetch("/notifications/latest");
            const data = await response.json();

            this.renderNotifications(data.notifications);
            this.updateBadge(data.unread_count);

            if (
                !silent &&
                data.unread_count > 0 &&
                this.hasNewNotifications(data.notifications)
            ) {
                this.showNewNotificationAlert();
            }
        } catch (error) {
            console.error("Error loading notifications:", error);
        }
    }

    hasNewNotifications(notifications) {
        if (!notifications || notifications.length === 0) return false;

        const currentIds = Array.from(
            document.querySelectorAll(".notification-item"),
        ).map((el) => el.dataset.id);
        const newIds = notifications.map((n) => n.id.toString());

        return newIds.some((id) => !currentIds.includes(id));
    }

    renderNotifications(notifications) {
        const container = document.getElementById("notificationList");
        if (!container) return;

        if (!notifications || notifications.length === 0) {
            container.innerHTML = `
                <div class="empty-notification">
                    <i class="fas fa-bell-slash"></i>
                    <p>Tidak ada notifikasi</p>
                </div>
            `;
            return;
        }

        container.innerHTML = notifications
            .map(
                (notification) => `
            <div class="notification-item ${!notification.is_read ? "unread" : ""}" data-id="${notification.id}">
                <div class="d-flex gap-3">
                    <div class="notification-icon" style="background: ${this.getColor(notification.type)}20; color: ${this.getColor(notification.type)}">
                        <i class="fas ${this.getIcon(notification.type)}"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-title">${this.escapeHtml(notification.title)}</div>
                        <div class="notification-message">${this.escapeHtml(notification.message)}</div>
                        <div class="notification-time">
                            <i class="far fa-clock me-1"></i>
                            ${this.formatTime(notification.created_at)}
                        </div>
                    </div>
                    <div class="notification-actions">
                        ${
                            !notification.is_read
                                ? `
                            <button class="btn-icon mark-read-btn" data-id="${notification.id}" title="Tandai sudah dibaca">
                                <i class="fas fa-check"></i>
                            </button>
                        `
                                : ""
                        }
                        <button class="btn-icon delete-btn" data-id="${notification.id}" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                ${notification.link ? `<a href="${notification.link}" class="stretched-link"></a>` : ""}
            </div>
        `,
            )
            .join("");

        // Attach event listeners to new buttons
        document.querySelectorAll(".mark-read-btn").forEach((btn) => {
            btn.addEventListener("click", (e) => {
                e.stopPropagation();
                this.markAsRead(btn.dataset.id);
            });
        });

        document.querySelectorAll(".delete-btn").forEach((btn) => {
            btn.addEventListener("click", (e) => {
                e.stopPropagation();
                this.deleteNotification(btn.dataset.id);
            });
        });
    }

    getColor(type) {
        const colors = {
            success: "#2ecc71",
            warning: "#f39c12",
            danger: "#e74c3c",
            info: "#3498db",
        };
        return colors[type] || "#d4af37";
    }

    getIcon(type) {
        const icons = {
            success: "fa-check-circle",
            warning: "fa-exclamation-triangle",
            danger: "fa-times-circle",
            info: "fa-info-circle",
        };
        return icons[type] || "fa-bell";
    }

    updateBadge(count) {
        const badge = document.getElementById("notificationBadge");
        const dropdown = document.getElementById("notificationDropdown");

        if (badge) {
            if (count > 0) {
                badge.textContent = count > 99 ? "99+" : count;
                badge.style.display = "inline-block";
                if (dropdown) dropdown.classList.add("has-notification");
            } else {
                badge.style.display = "none";
                if (dropdown) dropdown.classList.remove("has-notification");
            }
        }
    }

    async markAsRead(id) {
        try {
            const response = await fetch(`/notifications/${id}/read`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]',
                    ).content,
                },
            });

            if (response.ok) {
                const item = document.querySelector(
                    `.notification-item[data-id="${id}"]`,
                );
                if (item) {
                    item.classList.remove("unread");
                    const markBtn = item.querySelector(".mark-read-btn");
                    if (markBtn) markBtn.remove();
                }
                this.updateBadgeCount();
                this.showToast(
                    "Notifikasi ditandai sebagai sudah dibaca",
                    "success",
                );
            }
        } catch (error) {
            console.error("Error marking notification as read:", error);
        }
    }

    async markAllAsRead() {
        try {
            const response = await fetch("/notifications/mark-all-read", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]',
                    ).content,
                },
            });

            if (response.ok) {
                document
                    .querySelectorAll(".notification-item.unread")
                    .forEach((item) => {
                        item.classList.remove("unread");
                        const markBtn = item.querySelector(".mark-read-btn");
                        if (markBtn) markBtn.remove();
                    });
                this.updateBadgeCount();
                this.showToast(
                    "Semua notifikasi telah ditandai sebagai sudah dibaca",
                    "success",
                );
            }
        } catch (error) {
            console.error("Error marking all notifications as read:", error);
        }
    }

    async updateBadgeCount() {
        try {
            const response = await fetch("/notifications/unread-count");
            const data = await response.json();
            this.updateBadge(data.count);
        } catch (error) {
            console.error("Error updating badge count:", error);
        }
    }

    async deleteNotification(id) {
        if (!confirm("Hapus notifikasi ini?")) return;

        try {
            const response = await fetch(`/notifications/${id}`, {
                method: "DELETE",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]',
                    ).content,
                },
            });

            if (response.ok) {
                const item = document.querySelector(
                    `.notification-item[data-id="${id}"]`,
                );
                if (item) item.remove();
                this.updateBadgeCount();
                this.showToast("Notifikasi dihapus", "success");

                // Check if empty
                if (
                    document.querySelectorAll(".notification-item").length === 0
                ) {
                    this.loadNotifications();
                }
            }
        } catch (error) {
            console.error("Error deleting notification:", error);
        }
    }

    attachEventListeners() {
        // Mark all as read button
        const markAllBtn = document.getElementById("markAllReadBtn");
        if (markAllBtn) {
            markAllBtn.addEventListener("click", () => this.markAllAsRead());
        }

        // Refresh notifications when dropdown opens
        const dropdown = document.getElementById("notificationDropdown");
        if (dropdown) {
            dropdown.addEventListener("shown.bs.dropdown", () => {
                this.loadNotifications();
            });
        }
    }

    formatTime(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);

        if (diffMins < 1) return "Baru saja";
        if (diffMins < 60) return `${diffMins} menit yang lalu`;
        if (diffHours < 24) return `${diffHours} jam yang lalu`;
        if (diffDays < 7) return `${diffDays} hari yang lalu`;

        return date.toLocaleDateString("id-ID", {
            day: "numeric",
            month: "short",
            year: "numeric",
        });
    }

    escapeHtml(text) {
        const div = document.createElement("div");
        div.textContent = text;
        return div.innerHTML;
    }

    showNewNotificationAlert() {
        // Optional: Play sound
        // this.playSound();

        // Show browser notification if permitted
        if (Notification.permission === "granted") {
            new Notification("Notifikasi Baru", {
                body: "Anda memiliki notifikasi baru",
                icon: "/favicon.ico",
            });
        }
    }

    showToast(message, type = "info") {
        // Remove existing toasts
        document
            .querySelectorAll(".toast-notification")
            .forEach((t) => t.remove());

        const toast = document.createElement("div");
        toast.className = `toast-notification toast-${type}`;
        toast.innerHTML = `
            <i class="fas ${type === "success" ? "fa-check-circle" : "fa-info-circle"} me-2"></i>
            ${message}
        `;
        document.body.appendChild(toast);

        setTimeout(() => toast.classList.add("show"), 100);
        setTimeout(() => {
            toast.classList.remove("show");
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    playSound() {
        try {
            const audio = new Audio("/sounds/notification.mp3");
            audio.volume = 0.3;
            audio.play().catch((e) => console.log("Audio play failed:", e));
        } catch (e) {
            console.log("Audio not supported");
        }
    }
}

// Request notification permission
if ("Notification" in window && Notification.permission !== "denied") {
    Notification.requestPermission();
}

// Initialize notification system when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
    window.notificationSystem = new NotificationSystem();
});
