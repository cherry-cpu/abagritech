<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice Generator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 900px;
            margin: 20px auto;
            background: #fff;
            padding: 25px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        h3 {
            margin-top: 25px;
            color: #555;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .form-group {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 10px;
            gap: 15px;
        }

        .form-group label {
            flex: 0 0 100px;
            font-weight: bold;
            align-self: center;
        }

        .form-group input {
            flex: 1 1 200px;
            padding: 5px 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .form-group-row {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn {
            background-color: #007BFF;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            transition: 0.3s;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .table-container {
            overflow-x: auto;
            margin-top: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 650px;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: center;
        }

        table th {
            background-color: #f2f2f2;
        }

        .remove-btn {
            background-color: #dc3545;
            color: #fff;
            border: none;
            padding: 4px 8px;
            border-radius: 4px;
            cursor: pointer;
        }

        .remove-btn:hover {
            background-color: #c82333;
        }

        /* Responsive tweaks */
        @media (max-width: 768px) {
            .form-group {
                flex-direction: column;
            }

            .form-group label {
                flex: 0 0 auto;
            }

            input {
                width: 100%;
            }

            table {
                min-width: 100%;
            }
        }
    </style>
    <script>
        function addRow() {
            let table = document.getElementById("items");
            let row = table.insertRow();
            row.innerHTML = `
                <td><input type="text" name="product_name[]" required></td>
                <td><input type="number" name="packing[]" required></td>
                <td><input type="number" name="billing_price[]" step="0.01" required></td>
                <td><input type="text" name="product_code[]" required></td>
                <td><button type="button" class="remove-btn" onclick="this.parentElement.parentElement.remove()">X</button></td>
            `;
        }

        window.onload = function() {
            addRow(); // Add initial product row
        }
    </script>
</head>
<body>
<div class="container">
    <h2>Professional Invoice Generator</h2>

    <form method="POST" action="save_invoice.php">
        <input type="hidden" name="invoice_id" value="<?php echo uniqid('INV_'); ?>">

        <h3>Customer (To) Details</h3>
        <div class="form-group">
            <label>Name:</label>
            <input type="text" name="customer_name"  required value="asd">
            <label>Phone:</label>
            <input type="text" name="phone" value="asd">
        </div>

        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" value="asd@asd.asd">
        </div>

        <div class="form-group">
            <label>Address:</label>
            <input type="text" name="address" required value="asd">
        </div>

        <div class="form-group-row">
            <div class="form-group">
                <label>City:</label>
                <input type="text" name="city" required value="asd">
            </div>
            <div class="form-group">
                <label>State:</label>
                <input type="text" name="state" required value="asd">
            </div>
            <div class="form-group">
                <label>ZIP:</label>
                <input type="text" name="zip" required value="asd">
            </div>
        </div>

        <h3>Products</h3>
        <button type="button" class="btn" onclick="addRow()">Add Product</button>

        <div class="table-container">
            <table id="items">
                <tr>
                    <th>Product Name</th>
                    <th>Packing</th>
                    <th>Billing Price</th>
                    <th>Product Code</th>
                    <th>Action</th>
                </tr>
            </table>
        </div>

        <button type="submit" class="btn">Save & Generate PDF</button>
    </form>
</div>
</body>
</html>