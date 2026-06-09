<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #333; font-size: 12px; }
        h1 { color: #2f75b5; margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 14px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f2f6fa; }
        .right { text-align: right; }
        .summary td { font-size: 14px; }
        .footer { margin-top: 35px; padding-top: 12px; border-top: 1px solid #ddd; text-align: center; color: #777; }
    </style>
</head>
<body>
    <h1>Warehouse Sales Report</h1>
    <div><?php echo html_escape($range['from']); ?> to <?php echo html_escape($range['to']); ?></div>
    <table class="summary">
        <tr><td>Total Sales</td><td class="right"><?php echo format_ksh($summary->total_sales); ?></td></tr>
        <tr><td>Total Tax</td><td class="right"><?php echo format_ksh($summary->total_tax); ?></td></tr>
        <tr><td>Total Discounts</td><td class="right"><?php echo format_ksh($summary->total_discounts); ?></td></tr>
        <tr><td>Total Invoices</td><td class="right"><?php echo (int) $summary->total_invoices; ?></td></tr>
    </table>

    <h3>Best Selling Products</h3>
    <table>
        <thead><tr><th>Product</th><th class="right">Quantity</th><th class="right">Sales</th></tr></thead>
        <tbody>
            <?php if ($best_products) { foreach ($best_products as $product) { ?>
                <tr><td><?php echo html_escape($product->nama_barang); ?></td><td class="right"><?php echo (int) $product->quantity_sold; ?></td><td class="right"><?php echo format_ksh($product->total_sales); ?></td></tr>
            <?php }} else { ?><tr><td colspan="3">No data.</td></tr><?php } ?>
        </tbody>
    </table>

    <h3>Top Customers</h3>
    <table>
        <thead><tr><th>Customer</th><th class="right">Invoices</th><th class="right">Sales</th></tr></thead>
        <tbody>
            <?php if ($top_customers) { foreach ($top_customers as $customer) { ?>
                <tr><td><?php echo html_escape($customer->nama_customer); ?></td><td class="right"><?php echo (int) $customer->invoice_count; ?></td><td class="right"><?php echo format_ksh($customer->total_sales); ?></td></tr>
            <?php }} else { ?><tr><td colspan="3">No data.</td></tr><?php } ?>
        </tbody>
    </table>

    <div class="footer">Developed by Abdullahi Omar Salad.</div>
</body>
</html>
