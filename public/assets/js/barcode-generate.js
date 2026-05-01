// =====================================================
// SPARTTAPOS - BARCODE GENERATE JS
// =====================================================

document.addEventListener("DOMContentLoaded", function () {
    initBarcodeGenerate();
});

function initBarcodeGenerate() {
    initCheckboxHandlers();
    initSearchFilter();
    initSelectAll();
    initFormSubmit();
    updateSelectedCount();
}

/**
 * Initialize checkbox handlers
 */
function initCheckboxHandlers() {
    const checkboxes = document.querySelectorAll(".sparepart-checkbox");
    const cards = document.querySelectorAll(".sparepart-card");

    // Add click handler to cards
    cards.forEach((card, index) => {
        const checkbox = checkboxes[index];

        card.addEventListener("click", function (e) {
            // Prevent if clicking on checkbox directly
            if (e.target.type !== "checkbox") {
                checkbox.checked = !checkbox.checked;
                toggleCardSelection(card, checkbox.checked);
                updateSelectedCount();
            }
        });

        checkbox.addEventListener("change", function () {
            toggleCardSelection(card, this.checked);
            updateSelectedCount();
        });
    });
}

/**
 * Toggle card selection style
 */
function toggleCardSelection(card, isSelected) {
    if (isSelected) {
        card.classList.add("selected");
    } else {
        card.classList.remove("selected");
    }
}

/**
 * Initialize search filter
 */
function initSearchFilter() {
    const searchInput = document.getElementById("searchSparepart");
    if (!searchInput) return;

    searchInput.addEventListener("input", function () {
        const keyword = this.value.toLowerCase();
        const cards = document.querySelectorAll(".sparepart-card");

        cards.forEach((card) => {
            const name = card.dataset.name || "";
            const code = card.dataset.code || "";

            if (name.includes(keyword) || code.includes(keyword)) {
                card.style.display = "flex";
            } else {
                card.style.display = "none";
            }
        });
    });
}

/**
 * Initialize select all / deselect all
 */
function initSelectAll() {
    const selectAllBtn = document.getElementById("selectAllBtn");
    const deselectAllBtn = document.getElementById("deselectAllBtn");
    const checkboxes = document.querySelectorAll(".sparepart-checkbox");
    const cards = document.querySelectorAll(".sparepart-card");

    if (selectAllBtn) {
        selectAllBtn.addEventListener("click", function () {
            checkboxes.forEach((checkbox, index) => {
                checkbox.checked = true;
                toggleCardSelection(cards[index], true);
            });
            updateSelectedCount();
            showToast("Semua sparepart dipilih", "success");
        });
    }

    if (deselectAllBtn) {
        deselectAllBtn.addEventListener("click", function () {
            checkboxes.forEach((checkbox, index) => {
                checkbox.checked = false;
                toggleCardSelection(cards[index], false);
            });
            updateSelectedCount();
            showToast("Pilihan dibatalkan", "info");
        });
    }
}

/**
 * Update selected count display
 */
function updateSelectedCount() {
    const selected = document.querySelectorAll(".sparepart-checkbox:checked");
    const countSpan = document.querySelector("#selectedCount span");
    const printBtn = document.getElementById("printSelectedBtn");

    if (countSpan) {
        countSpan.textContent = selected.length;
    }

    if (printBtn) {
        printBtn.disabled = selected.length === 0;
        if (selected.length === 0) {
            printBtn.style.opacity = "0.5";
            printBtn.style.cursor = "not-allowed";
        } else {
            printBtn.style.opacity = "1";
            printBtn.style.cursor = "pointer";
        }
    }
}

/**
 * Initialize form submit
 */
function initFormSubmit() {
    const form = document.getElementById("barcodeForm");
    const printBtn = document.getElementById("printSelectedBtn");

    if (form && printBtn) {
        form.addEventListener("submit", function (e) {
            const selected = document.querySelectorAll(
                ".sparepart-checkbox:checked",
            );

            if (selected.length === 0) {
                e.preventDefault();
                showToast(
                    "Pilih minimal satu sparepart untuk dicetak",
                    "error",
                );
                return false;
            }

            showToast(`Mencetak ${selected.length} barcode...`, "success");
        });
    }
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
        <i class="fas ${type === "success" ? "fa-check-circle" : type === "error" ? "fa-exclamation-circle" : "fa-info-circle"} me-2"></i>
        ${message}
    `;
    document.body.appendChild(toast);

    setTimeout(() => toast.classList.add("show"), 10);
    setTimeout(() => {
        toast.classList.remove("show");
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
