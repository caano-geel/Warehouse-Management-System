<div class="page">
    <div class="page-title blue"><h3><?php echo $breadcrumb; ?></h3><p>Manage sales taxes such as VAT 16%.</p></div>
    <div class="page-content container-fluid">
        <div class="panel">
            <div class="panel-heading"><h5 class="panel-title"><?php echo $action === 'edit' ? 'Edit Tax' : 'Tax Management'; ?></h5></div>
            <div class="panel-body">
                <?php if ($this->session->flashdata('success')) { ?><div class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div><?php } ?>
                <?php if ($action === 'tambah' || $action === 'edit') { ?>
                    <form method="post" action="<?php echo site_url('sales/taxes/'.$action.($row ? '/'.$row->tax_id : '')); ?>">
                        <div class="form-group"><label>Name</label><input type="text" name="tax_name" class="form-control" required value="<?php echo $row ? html_escape($row->tax_name) : ''; ?>"></div>
                        <div class="form-group"><label>Percentage</label><input type="number" name="tax_rate" class="form-control" min="0" step="0.01" required value="<?php echo $row ? html_escape($row->tax_rate) : '16'; ?>"></div>
                        <div class="form-group"><label>Status</label><select name="status" class="form-control"><option value="active" <?php echo !$row || $row->status === 'active' ? 'selected' : ''; ?>>Active</option><option value="inactive" <?php echo $row && $row->status === 'inactive' ? 'selected' : ''; ?>>Inactive</option></select></div>
                        <button class="btn btn-success" name="simpan" value="1">Save</button> <a class="btn btn-default" href="<?php echo site_url('sales/taxes'); ?>">Cancel</a>
                    </form>
                <?php } else { ?>
                    <a class="btn btn-success" href="<?php echo site_url('sales/taxes/tambah'); ?>"><i class="fa fa-plus"></i> Add Tax</a>
                    <div class="table-responsive" style="margin-top:20px;"><table class="table table-bordered table-striped"><thead><tr><th>Name</th><th>Percentage</th><th>Status</th><th class="text-center">Action</th></tr></thead><tbody>
                    <?php if ($rows) { foreach ($rows as $item) { ?><tr><td><?php echo html_escape($item->tax_name); ?></td><td><?php echo html_escape($item->tax_rate); ?>%</td><td><?php echo ucfirst($item->status); ?></td><td class="text-center"><a class="btn btn-info btn-xs" href="<?php echo site_url('sales/taxes/edit/'.$item->tax_id); ?>">Edit</a> <a class="btn btn-danger btn-xs" href="<?php echo site_url('sales/taxes/hapus/'.$item->tax_id); ?>" onclick="return confirm('Delete this tax?');">Delete</a></td></tr><?php }} else { ?><tr><td colspan="4">No taxes found.</td></tr><?php } ?>
                    </tbody></table></div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
