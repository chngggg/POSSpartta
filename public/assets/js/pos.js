// =====================================================
// SPARTTAPOS - POS (POINT OF SALE) JS
// =====================================================

let cart = [];
let currentCategory = "all";
let selectedPaymentMethod = "cash";
let selectedBank = "bca";
let paymentPollingInterval = null;

document.addEventListener("DOMContentLoaded", function () {
    console.log("POS JS Loaded");
    initPOS();
});

function initPOS() {
    loadCartFromStorage();
    updateCartDisplay();
    initEventListeners();
    attachProductCardHandlers();
}

function initEventListeners() {
    const barcodeInput = document.getElementById("barcodeInput");
    if (barcodeInput) {
        barcodeInput.addEventListener("keypress", function (e) {
            if (e.key === "Enter") {
                addProductByBarcode(this.value);
                this.value = "";
            }
        });
    }

    const searchInput = document.getElementById("searchProducts");
    if (searchInput) {
        searchInput.addEventListener("input", function () {
            filterProducts(this.value);
        });
    }
}

function attachProductCardHandlers() {
    const productCards = document.querySelectorAll(".product-card");
    console.log("Product cards found:", productCards.length);

    productCards.forEach((card) => {
        card.removeEventListener("click", handleProductClick);
        card.addEventListener("click", handleProductClick);
    });
}

function handleProductClick(e) {
    e.stopPropagation();

    const productData = {
        id: parseInt(this.dataset.id),
        code: this.dataset.code,
        name: this.dataset.name,
        selling_price: parseInt(this.dataset.price),
        stock: parseInt(this.dataset.stock),
    };

    console.log("Product clicked:", productData);
    addToCart(productData);
}

