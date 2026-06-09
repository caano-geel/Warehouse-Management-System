<div class="page">
    <div class="page-title blue">
        <h3><?php echo $breadcrumb; ?></h3>
        <p>Sales performance from <?php echo html_escape($range['from']); ?> to <?php echo html_escape($range['to']); ?>.</p>
    </div>
    <div class="page-content container-fluid">
        <div class="panel">
            <div class="panel-heading"><h5 class="panel-title">Sales Report</h5></div>
            <div class="panel-body">
                <form method="get" action="<?php echo site_url('sales/report'); ?>" class="row">
                    <div class="col-md-3">
                        <select name="range" class="form-control" id="rangeSelect">
                            <option value="daily" <?php echo $range['type'] === 'daily' ? 'selected' : ''; ?>>Daily</option>
                            <option value="weekly" <?php echo $range['type'] === 'weekly' ? 'selected' : ''; ?>>Weekly</option>
                            <option value="monthly" <?php echo $range['type'] === 'monthly' ? 'selected' : ''; ?>>Monthly</option>
                            <option value="custom" <?php echo $range['type'] === 'custom' ? 'selected' : ''; ?>>Custom date range</option>
                        </select>
                    </div>
                    <div class="col-md-3"><input type="date" name="date_from" class="form-control custom-date" value="<?php echo html_escape($range['from']); ?>"></div>
                    <div class="col-md-3"><input type="date" name="date_to" class="form-control custom-date" value="<?php echo html_escape($range['to']); ?>"></div>
                    <div class="col-md-3"><button class="btn btn-success" type="submit">Apply</button> <a class="btn btn-info" href="<?php echo site_url('sales/report_pdf?range='.$range['type'].'&date_from='.$range['from'].'&date_to='.$range['to']); ?>">Export PDF</a></div>
                </form>
                <div class="row" style="margin-top: 20px;">
                    <div class="col-md-3 col-sm-6"><div class="small-box bg-green"><div class="inner"><h3><?php echo format_ksh($summary->total_sales); ?></h3><p>Total Sales</p></div><div class="icon"><i class="fa fa-money"></i></div></div></div>
                    <div class="col-md-3 col-sm-6"><div class="small-box bg-aqua"><div class="inner"><h3><?php echo format_ksh($summary->total_tax); ?></h3><p>Total Tax</p></div><div class="icon"><i class="fa fa-percent"></i></div></div></div>
                    <div class="col-md-3 col-sm-6"><div class="small-box bg-yellow"><div class="inner"><h3><?php echo format_ksh($summary->total_discounts); ?></h3><p>Total Discounts</p></div><div class="icon"><i class="fa fa-tags"></i></div></div></div>
                    <div class="col-md-3 col-sm-6"><div class="small-box bg-red"><div class="inner"><h3><?php echo (int) $summary->total_invoices; ?></h3><p>Total Invoices</p></div><div class="icon"><i class="fa fa-file-text-o"></i></div></div></div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h4>Best Selling Products</h4>
                        <table class="table table-bordered table-striped">
                            <thead><tr><th>Product</th><th class="text-right">Qty</th><th class="text-right">Sales</th></tr></thead>
                            <tbody>
                                <?php if ($best_products) { foreach ($best_products as $product) { ?>
                                    <tr><td><?php echo html_escape($product->nama_barang); ?></td><td class="text-right"><?php echo (int) $product->quantity_sold; ?></td><td class="text-right"><?php echo format_ksh($product->total_sales); ?></td></tr>
                                <?php }} else { ?><tr><td colspan="3">No data.</td></tr><?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h4>Top Customers</h4>
                        <table class="table table-bordered table-striped">
                            <thead><tr><th>Customer</th><th class="text-right">Invoices</th><th class="text-right">Sales</th></tr></thead>
                            <tbody>
                                <?php if ($top_customers) { foreach ($top_customers as $customer) { ?>
                                    <tr><td><?php echo html_escape($customer->nama_customer); ?></td><td class="text-right"><?php echo (int) $customer->invoice_count; ?></td><td class="text-right"><?php echo format_ksh($customer->total_sales); ?></td></tr>
                                <?php }} else { ?><tr><td colspan="3">No data.</td></tr><?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
