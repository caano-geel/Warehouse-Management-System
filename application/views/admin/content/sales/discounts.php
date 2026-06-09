<div class="page">
    <div class="page-title blue"><h3><?php echo $breadcrumb; ?></h3><p>Manage percentage and fixed KSh discounts.</p></div>
    <div class="page-content container-fluid">
        <div class="panel">
            <div class="panel-heading"><h5 class="panel-title"><?php echo $action === 'edit' ? 'Edit Discount' : 'Discounts'; ?></h5></div>
            <div class="panel-body">
                <?php if ($this->session->flashdata('success')) { ?><div class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div><?php } ?>
                <?php if ($action === 'tambah' || $action === 'edit') { ?>
                    <form method="post" action="<?php echo site_url('sales/discounts/'.$action.($row ? '/'.$row->discount_id : '')); ?>">
                        <div class="form-group"><label>Name</label><input type="text" name="discount_name" class="form-control" required value="<?php echo $row ? html_escape($row->discount_name) : ''; ?>"></div>
                        <div class="form-group"><label>Type</label><select name="discount_type" class="form-control"><option value="percentage" <?php echo $row && $row->discount_type === 'percentage' ? 'selected' : ''; ?>>Percentage</option><option value="fixed" <?php echo $row && $row->discount_type === 'fixed' ? 'selected' : ''; ?>>Fixed amount in KSh</option></select></div>
                        <div class="form-group"><label>Value</label><input type="number" name="discount_value" class="form-control" min="0" step="0.01" required value="<?php echo $row ? html_escape($row->discount_value) : '0'; ?>"></div>
                        <div class="form-group"><label>Status</label><select name="status" class="form-control"><option value="active" <?php echo !$row || $row->status === 'active' ? 'selected' : ''; ?>>Active</option><option value="inactive" <?php echo $row && $row->status === 'inactive' ? 'selected' : ''; ?>>Inactive</option></select></div>
                        <button class="btn btn-success" name="simpan" value="1">Save</button> <a class="btn btn-default" href="<?php echo site_url('sales/discounts'); ?>">Cancel</a>
                    </form>
                <?php } else { ?>
                    <a class="btn btn-success" href="<?php echo site_url('sales/discounts/tambah'); ?>"><i class="fa fa-plus"></i> Add Discount</a>
                    <div class="table-responsive" style="margin-top:20px;"><table class="table table-bordered table-striped"><thead><tr><th>Name</th><th>Type</th><th>Value</th><th>Status</th><th class="text-center">Action</th></tr></thead><tbody>
                    <?php if ($rows) { foreach ($rows as $item) { ?><tr><td><?php echo html_escape($item->discount_name); ?></td><td><?php echo ucfirst($item->discount_type); ?></td><td><?php echo $item->discount_type === 'fixed' ? format_ksh($item->discount_value) : html_escape($item->discount_value).'%'; ?></td><td><?php echo ucfirst($item->status); ?></td><td class="text-center"><a class="btn btn-info btn-xs" href="<?php echo site_url('sales/discounts/edit/'.$item->discount_id); ?>">Edit</a> <a class="btn btn-danger btn-xs" href="<?php echo site_url('sales/discounts/hapus/'.$item->discount_id); ?>" onclick="return confirm('Delete this discount?');">Delete</a></td></tr><?php }} else { ?><tr><td colspan="5">No discounts found.</td></tr><?php } ?>
                    </tbody></table></div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