function addProductByBarcode(barcode) {
    if (!barcode) {
        showToast("Masukkan barcode terlebih dahulu", "warning");
        return;
    }

    console.log("Searching barcode:", barcode);

    fetch(`/pos/search-by-barcode?barcode=${barcode}`)
        .then((response) => response.json())
        .then((data) => {
            console.log("Barcode response:", data);
            if (data.success) {
                addToCart(data.data);
            } else {
                showToast(data.message || "Sparepart tidak ditemukan", "error");
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            showToast("Terjadi kesalahan", "error");
        });
}

function addToCart(product) {
    console.log("Adding to cart:", product);

    if (!product || !product.id) {
        showToast("Data produk tidak valid", "error");
        return;
    }

    const existingItem = cart.find((item) => item.id === product.id);

    if (existingItem) {
        if (existingItem.quantity < product.stock) {
            existingItem.quantity++;
            showToast(`${product.name} jumlah ditambah`, "success");
        } else {
            showToast(
                `Stok ${product.name} tidak mencukupi (tersisa ${product.stock})`,
                "error",
            );
            return;
        }
    } else {
        if (product.stock > 0) {
            cart.push({
                id: product.id,
                code: product.code,
                name: product.name,
                price: product.selling_price,
                quantity: 1,
                stock: product.stock,
            });
            showToast(`${product.name} ditambahkan ke keranjang`, "success");
        } else {
            showToast(`Stok ${product.name} habis`, "error");
            return;
        }
    }

    saveCartToStorage();
    updateCartDisplay();
}

function updateQuantity(id, delta) {
    const item = cart.find((item) => item.id === id);
    if (item) {
        const newQuantity = item.quantity + delta;
        if (newQuantity > 0 && newQuantity <= item.stock) {
            item.quantity = newQuantity;
            showToast(`${item.name} jumlah: ${newQuantity}`, "success");
        } else if (newQuantity > item.stock) {
            showToast(
                `Stok ${item.name} tidak mencukupi (maksimal ${item.stock})`,
                "error",
            );
            return;
        } else if (newQuantity <= 0) {
            removeFromCart(id);
            return;
        }
    }
    saveCartToStorage();
    updateCartDisplay();
}

function removeFromCart(id) {
    const item = cart.find((item) => item.id === id);
    if (item) {
        cart = cart.filter((item) => item.id !== id);
        saveCartToStorage();
        updateCartDisplay();
        showToast(`${item.name} dihapus dari keranjang`, "info");
    }
}

function updateCartDisplay() {
    const cartItemsContainer = document.getElementById("cartItems");
    const subtotalElement = document.getElementById("subtotal");
    const totalElement = document.getElementById("totalAmount");
    const itemCountElement = document.getElementById("itemCount");

    if (!cartItemsContainer) {
        console.log("Cart container not found");
        return;
    }

    if (cart.length === 0) {
        cartItemsContainer.innerHTML = `
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <p>Keranjang kosong</p>
                <small class="text-muted">Klik produk di sebelah kiri</small>
            </div>
        `;
        if (subtotalElement) subtotalElement.textContent = "Rp 0";
        if (totalElement) totalElement.textContent = "Rp 0";
        if (itemCountElement) itemCountElement.textContent = "0";
        return;
    }

    let html = "";
    let subtotal = 0;

    cart.forEach((item) => {
        const itemTotal = item.price * item.quantity;
        subtotal += itemTotal;

        html += `
            <div class="cart-item">
                <div class="cart-item-info">
                    <div class="cart-item-name">${escapeHtml(item.name)}</div>
                    <div class="cart-item-price">${formatRupiah(item.price)}</div>
                </div>
                <div class="cart-item-quantity">
                    <button class="quantity-btn" onclick="updateQuantity(${item.id}, -1)">-</button>
                    <span class="quantity-value">${item.quantity}</span>
                    <button class="quantity-btn" onclick="updateQuantity(${item.id}, 1)">+</button>
                </div>
                <div class="cart-item-subtotal">${formatRupiah(itemTotal)}</div>
                <button class="remove-item" onclick="removeFromCart(${item.id})">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        `;
    });

    cartItemsContainer.innerHTML = html;

    if (subtotalElement) subtotalElement.textContent = formatRupiah(subtotal);
    if (totalElement) totalElement.textContent = formatRupiah(subtotal);
    if (itemCountElement)
        itemCountElement.textContent = cart.reduce(
            (sum, item) => sum + item.quantity,
            0,
        );
}

function filterProducts(keyword) {
    const products = document.querySelectorAll(".product-card");
    const lowerKeyword = keyword.toLowerCase();

    products.forEach((product) => {
        const name = product.dataset.name?.toLowerCase() || "";
        const code = product.dataset.code?.toLowerCase() || "";

        if (name.includes(lowerKeyword) || code.includes(lowerKeyword)) {
            product.style.display = "block";
        } else {
            product.style.display = "none";
        }
    });
}

function filterByCategory(categoryId, element) {
    currentCategory = categoryId;

    document.querySelectorAll(".filter-chip").forEach((chip) => {
        chip.classList.remove("active");
    });
    element.classList.add("active");

    const products = document.querySelectorAll(".product-card");
    products.forEach((product) => {
        const productCategory = product.dataset.category;
        if (categoryId === "all" || productCategory === categoryId) {
            product.style.display = "block";
        } else {
            product.style.display = "none";
        }
    });
}

function clearCart() {
    if (cart.length === 0) return;

    if (confirm("Yakin ingin mengosongkan keranjang?")) {
        cart = [];
        saveCartToStorage();
        updateCartDisplay();
        showToast("Keranjang dikosongkan", "info");
    }
}

function processTransaction() {
    if (cart.length === 0) {
        showToast("Keranjang masih kosong", "error");
        return;
    }

    const total = cart.reduce(
        (sum, item) => sum + item.price * item.quantity,
        0,
    );

    selectedPaymentMethod = "cash";
    selectedBank = "bca";

    showPaymentModal(total);
}

function showPaymentModal(total) {
    let modal = document.getElementById("paymentModal");
    if (!modal) {
        createPaymentModal();
        modal = document.getElementById("paymentModal");
    }

    updatePaymentModal(total);

    const paymentModal = new bootstrap.Modal(modal);
    paymentModal.show();
}

function createPaymentModal() {
    const modalHTML = `
        <div class="modal fade payment-modal" id="paymentModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-credit-card me-2"></i> Metode Pembayaran
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="payment-methods">
                            <div class="payment-method-card cash active" onclick="selectPaymentMethod('cash')">
                                <i class="fas fa-money-bill-wave"></i>
                                <div class="method-name">Tunai</div>
                                <div class="method-desc">Pembayaran tunai</div>
                            </div>
                            <div class="payment-method-card qris" onclick="selectPaymentMethod('qris')">
                                <i class="fas fa-qrcode"></i>
                                <div class="method-name">QRIS</div>
                                <div class="method-desc">Scan QR Code</div>
                            </div>
                            <div class="payment-method-card transfer" onclick="selectPaymentMethod('transfer')">
                                <i class="fas fa-university"></i>
                                <div class="method-name">Transfer Bank</div>
                                <div class="method-desc">Virtual Account</div>
                            </div>
                        </div>
                        <div id="paymentDetailsContainer"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-gold" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-pay" onclick="confirmPayment()">
                            <i class="fas fa-check-circle me-2"></i> Konfirmasi Pembayaran
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML("beforeend", modalHTML);
}

function updatePaymentModal(total) {
    const container = document.getElementById("paymentDetailsContainer");
    if (!container) return;

    if (selectedPaymentMethod === "cash") {
        container.innerHTML = `
            <div class="payment-details">
                <div class="cash-section">
                    <div class="payment-summary">
                        <div class="summary-line">
                            <span>Total Belanja</span>
                            <span class="total-amount">${formatRupiah(total)}</span>
                        </div>
                    </div>
                    <div class="cash-input-group">
                        <label>Jumlah Pembayaran</label>
                        <input type="number" id="cashAmount" class="form-control" placeholder="Masukkan jumlah tunai" step="1000">
                    </div>
                    <div class="payment-summary" id="changeSummary" style="display: none;">
                        <div class="summary-line">
                            <span>Kembalian</span>
                            <span id="changeAmountCash" style="color: #2ecc71;">Rp 0</span>
                        </div>
                    </div>
                </div>
            </div>
        `;

        const cashInput = document.getElementById("cashAmount");
        if (cashInput) {
            cashInput.addEventListener("input", function () {
                const cash = parseFloat(this.value) || 0;
                const change = cash - total;
                const changeSummary = document.getElementById("changeSummary");
                const changeAmountSpan =
                    document.getElementById("changeAmountCash");

                if (cash >= total) {
                    changeSummary.style.display = "block";
                    changeAmountSpan.textContent = formatRupiah(change);
                    changeAmountSpan.style.color = "#2ecc71";
                } else if (cash > 0) {
                    changeSummary.style.display = "block";
                    changeAmountSpan.textContent = formatRupiah(
                        Math.abs(change),
                    );
                    changeAmountSpan.style.color = "#e74c3c";
                } else {
                    changeSummary.style.display = "none";
                }
            });
        }
    } else if (selectedPaymentMethod === "qris") {
        // Tampilkan loading state
        container.innerHTML = `
            <div class="payment-details">
                <div class="qris-section">
                    <div class="qris-placeholder" id="qrisPlaceholder">
                        <i class="fas fa-spinner fa-pulse"></i>
                        <span>Memuat QR Code...</span>
                    </div>
                    <div class="payment-summary">
                        <div class="summary-line">
                            <span>Total Pembayaran</span>
                            <span>${formatRupiah(total)}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Fetch QR Code dari server
        fetch(`/pos/generate-qris?amount=${total}`)
            .then((response) => {
                console.log("QR STATUS:", response.status);
                return response.json();
            })
            .then((data) => {
                console.log("QR DATA:", data);

                if (data.success) {
                    const qrImage = `data:image/svg+xml;base64,${data.qr_code}`;

                    container.innerHTML = `
<div class="payment-details">
    <div class="qris-section">

        <div style="text-align:center; margin-bottom:15px;">
            <h5 style="margin:0; color:#fff;">SPARTTA POS</h5>
            <small style="color:#aaa;">Semarang</small>
        </div>

        <div style="
            background:white;
            padding:20px;
            border-radius:16px;
            display:flex;
            justify-content:center;
            align-items:center;
            margin-bottom:15px;
        ">
            <img src="${qrImage}" style="width:220px; height:220px;">
        </div>

        <div style="
            background:#0a0a0a;
            border-radius:12px;
            padding:15px;
            text-align:center;
        ">
            <div style="font-size:0.9rem; color:#aaa;">Total Pembayaran</div>
            <div style="font-size:1.3rem; font-weight:bold; color:#fff;">
                ${formatRupiah(total)}
            </div>
        </div>

        <div style="
            margin-top:15px;
            text-align:center;
            font-size:0.8rem;
            color:#888;
        ">
            Scan menggunakan mobile banking / e-wallet
        </div>

        <div style="
            margin-top:10px;
            text-align:center;
            font-size:0.7rem;
            color:#666;
        ">
            ID: ${data.transaction_id}
        </div>

    </div>
</div>
`;

                    window.currentQRISTransaction = {
                        id: data.transaction_id,
                        amount: total,
                    };
                } else {
                    throw new Error(data.message || "QR gagal dibuat");
                }
            })
            .catch((error) => {
                console.error("QR ERROR:", error);

                container.innerHTML = `
            <div class="payment-details">
                <div class="qris-section">
                    <div class="qris-placeholder" style="background: rgba(231, 76, 60, 0.1);">
                        <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #e74c3c;"></i>
                        <span>Gagal memuat QR Code</span>
                    </div>

                    <button class="btn btn-gold mt-3" onclick="retryQRIS(${total})">
                        <i class="fas fa-sync-alt me-2"></i> Coba Lagi
                    </button>
                </div>
            </div>
        `;
            });
    } else if (selectedPaymentMethod === "transfer") {
        const vaNumber = generateVANumber(selectedBank);
        container.innerHTML = `
            <div class="payment-details">
                <div class="transfer-section">
                    <div class="bank-list">
                        <div class="bank-item ${selectedBank === "bca" ? "active" : ""}" onclick="selectBank('bca')">
                            <i class="fas fa-university"></i>
                            <div class="bank-name">BCA</div>
                        </div>
                        <div class="bank-item ${selectedBank === "mandiri" ? "active" : ""}" onclick="selectBank('mandiri')">
                            <i class="fas fa-university"></i>
                            <div class="bank-name">Mandiri</div>
                        </div>
                        <div class="bank-item ${selectedBank === "bri" ? "active" : ""}" onclick="selectBank('bri')">
                            <i class="fas fa-university"></i>
                            <div class="bank-name">BRI</div>
                        </div>
                        <div class="bank-item ${selectedBank === "bni" ? "active" : ""}" onclick="selectBank('bni')">
                            <i class="fas fa-university"></i>
                            <div class="bank-name">BNI</div>
                        </div>
                    </div>
                    <div class="payment-summary">
                        <div class="summary-line">
                            <span>Total Pembayaran</span>
                            <span>${formatRupiah(total)}</span>
                        </div>
                    </div>
                    <div class="va-info">
                        <div class="label">Virtual Account Number</div>
                        <div class="va-number">${vaNumber}</div>
                        <p class="text-muted small mt-2">Gunakan nomor Virtual Account di atas untuk melakukan pembayaran.</p>
                    </div>
                </div>
            </div>
        `;
    }
}

