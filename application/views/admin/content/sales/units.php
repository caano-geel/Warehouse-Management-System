<div class="page">
    <div class="page-title blue"><h3><?php echo $breadcrumb; ?></h3><p>Manage sale units such as pcs, box, carton, kg, and litre.</p></div>
    <div class="page-content container-fluid">
        <div class="panel">
            <div class="panel-heading"><h5 class="panel-title"><?php echo $action === 'edit' ? 'Edit Unit' : 'Unit Management'; ?></h5></div>
            <div class="panel-body">
                <?php if ($this->session->flashdata('success')) { ?><div class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div><?php } ?>
                <?php if ($action === 'tambah' || $action === 'edit') { ?>
                    <form method="post" action="<?php echo site_url('sales/units/'.$action.($row ? '/'.$row->unit_id : '')); ?>">
                        <div class="form-group"><label>Unit Name</label><input type="text" name="unit_name" class="form-control" required value="<?php echo $row ? html_escape($row->unit_name) : ''; ?>"></div>
                        <div class="form-group"><label>Status</label><select name="status" class="form-control"><option value="active" <?php echo !$row || $row->status === 'active' ? 'selected' : ''; ?>>Active</option><option value="inactive" <?php echo $row && $row->status === 'inactive' ? 'selected' : ''; ?>>Inactive</option></select></div>
                        <button class="btn btn-success" name="simpan" value="1">Save</button> <a class="btn btn-default" href="<?php echo site_url('sales/units'); ?>">Cancel</a>
                    </form>
                <?php } else { ?>
                    <a class="btn btn-success" href="<?php echo site_url('sales/units/tambah'); ?>"><i class="fa fa-plus"></i> Add Unit</a>
                    <div class="table-responsive" style="margin-top:20px;"><table class="table table-bordered table-striped"><thead><tr><th>Unit</th><th>Status</th><th class="text-center">Action</th></tr></thead><tbody>
                    <?php if ($rows) { foreach ($rows as $item) { ?><tr><td><?php echo html_escape($item->unit_name); ?></td><td><?php echo ucfirst($item->status); ?></td><td class="text-center"><a class="btn btn-info btn-xs" href="<?php echo site_url('sales/units/edit/'.$item->unit_id); ?>">Edit</a> <a class="btn btn-danger btn-xs" href="<?php echo site_url('sales/units/hapus/'.$item->unit_id); ?>" onclick="return confirm('Delete this unit?');">Delete</a></td></tr><?php }} else { ?><tr><td colspan="3">No units found.</td></tr><?php } ?>
                    </tbody></table></div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
