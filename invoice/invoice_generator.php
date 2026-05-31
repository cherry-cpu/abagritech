<?php
// Invoice Generator - Aakasha Bindu Agritech
// All calculations are done in real-time via JavaScript
require_once 'check_auth.php';
requireLogin();

$invoice_id = 'INV_' . strtoupper(uniqid());
$invoice_date = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Generator - Aakasha Bindu Agritech</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #dbeafe;
            --success: #16a34a;
            --success-light: #dcfce7;
            --danger: #dc2626;
            --danger-light: #fee2e2;
            --warning: #f59e0b;
            --warning-light: #fef3c7;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --radius: 10px;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
            --shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -4px rgba(0,0,0,0.1);
            --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #f0f4ff 0%, #e8effe 50%, #f0f4ff 100%);
            color: var(--gray-800);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        /* Header */
        .invoice-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            padding: 28px 32px;
            border-radius: var(--radius) var(--radius) 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .invoice-header h1 {
            font-size: 1.6rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .invoice-header h1 i { font-size: 1.4rem; }

        .header-meta {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .header-meta-item {
            background: rgba(255,255,255,0.15);
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.85rem;
            backdrop-filter: blur(4px);
        }

        .header-meta-item span { font-weight: 600; }

        /* Card */
        .card {
            background: #fff;
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .card-body { padding: 28px 32px; }

        /* Section Title */
        .section-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--gray-100);
        }

        .section-title i { color: var(--primary); font-size: 1.1rem; }

        /* Customer form */
        .customer-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .customer-grid .full-width { grid-column: 1 / -1; }

        .form-field label {
            display: block;
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--gray-500);
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .form-field input,
        .form-field select {
            width: 100%;
            padding: 10px 12px;
            border: 1.5px solid var(--gray-200);
            border-radius: 8px;
            font-size: 0.9rem;
            font-family: inherit;
            color: var(--gray-800);
            background: var(--gray-50);
            transition: all 0.2s ease;
        }

        .form-field input:focus,
        .form-field select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
            background: #fff;
        }

        /* Products Table */
        .products-section { margin-top: 4px; }

        .table-wrapper {
            overflow-x: auto;
            border-radius: 8px;
            border: 1.5px solid var(--gray-200);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 700px;
        }

        thead th {
            background: var(--gray-800);
            color: #fff;
            padding: 12px 14px;
            font-size: 0.78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            text-align: left;
            white-space: nowrap;
        }

        thead th:first-child { border-radius: 6px 0 0 0; }
        thead th:last-child { border-radius: 0 6px 0 0; }

        tbody td {
            padding: 10px 14px;
            border-bottom: 1px solid var(--gray-100);
            vertical-align: middle;
        }

        tbody tr { transition: background 0.15s ease; }
        tbody tr:hover { background: var(--gray-50); }
        tbody tr:last-child td { border-bottom: none; }

        table select,
        table input[type="number"] {
            width: 100%;
            padding: 8px 10px;
            border: 1.5px solid var(--gray-200);
            border-radius: 6px;
            font-size: 0.88rem;
            font-family: inherit;
            background: var(--gray-50);
            color: var(--gray-800);
            transition: all 0.2s ease;
        }

        table select:focus,
        table input[type="number"]:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
            background: #fff;
        }

        table input[readonly] {
            background: var(--primary-light);
            border-color: transparent;
            color: var(--primary-dark);
            font-weight: 600;
            cursor: default;
        }

        .price-cell { font-weight: 600; color: var(--gray-700); }
        .subtotal-cell { font-weight: 700; color: var(--primary-dark); }

        .sno-cell { width: 50px; text-align: center; font-weight: 600; color: var(--gray-400); }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 0.88rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success), #15803d);
            color: #fff;
            box-shadow: 0 2px 8px rgba(22,163,74,0.3);
        }

        .btn-success:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(22,163,74,0.4);
        }

        .btn-remove {
            background: var(--danger-light);
            color: var(--danger);
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 0.82rem;
        }

        .btn-remove:hover { background: var(--danger); color: #fff; }

        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 16px;
            flex-wrap: wrap;
        }

        /* Tax / Discount row */
        .calc-section {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px;
            margin-top: 20px;
        }

        .calc-field label {
            display: block;
            font-size: 0.78rem;
            font-weight: 500;
            color: var(--gray-500);
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .calc-field input {
            width: 100%;
            padding: 10px 12px;
            border: 1.5px solid var(--gray-200);
            border-radius: 8px;
            font-size: 0.9rem;
            font-family: inherit;
            background: var(--gray-50);
            transition: all 0.2s ease;
        }

        .calc-field input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
            background: #fff;
        }

        /* Summary Card */
        .summary-card {
            background: linear-gradient(135deg, var(--gray-800) 0%, var(--gray-900) 100%);
            color: #fff;
            border-radius: var(--radius);
            padding: 28px 32px;
            margin-top: 24px;
            box-shadow: var(--shadow-xl);
        }

        .summary-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
            opacity: 0.9;
        }

        .summary-title i { color: var(--warning); }

        .summary-rows { display: flex; flex-direction: column; gap: 12px; }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
        }

        .summary-row.border-top {
            border-top: 1px solid rgba(255,255,255,0.15);
            padding-top: 16px;
            margin-top: 4px;
        }

        .summary-label {
            font-size: 0.9rem;
            opacity: 0.8;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .summary-value { font-size: 0.95rem; font-weight: 600; }

        .summary-row.total {
            border-top: 2px solid rgba(255,255,255,0.25);
            padding-top: 18px;
            margin-top: 8px;
        }

        .summary-row.total .summary-label {
            font-size: 1.15rem;
            font-weight: 700;
            opacity: 1;
        }

        .summary-row.total .summary-value {
            font-size: 1.4rem;
            font-weight: 800;
            color: #4ade80;
        }

        .tax-badge {
            display: inline-block;
            background: rgba(255,255,255,0.1);
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 500;
            margin-left: 4px;
        }

        .discount-badge {
            background: rgba(245, 158, 11, 0.2);
            color: var(--warning);
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--gray-400);
        }

        .empty-state i { font-size: 2.5rem; margin-bottom: 12px; display: block; }
        .empty-state p { font-size: 0.9rem; }

        /* Validation error */
        .input-error { border-color: var(--danger) !important; background: var(--danger-light) !important; }

        /* Responsive */
        @media (max-width: 768px) {
            body { padding: 10px; }
            .card-body { padding: 18px 16px; }
            .invoice-header { padding: 20px 16px; flex-direction: column; align-items: flex-start; }
            .customer-grid { grid-template-columns: 1fr; }
            .calc-section { grid-template-columns: 1fr; }
            .summary-card { padding: 20px 16px; }
        }

        /* Print styles */
        @media print {
            body { background: #fff; padding: 0; }
            .btn, .btn-group, .no-print { display: none !important; }
            .card { box-shadow: none; border: 1px solid var(--gray-200); }
            .summary-card { box-shadow: none; }
        }

        /* Animations */
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        tbody tr { animation: slideIn 0.25s ease forwards; }
    </style>
</head>
<body>
<form id="invoiceForm" method="POST" action="save_invoice.php">
<div class="container">
    <!-- Invoice Card -->
    <div class="card">
        <!-- Header -->
        <div class="invoice-header">
            <h1><i class="fas fa-file-invoice"></i> Invoice Generator</h1>
            <div class="header-meta">
                <div class="header-meta-item">Invoice: <span><?php echo $invoice_id; ?></span></div>
                <div class="header-meta-item">Date: <span><?php echo $invoice_date; ?></span></div>
            </div>
        </div>

        <div class="card-body">
            <!-- Customer Details -->
            <div class="section-title"><i class="fas fa-user"></i> Customer Details</div>
            <div class="customer-grid">
                <div class="form-field">
                    <label>Customer Name *</label>
                    <input type="text" id="customer_name" placeholder="Enter customer name" required>
                </div>
                <div class="form-field">
                    <label>Phone</label>
                    <input type="tel" id="phone" placeholder="Enter phone number">
                </div>
                <div class="form-field">
                    <label>Email</label>
                    <input type="email" id="email" placeholder="Enter email address">
                </div>
                <div class="form-field">
                    <label>City</label>
                    <input type="text" id="city" placeholder="Enter city">
                </div>
                <div class="form-field full-width">
                    <label>Address</label>
                    <input type="text" id="address" placeholder="Enter full address">
                </div>
            </div>
             <div class="form-field full-width">
                <label>Gst No</label>
                <input type="text" id="gstNo" placeholder="Gst no">
            </div>

            <!-- Products Section -->
            <div class="products-section">
                <div class="section-title" style="margin-top: 28px;">
                    <i class="fas fa-boxes-stacked"></i> Products
                </div>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th style="width:50px">S.No</th>
                                <th style="min-width:200px">Product</th>
                                <th style="width:100px">Qty</th>
                                <th style="width:130px">Price (₹)</th>
                                <th style="width:140px">Subtotal (₹)</th>
                                <th style="width:60px"></th>
                            </tr>
                        </thead>
                        <tbody id="productRows">
                            <!-- Rows added by JS -->
                        </tbody>
                    </table>
                    <div class="empty-state" id="emptyState">
                        <i class="fas fa-cart-plus"></i>
                        <p>No products added yet. Click <strong>"Add Product"</strong> to begin.</p>
                    </div>
                </div>

                <div class="btn-group no-print">
                    <button type="button" class="btn btn-primary" onclick="addProductRow()">
                        <i class="fas fa-plus"></i> Add Product
                    </button>
                </div>
            </div>

            <!-- Tax & Discount -->
            <div class="section-title" style="margin-top: 28px;">
                <i class="fas fa-calculator"></i> Tax & Discount
            </div>
            <div class="calc-section">
                <div class="calc-field">
                    <label>SGST (%)</label>
                    <input type="number" id="sgst" value="0" min="0" max="100" step="0.01" oninput="validatePercent(this); recalculate();">
                </div>
                <div class="calc-field">
                    <label>CGST (%)</label>
                    <input type="number" id="cgst" value="0" min="0" max="100" step="0.01" oninput="validatePercent(this); recalculate();">
                </div>
                <div class="calc-field">
                    <label>Discount (%)</label>
                    <input type="number" id="discount" value="0" min="0" max="100" step="0.01" oninput="validatePercent(this); recalculate();">
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="summary-card">
        <div class="summary-title"><i class="fas fa-receipt"></i> Invoice Summary</div>
        <div class="summary-rows">
            <div class="summary-row">
                <span class="summary-label"><i class="fas fa-layer-group"></i> Subtotal</span>
                <span class="summary-value" id="summarySubtotal">₹0.00</span>
            </div>
            <div class="summary-row border-top">
                <span class="summary-label"><i class="fas fa-landmark"></i> SGST <span class="tax-badge" id="sgstBadge">0%</span></span>
                <span class="summary-value" id="summarySgst">₹0.00</span>
            </div>
            <div class="summary-row">
                <span class="summary-label"><i class="fas fa-landmark"></i> CGST <span class="tax-badge" id="cgstBadge">0%</span></span>
                <span class="summary-value" id="summaryCgst">₹0.00</span>
            </div>
            <div class="summary-row">
                <span class="summary-label"><i class="fas fa-tags"></i> Discount <span class="tax-badge discount-badge" id="discBadge">0%</span></span>
                <span class="summary-value" id="summaryDiscount" style="color:#f87171;">-₹0.00</span>
            </div>
            <div class="summary-row total">
                <span class="summary-label"><i class="fas fa-wallet"></i> Grand Total</span>
                <span class="summary-value" id="summaryTotal">₹0.00</span>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="btn-group no-print" style="margin-top: 20px; justify-content: flex-end;">
        <button type="button" class="btn btn-success" onclick="saveInvoice();">
            <i class="fas fa-save"></i> Save Invoice
        </button>
    </div>
</div>

<script>
// ═══════════════════════════════════════════════════════════════
// Product Catalog — add/edit products and prices here
// ═══════════════════════════════════════════════════════════════
const PRODUCTS = [
    { name: 'Groww Booster(20L)',       code: 'GB-20000',   price: 2899 },

    { name: 'Groww Power (20L)',     code: 'GP-20000',   price: 3099 },
    
    { name: 'STAR BINDU (1L)',        code: 'SB-1000',  price: 3499 },
    { name: 'STAR BINDU (500ml)',     code: 'SB-500',   price: 1799 },
    { name: 'STAR BINDU (250ml)',     code: 'SB-250',   price: 999 },
    
    { name: 'M-KILLER (1L)',          code: 'MK-1000',  price: 3499 },
    { name: 'M-KILLER (500ml)',          code: 'MK-500',  price: 1799 },
    { name: 'M-KILLER (250ml)',          code: 'MK-250',  price: 999 },

    { name: 'Growth King (1L)',       code: 'GK-1000',   price: 699 },
    { name: 'Growth King (500ml)',       code: 'GK-500',   price: 399 },

    { name: 'Nutri Power (1L)',     code: 'GP-1000',   price: 699 },
    { name: 'Nutri Power (500ml)',     code: 'GP-500',   price: 399 },

    { name: 'Humi Black Gold (1Kg)',     code: 'HBG-1000',   price: 699 },
    { name: 'Humi Black Gold (500gms)',     code: 'HBG-500',   price: 399 },

];

let rowCounter = 0;

// ═══════════════════════════════════════════════════════════════
// Add a new product row
// ═══════════════════════════════════════════════════════════════
function addProductRow() {
    rowCounter++;
    const tbody = document.getElementById('productRows');
    const tr = document.createElement('tr');
    tr.id = 'row-' + rowCounter;

    // Build product options
    let options = '<option value="">-- Select Product --</option>';
    PRODUCTS.forEach((p, i) => {
        options += `<option value="${i}">${p.name} (${p.code})</option>`;
    });

    tr.innerHTML = `
        <td class="sno-cell">${tbody.rows.length + 1}</td>
        <td>
            <select onchange="onProductChange(this, ${rowCounter})">${options}</select>
        </td>
        <td>
            <input type="number" id="qty-${rowCounter}" value="1" min="1" step="1"
                   oninput="validateQty(this); recalculate();">
        </td>
        <td>
            <input type="number" id="price-${rowCounter}" value="0" readonly class="price-cell">
        </td>
        <td>
            <input type="number" id="subtotal-${rowCounter}" value="0.00" readonly class="subtotal-cell">
        </td>
        <td style="text-align:center">
            <button type="button" class="btn btn-remove" onclick="removeRow('row-${rowCounter}')">
                <i class="fas fa-trash-alt"></i>
            </button>
        </td>
    `;

    tbody.appendChild(tr);
    toggleEmptyState();
    recalculate();
}

// ═══════════════════════════════════════════════════════════════
// Remove a product row
// ═══════════════════════════════════════════════════════════════
function removeRow(rowId) {
    const row = document.getElementById(rowId);
    if (row) row.remove();
    renumberRows();
    toggleEmptyState();
    recalculate();
}

// ═══════════════════════════════════════════════════════════════
// Re-number S.No column after row removal
// ═══════════════════════════════════════════════════════════════
function renumberRows() {
    const rows = document.querySelectorAll('#productRows tr');
    rows.forEach((tr, i) => {
        tr.querySelector('.sno-cell').textContent = i + 1;
    });
}

// ═══════════════════════════════════════════════════════════════
// When product dropdown changes
// ═══════════════════════════════════════════════════════════════
function onProductChange(select, rowId) {
    const priceInput = document.getElementById('price-' + rowId);
    const idx = select.value;

    if (idx === '') {
        priceInput.value = 0;
    } else {
        priceInput.value = PRODUCTS[parseInt(idx)].price;
    }
    recalculate();
}

// ═══════════════════════════════════════════════════════════════
// Validate: quantity must be >= 1
// ═══════════════════════════════════════════════════════════════
function validateQty(input) {
    let v = parseInt(input.value);
    if (isNaN(v) || v < 1) {
        input.classList.add('input-error');
        input.value = 1;
    } else {
        input.classList.remove('input-error');
    }
}

// ═══════════════════════════════════════════════════════════════
// Validate: percent fields 0–100
// ═══════════════════════════════════════════════════════════════
function validatePercent(input) {
    let v = parseFloat(input.value);
    if (isNaN(v) || v < 0) {
        input.value = 0;
        input.classList.add('input-error');
    } else if (v > 100) {
        input.value = 100;
        input.classList.add('input-error');
    } else {
        input.classList.remove('input-error');
    }
}

// ═══════════════════════════════════════════════════════════════
// Toggle empty-state message
// ═══════════════════════════════════════════════════════════════
function toggleEmptyState() {
    const rows = document.querySelectorAll('#productRows tr');
    document.getElementById('emptyState').style.display = rows.length === 0 ? 'block' : 'none';
}

// ═══════════════════════════════════════════════════════════════
// Recalculate everything
//   Discount is applied on subtotal BEFORE tax.
//   Final = (Subtotal - Discount) + SGST + CGST
// ═══════════════════════════════════════════════════════════════
function recalculate() {
    const rows = document.querySelectorAll('#productRows tr');
    let itemsTotal = 0;

    rows.forEach(tr => {
        const id = tr.id.replace('row-', '');
        const qty = parseInt(document.getElementById('qty-' + id).value) || 0;
        const price = parseFloat(document.getElementById('price-' + id).value) || 0;
        const sub = qty * price;
        document.getElementById('subtotal-' + id).value = sub.toFixed(2);
        itemsTotal += sub;
    });

    const sgstPct = parseFloat(document.getElementById('sgst').value) || 0;
    const cgstPct = parseFloat(document.getElementById('cgst').value) || 0;
    const discPct = parseFloat(document.getElementById('discount').value) || 0;

    const discountAmt = itemsTotal * (discPct / 100);
    const afterDiscount = itemsTotal - discountAmt;
    const sgstAmt = afterDiscount * (sgstPct / 100);
    const cgstAmt = afterDiscount * (cgstPct / 100);
    const grandTotal = afterDiscount + sgstAmt + cgstAmt;

    // Update summary
    document.getElementById('summarySubtotal').textContent =itemsTotal.toFixed(2);
    document.getElementById('summarySgst').textContent     =sgstAmt.toFixed(2);
    document.getElementById('summaryCgst').textContent     = cgstAmt.toFixed(2);
    document.getElementById('summaryDiscount').textContent  = discountAmt.toFixed(2);
    document.getElementById('summaryTotal').textContent     = grandTotal.toFixed(2);

    // Update badges
    document.getElementById('sgstBadge').textContent = sgstPct + '%';
    document.getElementById('cgstBadge').textContent = cgstPct + '%';
    document.getElementById('discBadge').textContent = discPct + '%';
}

// ═══════════════════════════════════════════════════════════════
// Save Invoice (collect data and POST to save_invoice.php)
// ═══════════════════════════════════════════════════════════════
function saveInvoice() {
    const rows = document.querySelectorAll('#productRows tr');

    if (rows.length === 0) {
        alert('Please add at least one product.');
        return;
    }

    const name = document.getElementById('customer_name').value.trim();
    if (!name) {
        alert('Please enter the customer name.');
        return;
    }

    let products = [];

    rows.forEach(tr => {
        const id = tr.id.replace('row-', '');
        const sel = tr.querySelector('select');

        if (sel.value === '') return;

        const product = PRODUCTS[sel.value];

        products.push({
            name: product.name,
            code: product.code,
            qty: document.getElementById('qty-' + id).value,
            price: document.getElementById('price-' + id).value,
            subtotal: document.getElementById('subtotal-' + id).value
        });
    });

    const data = {
        invoice_id: "<?php echo $invoice_id; ?>",
        date: "<?php echo $invoice_date; ?>",
        customer: {
            name: name,
            phone: document.getElementById('phone').value,
            email: document.getElementById('email').value,
            city: document.getElementById('city').value,
            address: document.getElementById('address').value,
            gstNo: document.getElementById('gstNo').value

        },
        products: products,
        summary: {
            
            subtotal: document.getElementById('summarySubtotal').textContent,
            sgst: document.getElementById('summarySgst').textContent,
            cgst: document.getElementById('summaryCgst').textContent,
            discount: document.getElementById('summaryDiscount').textContent,
            total: document.getElementById('summaryTotal').textContent
        },
        tax: {
            sgst_pct: document.getElementById('sgst').value,
            cgst_pct: document.getElementById('cgst').value,
            discount_pct: document.getElementById('discount').value
        }
    };

    let input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'invoice_data';
    input.value = JSON.stringify(data);

    document.getElementById('invoiceForm').appendChild(input);
    document.getElementById('invoiceForm').submit();
}
// ═══════════════════════════════════════════════════════════════
// Initialize with one empty row
// ═══════════════════════════════════════════════════════════════
window.addEventListener('DOMContentLoaded', () => {
    addProductRow();
});
</script>
</form>
</body>
</html>