function retryQRIS(amount) {
    const container = document.getElementById("paymentDetailsContainer");
    if (!container) return;

    container.innerHTML = `
        <div class="payment-details">
            <div class="qris-section">
                <div class="qris-placeholder" id="qrisPlaceholder">
                    <i class="fas fa-spinner fa-pulse"></i>
                    <span>Memuat QR Code...</span>
                </div>
                <div class="payment-summary">
                    <div class="summary-line">
                        <span>Total Pembayaran</span>
                        <span>${formatRupiah(amount)}</span>
                    </div>
                </div>
            </div>
        </div>
    `;

    fetch(`/pos/generate-qris?amount=${amount}`)
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                const qrImage = `data:image/png;base64,${data.qr_code}`;
                container.innerHTML = `
                    <div class="payment-details">
                        <div class="qris-section">
                            <div class="qris-placeholder" style="background: white; padding: 20px; border-radius: 20px;">
                                <img src="${qrImage}" alt="QRIS Code" style="width: 200px; height: 200px;">
                            </div>
                            <div class="payment-summary">
                                <div class="summary-line">
                                    <span>Total Pembayaran</span>
                                    <span>${formatRupiah(amount)}</span>
                                </div>
                            </div>
                            <div class="qris-info" style="margin-top: 20px;">
                                <div style="background: #0a0a0a; border-radius: 12px; padding: 15px;">
                                    <p class="text-muted small text-center">
                                        Scan QR Code di atas menggunakan aplikasi mobile banking atau e-wallet Anda.
                                    </p>
                                    <div class="qris-code" style="margin-top: 15px; text-align: center; font-size: 0.7rem;">
                                        Kode: ${data.transaction_id}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
        });
}

function generateVANumber(bank) {
    const prefixes = {
        bca: "8888",
        mandiri: "8899",
        bri: "8890",
        bni: "8891",
    };
    const random = Math.floor(Math.random() * 10000000000)
        .toString()
        .padStart(10, "0");
    return prefixes[bank] + random;
}

function selectPaymentMethod(method) {
    selectedPaymentMethod = method;

    document.querySelectorAll(".payment-method-card").forEach((card) => {
        card.classList.remove("active");
    });
    document
        .querySelector(`.payment-method-card.${method}`)
        .classList.add("active");

    const total = cart.reduce(
        (sum, item) => sum + item.price * item.quantity,
        0,
    );
    updatePaymentModal(total);
}

function selectBank(bank) {
    selectedBank = bank;

    document.querySelectorAll(".bank-item").forEach((item) => {
        item.classList.remove("active");
    });

    const clickedBank = document.querySelector(
        `.bank-item[onclick*="${bank}"]`,
    );
    if (clickedBank) clickedBank.classList.add("active");

    const total = cart.reduce(
        (sum, item) => sum + item.price * item.quantity,
        0,
    );
    updatePaymentModal(total);
}

function confirmPayment() {
    const total = cart.reduce(
        (sum, item) => sum + item.price * item.quantity,
        0,
    );
    let paymentAmount = total;
    let change = 0;

    if (selectedPaymentMethod === "cash") {
        const cashInput = document.getElementById("cashAmount");
        const cash = parseFloat(cashInput?.value) || 0;

        if (cash < total) {
            showToast("Jumlah pembayaran kurang!", "error");
            return;
        }
        paymentAmount = cash;
        change = cash - total;

        processTransactionData(total, paymentAmount, change, "cash", null);
    } else if (selectedPaymentMethod === "qris") {
        showToast(
            "Scan QR Code menggunakan aplikasi mobile banking atau e-wallet Anda",
            "info",
        );
    } else if (selectedPaymentMethod === "transfer") {
        showToast(
            `Silakan transfer ke Virtual Account: ${generateVANumber(selectedBank)}`,
            "info",
        );
        processTransactionData(total, total, 0, "transfer", selectedBank);
    }
}

function processTransactionData(total, paymentAmount, change, method, bank) {
    const data = {
        items: cart.map((item) => ({
            id: item.id,
            quantity: item.quantity,
        })),
        total_amount: total,
        payment_amount: paymentAmount,
        change_amount: change,
        payment_method: method,
        bank: bank,
    };

    console.log("Transaction data:", data);

    fetch("/pos/transaction", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
        },
        body: JSON.stringify(data),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                const modal = bootstrap.Modal.getInstance(
                    document.getElementById("paymentModal"),
                );
                if (modal) modal.hide();

                showPaymentReceipt(
                    data.transaction_id,
                    total,
                    paymentAmount,
                    change,
                    method,
                );

                cart = [];
                saveCartToStorage();
                updateCartDisplay();
            } else {
                showToast(data.message, "error");
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            showToast("Terjadi kesalahan", "error");
        });
}

function showPaymentReceipt(transactionId, total, payment, change, method) {
    const methodNames = {
        cash: "Tunai",
        qris: "QRIS",
        transfer: "Transfer Bank",
    };

    // Format tanggal dan waktu
    const now = new Date();
    const formattedDate = now.toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "long",
        year: "numeric",
    });
    const formattedTime = now.toLocaleTimeString("id-ID", {
        hour: "2-digit",
        minute: "2-digit",
    });

    // Nomor faktur
    const invoiceNumber =
        "INV/" +
        now.getFullYear() +
        "/" +
        String(now.getMonth() + 1).padStart(2, "0") +
        "/" +
        String(now.getDate()).padStart(2, "0") +
        "/" +
        String(Math.floor(Math.random() * 10000)).padStart(4, "0");

    // Format items untuk faktur
    const itemsHtml = cart
        .map(
            (item, index) => `
        <tr style="border-bottom: 1px solid #eee;">
            <td style="padding: 10px 5px; text-align: center;">${index + 1}</td>
            <td style="padding: 10px 5px;">
                <div style="font-weight: 600;">${escapeHtml(item.name)}</div>
                <div style="font-size: 10px; color: #888;">${item.code}</div>
            </td>
            <td style="padding: 10px 5px; text-align: center;">${item.quantity}</td>
            <td style="padding: 10px 5px; text-align: right;">${formatRupiah(item.price)}</td>
            <td style="padding: 10px 5px; text-align: right; font-weight: 600; color: #2ecc71;">${formatRupiah(item.price * item.quantity)}</td>
        </tr>
    `,
        )
        .join("");

    // Terbilang untuk total
    const terbilang = numberToWords(total);

    const receiptHtml = `
        <div id="printArea" style="
            background: white;
            color: #1a1a1a;
            padding: 30px;
            font-family: 'Courier New', 'Times New Roman', monospace;
            max-width: 800px;
            margin: 0 auto;
            border-radius: 0;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        ">
            <!-- Kop Faktur -->
            <div style="text-align: center; margin-bottom: 25px; border-bottom: 2px solid #1a1a1a; padding-bottom: 15px;">
                <div style="font-size: 24px; font-weight: 800; letter-spacing: 3px; color: #d4af37;">SPARTTA POS</div>
                <div style="font-size: 11px; color: #555; margin-top: 5px;">Jl. Merdeka No. 123, Jakarta Selatan 12120</div>
                <div style="font-size: 10px; color: #777;">Telp: (021) 1234-5678 | Email: info@sparttapos.com</div>
                <div style="font-size: 10px; color: #777;">www.sparttapos.com</div>
            </div>
            
            <!-- Judul FAKTUR -->
            <div style="text-align: center; margin: 20px 0;">
                <div style="font-size: 18px; font-weight: 800; letter-spacing: 3px;">FAKTUR PENJUALAN</div>
                <div style="font-size: 11px; color: #888;">INVOICE / RECEIPT</div>
            </div>
            
            <!-- Info Faktur 2 Kolom -->
            <table style="width: 100%; font-size: 11px; margin-bottom: 20px; border-collapse: collapse;">
                <tr>
                    <td style="width: 50%; padding: 4px 0;">
                        <strong>No. Faktur</strong><br>
                        ${invoiceNumber}
                    </td>
                    <td style="width: 50%; padding: 4px 0;">
                        <strong>Tanggal</strong><br>
                        ${formattedDate}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 4px 0;">
                        <strong>ID Transaksi</strong><br>
                        ${transactionId}
                    </td>
                    <td style="padding: 4px 0;">
                        <strong>Waktu</strong><br>
                        ${formattedTime}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 4px 0;">
                        <strong>Kasir</strong><br>
                        ${localStorage.getItem("user_name") || "Admin"}
                    </td>
                    <td style="padding: 4px 0;">
                        <strong>Metode Bayar</strong><br>
                        ${methodNames[method]}
                    </td>
                </tr>
            </table>
            
            <!-- Garis Pemisah -->
            <div style="border-top: 1px solid #ddd; margin: 10px 0;"></div>
            
            <!-- Header Tabel Barang -->
            <table style="width: 100%; font-size: 11px; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr style="background: #f5f5f5; border-bottom: 2px solid #ddd;">
                        <th style="padding: 8px 5px; text-align: center;">No</th>
                        <th style="padding: 8px 5px; text-align: left;">Nama Barang</th>
                        <th style="padding: 8px 5px; text-align: center;">Qty</th>
                        <th style="padding: 8px 5px; text-align: right;">Harga</th>
                        <th style="padding: 8px 5px; text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    ${itemsHtml}
                </tbody>
            </table>
            
            <!-- Garis Pemisah -->
            <div style="border-top: 1px solid #ddd; margin: 15px 0;"></div>
            
            <!-- Ringkasan Pembayaran -->
            <table style="width: 100%; font-size: 12px; margin: 10px 0;">
                <tr>
                    <td style="width: 70%; text-align: right; padding: 5px;">Subtotal:</td>
                    <td style="width: 30%; text-align: right; padding: 5px; font-weight: 600;">${formatRupiah(total)}</td>
                </tr>
                ${
                    method === "cash" && payment > total
                        ? `
                <tr>
                    <td style="text-align: right; padding: 5px;">Tunai:</td>
                    <td style="text-align: right; padding: 5px;">${formatRupiah(payment)}</td>
                </tr>
                <tr>
                    <td style="text-align: right; padding: 5px;">Kembalian:</td>
                    <td style="text-align: right; padding: 5px; color: #2ecc71; font-weight: 700;">${formatRupiah(change)}</td>
                </tr>
                `
                        : ""
                }
                <tr style="border-top: 2px solid #d4af37;">
                    <td style="text-align: right; padding: 10px 5px 5px;"><strong>TOTAL</strong></td>
                    <td style="text-align: right; padding: 10px 5px 5px;"><strong style="font-size: 16px; color: #d4af37;">${formatRupiah(total)}</strong></td>
                </tr>
            </table>
            
            <!-- Terbilang -->
            <div style="background: #f9f9f9; padding: 10px; margin: 15px 0; border-left: 3px solid #d4af37;">
                <div style="font-size: 10px; color: #666;">Terbilang:</div>
                <div style="font-size: 11px; font-weight: 600;">${terbilang} Rupiah</div>
            </div>
            
            <!-- Footer -->
            <div style="text-align: center; margin-top: 30px; padding-top: 15px; border-top: 1px solid #ddd;">
                <div style="font-size: 11px; font-weight: 500;">✨ Terima kasih atas kunjungan Anda! ✨</div>
                <div style="font-size: 10px; color: #888; margin-top: 8px;">Barang yang sudah dibeli tidak dapat ditukar atau dikembalikan</div>
                <div style="font-size: 9px; color: #aaa; margin-top: 10px;">This is a computer generated document, no signature required.</div>
            </div>
        </div>
    `;

    const receiptModal = document.createElement("div");
    receiptModal.className = "modal fade";
    receiptModal.id = "receiptModal";
    receiptModal.setAttribute("tabindex", "-1");
    receiptModal.innerHTML = `
        <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width: 900px;">
            <div class="modal-content custom-modal" style="background: #1a1a1a; border-radius: 16px; overflow: hidden;">
                <div class="modal-header" style="border-bottom: 1px solid #333; padding: 16px 24px;">
                    <h5 class="modal-title" style="color: #d4af37;">
                        <i class="fas fa-file-invoice me-2"></i> Faktur Penjualan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding: 0; background: #f5f5f5; max-height: 80vh; overflow-y: auto;">
                    ${receiptHtml}
                </div>
                <div class="modal-footer" style="border-top: 1px solid #333; justify-content: center; gap: 12px; padding: 16px;">
                    <button class="btn btn-gold" onclick="printReceipt()">
                        <i class="fas fa-print me-2"></i> Cetak Faktur
                    </button>
                    <button class="btn btn-outline-gold" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(receiptModal);
    const modal = new bootstrap.Modal(receiptModal);
    modal.show();

    receiptModal.addEventListener("hidden.bs.modal", function () {
        receiptModal.remove();
    });
}

