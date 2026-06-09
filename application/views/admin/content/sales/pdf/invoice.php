<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #333; font-size: 12px; }
        .header { border-bottom: 2px solid #2f75b5; padding-bottom: 12px; margin-bottom: 20px; }
        .title { font-size: 28px; color: #2f75b5; font-weight: bold; }
        .meta { text-align: right; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f2f6fa; }
        .no-border td { border: 0; }
        .right { text-align: right; }
        .footer { margin-top: 35px; padding-top: 12px; border-top: 1px solid #ddd; text-align: center; color: #777; }
    </style>
</head>
<body>
    <table class="header no-border">
        <tr>
            <td>
                <div class="title">Warehouse</div>
                <div>Sales Invoice</div>
            </td>
            <td class="meta">
                <strong>Invoice:</strong> <?php echo html_escape($sale->invoice_number); ?><br>
                <strong>Date:</strong> <?php echo date('d F Y', strtotime($sale->sale_date)); ?><br>
                <strong>Status:</strong> <?php echo ucfirst($sale->status); ?>
            </td>
        </tr>
    </table>

    <table class="no-border" style="margin-bottom: 20px;">
        <tr>
            <td>
                <strong>Customer Details</strong><br>
                <?php echo html_escape($sale->nama_customer); ?><br>
                <?php echo html_escape($sale->alamat_customer); ?><br>
                <?php echo html_escape($sale->notelp_customer); ?>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Unit</th>
                <th class="right">Quantity</th>
                <th class="right">Selling Price</th>
                <th class="right">Subtotal</th>
                <th class="right">Discount</th>
                <th class="right">Tax</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item) { ?>
                <tr>
                    <td><?php echo html_escape($item->nama_barang); ?></td>
                    <td><?php echo html_escape($item->unit_name); ?></td>
                    <td class="right"><?php echo (int) $item->quantity; ?></td>
                    <td class="right"><?php echo format_ksh($item->selling_price); ?></td>
                    <td class="right"><?php echo format_ksh($item->subtotal); ?></td>
                    <td class="right"><?php echo format_ksh($item->discount_amount); ?></td>
                    <td class="right"><?php echo format_ksh($item->tax_amount); ?></td>
                    <td class="right"><?php echo format_ksh($item->line_total); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <table style="margin-top: 20px;">
        <tr><td class="right"><strong>Subtotal</strong></td><td class="right" width="180"><?php echo format_ksh($sale->subtotal); ?></td></tr>
        <tr><td class="right"><strong>Discount</strong></td><td class="right"><?php echo format_ksh($sale->discount_total); ?></td></tr>
        <tr><td class="right"><strong>Tax</strong></td><td class="right"><?php echo format_ksh($sale->tax_total); ?></td></tr>
        <tr><td class="right"><strong>Grand Total</strong></td><td class="right"><strong><?php echo format_ksh($sale->grand_total); ?></strong></td></tr>
    </table>

    <div class="footer">Developed by Abdullahi Omar Salad.</div>
</body>
</html>
