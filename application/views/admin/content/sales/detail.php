<div class="page">
    <div class="page-title blue">
        <h3><?php echo $breadcrumb; ?></h3>
        <p><?php echo html_escape($sale->invoice_number); ?></p>
    </div>
    <div class="page-content container-fluid">
        <div class="panel">
            <div class="panel-heading"><h5 class="panel-title">Invoice Details</h5></div>
            <div class="panel-body">
                <?php if ($this->session->flashdata('success')) { ?><div class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div><?php } ?>
                <div class="row">
                    <div class="col-md-6">
                        <h4>Warehouse</h4>
                        <p><strong>Invoice:</strong> <?php echo html_escape($sale->invoice_number); ?><br>
                        <strong>Date:</strong> <?php echo dateIndo($sale->sale_date); ?><br>
                        <strong>Status:</strong> <?php echo ucfirst($sale->status); ?></p>
                    </div>
                    <div class="col-md-6 text-right">
                        <h4>Customer</h4>
                        <p><?php echo html_escape($sale->nama_customer); ?><br>
                        <?php echo html_escape($sale->alamat_customer); ?><br>
                        <?php echo html_escape($sale->notelp_customer); ?></p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Unit</th>
                                <th class="text-right">Qty</th>
                                <th class="text-right">Price</th>
                                <th class="text-right">Discount</th>
                                <th class="text-right">Tax</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item) { ?>
                                <tr>
                                    <td><?php echo html_escape($item->nama_barang); ?></td>
                                    <td><?php echo html_escape($item->unit_name); ?></td>
                                    <td class="text-right"><?php echo (int) $item->quantity; ?></td>
                                    <td class="text-right"><?php echo format_ksh($item->selling_price); ?></td>
                                    <td class="text-right"><?php echo format_ksh($item->discount_amount); ?></td>
                                    <td class="text-right"><?php echo format_ksh($item->tax_amount); ?></td>
                                    <td class="text-right"><?php echo format_ksh($item->line_total); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-md-4 col-md-offset-8">
                        <table class="table table-bordered">
                            <tr><th>Subtotal</th><td class="text-right"><?php echo format_ksh($sale->subtotal); ?></td></tr>
                            <tr><th>Discount</th><td class="text-right"><?php echo format_ksh($sale->discount_total); ?></td></tr>
                            <tr><th>Tax</th><td class="text-right"><?php echo format_ksh($sale->tax_total); ?></td></tr>
                            <tr><th>Grand Total</th><td class="text-right"><strong><?php echo format_ksh($sale->grand_total); ?></strong></td></tr>
                        </table>
                    </div>
                </div>
                <a class="btn btn-success" href="<?php echo site_url('sales/invoice_pdf/'.$sale->sale_id); ?>"><i class="fa fa-download"></i> Download Invoice PDF</a>
                <a class="btn btn-default" href="<?php echo site_url('sales/records'); ?>">Back</a>
            </div>
        </div>
    </div>
</div>