// Fungsi untuk mengkonversi angka ke terbilang
function numberToWords(amount) {
    const angka = Math.floor(amount);
    const bilangan = [
        "",
        "Satu",
        "Dua",
        "Tiga",
        "Empat",
        "Lima",
        "Enam",
        "Tujuh",
        "Delapan",
        "Sembilan",
        "Sepuluh",
        "Sebelas",
    ];

    if (angka < 12) return bilangan[angka];
    if (angka < 20) return bilangan[angka - 10] + " Belas";
    if (angka < 100) {
        const puluhan = Math.floor(angka / 10);
        const satuan = angka % 10;
        return (
            bilangan[puluhan] +
            " Puluh" +
            (satuan > 0 ? " " + bilangan[satuan] : "")
        );
    }
    if (angka < 200)
        return (
            "Seratus" + (angka > 100 ? " " + numberToWords(angka - 100) : "")
        );
    if (angka < 1000) {
        const ratusan = Math.floor(angka / 100);
        const sisa = angka % 100;
        return (
            bilangan[ratusan] +
            " Ratus" +
            (sisa > 0 ? " " + numberToWords(sisa) : "")
        );
    }
    if (angka < 2000)
        return (
            "Seribu" + (angka > 1000 ? " " + numberToWords(angka - 1000) : "")
        );
    if (angka < 1000000) {
        const ribuan = Math.floor(angka / 1000);
        const sisa = angka % 1000;
        return (
            numberToWords(ribuan) +
            " Ribu" +
            (sisa > 0 ? " " + numberToWords(sisa) : "")
        );
    }
    if (angka < 1000000000) {
        const jutaan = Math.floor(angka / 1000000);
        const sisa = angka % 1000000;
        return (
            numberToWords(jutaan) +
            " Juta" +
            (sisa > 0 ? " " + numberToWords(sisa) : "")
        );
    }
    return "Angka terlalu besar";
}

