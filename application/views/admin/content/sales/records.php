<div class="page">
    <div class="page-title blue">
        <h3><?php echo $breadcrumb; ?></h3>
        <p>Search, view, reprint, and cancel sales invoices.</p>
    </div>
    <div class="page-content container-fluid">
        <div class="panel">
            <div class="panel-heading"><h5 class="panel-title">Sales Records</h5></div>
            <div class="panel-body">
                <?php if ($this->session->flashdata('success')) { ?><div class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div><?php } ?>
                <?php if ($this->session->flashdata('error')) { ?><div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div><?php } ?>
                <form method="get" action="<?php echo site_url('sales/records'); ?>" class="row">
                    <div class="col-md-3"><input type="text" name="invoice_number" class="form-control" placeholder="Invoice number" value="<?php echo html_escape($filters['invoice_number']); ?>"></div>
                    <div class="col-md-3"><input type="text" name="customer" class="form-control" placeholder="Customer" value="<?php echo html_escape($filters['customer']); ?>"></div>
                    <div class="col-md-2"><input type="date" name="date_from" class="form-control" value="<?php echo html_escape($filters['date_from']); ?>"></div>
                    <div class="col-md-2"><input type="date" name="date_to" class="form-control" value="<?php echo html_escape($filters['date_to']); ?>"></div>
                    <div class="col-md-2"><button class="btn btn-success btn-block" type="submit"><i class="fa fa-search"></i> Search</button></div>
                </form>
                <div class="table-responsive" style="margin-top:20px;">
                    <table class="table table-hover table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th class="text-right">Grand Total</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($sales) { foreach ($sales as $sale) { ?>
                                <tr>
                                    <td><?php echo html_escape($sale->invoice_number); ?></td>
                                    <td><?php echo html_escape($sale->nama_customer); ?></td>
                                    <td><?php echo dateIndo($sale->sale_date); ?></td>
                                    <td class="text-right"><?php echo format_ksh($sale->grand_total); ?></td>
                                    <td><?php echo ucfirst($sale->status); ?></td>
                                    <td class="text-center">
                                        <a class="btn btn-info btn-xs" href="<?php echo site_url('sales/detail/'.$sale->sale_id); ?>">View</a>
                                        <a class="btn btn-success btn-xs" href="<?php echo site_url('sales/invoice_pdf/'.$sale->sale_id); ?>">PDF</a>
                                        <?php if ((string) $admin->admin_level_kode === '1' && $sale->status === 'active') { ?>
                                            <a class="btn btn-danger btn-xs" href="<?php echo site_url('sales/cancel/'.$sale->sale_id); ?>" onclick="return confirm('Cancel this sale and restore stock?');">Cancel</a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php }} else { ?>
                                <tr><td colspan="6">No sales found.</td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <a class="btn btn-success" href="<?php echo site_url('sales/new_sale'); ?>"><i class="fa fa-plus"></i> New Sale</a>
            </div>
        </div>
    </div>
</div>