// Fungsi untuk print struk
function printReceipt() {
    const printContent = document.getElementById("printArea").cloneNode(true);
    const printWindow = window.open("", "_blank");

    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Faktur Penjualan - SPARTTA POS</title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                body {
                    font-family: 'Courier New', 'Times New Roman', monospace;
                    background: white;
                    padding: 20px;
                }
                @media print {
                    body {
                        padding: 0;
                        margin: 0;
                    }
                    .no-print {
                        display: none;
                    }
                }
                .no-print {
                    text-align: center;
                    margin-top: 20px;
                }
                .no-print button {
                    padding: 10px 20px;
                    margin: 5px;
                    cursor: pointer;
                    border: none;
                    border-radius: 5px;
                    font-size: 14px;
                }
                .no-print button:first-child {
                    background: #d4af37;
                    color: #000;
                }
                .no-print button:last-child {
                    background: #333;
                    color: #fff;
                }
            </style>
        </head>
        <body>
            ${printContent.outerHTML}
            <div class="no-print">
                <button onclick="window.print()">🖨️ Cetak Faktur</button>
                <button onclick="window.close()">❌ Tutup</button>
            </div>
        </body>
        </html>
    `);

    printWindow.document.close();
}

function saveCartToStorage() {
    localStorage.setItem("pos_cart", JSON.stringify(cart));
}

function loadCartFromStorage() {
    const savedCart = localStorage.getItem("pos_cart");
    if (savedCart) {
        cart = JSON.parse(savedCart);
        console.log("Cart loaded from storage:", cart);
    }
}

function formatRupiah(amount) {
    return "Rp " + amount.toLocaleString("id-ID");
}

function escapeHtml(text) {
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
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

// Make functions global
window.addToCart = addToCart;
window.updateQuantity = updateQuantity;
window.removeFromCart = removeFromCart;
window.filterByCategory = filterByCategory;
window.clearCart = clearCart;
window.processTransaction = processTransaction;
window.addProductByBarcode = addProductByBarcode;
window.selectPaymentMethod = selectPaymentMethod;
window.selectBank = selectBank;
window.confirmPayment = confirmPayment;
window.retryQRIS = retryQRIS;
window.printReceipt = printReceipt;
window.numberToWords = numberToWords;
