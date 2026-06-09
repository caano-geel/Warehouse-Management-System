<div class="page">
    <div class="page-title blue">
        <h3><?php echo $breadcrumb; ?></h3>
        <p>Record customer sales and generate invoices in KSh.</p>
    </div>
    <div class="page-content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-heading"><h5 class="panel-title">Create Invoice <?php echo html_escape($invoice_number); ?></h5></div>
                    <div class="panel-body container-fluid">
                        <?php if ($this->session->flashdata('error')) { ?>
                            <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
                        <?php } ?>
                        <form action="<?php echo site_url('sales/new_sale'); ?>" method="post" id="saleForm" autocomplete="off">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-material">
                                        <label class="control-label">Customer</label>
                                        <select name="id_customer" class="form-control" required>
                                            <option value="">Select customer</option>
                                            <?php foreach ($customers as $customer) { ?>
                                                <option value="<?php echo $customer->id_customer; ?>"><?php echo html_escape($customer->nama_customer); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="saleItems">
                                    <thead>
                                        <tr>
                                            <th>Product/Goods</th>
                                            <th width="90">Stock</th>
                                            <th width="95">Qty</th>
                                            <th width="120">Unit</th>
                                            <th width="140">Selling Price in KSh</th>
                                            <th width="160">Discount</th>
                                            <th width="135">Tax</th>
                                            <th width="140">Subtotal</th>
                                            <th width="60"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="sale-row">
                                            <td>
                                                <select name="id_barang[]" class="form-control product-select" required>
                                                    <option value="">Select product</option>
                                                    <?php foreach ($goods as $item) { ?>
                                                        <option value="<?php echo $item->id_barang; ?>" data-stock="<?php echo (int) $item->stock; ?>"><?php echo html_escape($item->nama_barang.' - '.$item->merek); ?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                            <td><input type="text" class="form-control stock-view" readonly value="0"></td>
                                            <td><input type="number" name="quantity[]" class="form-control qty" min="1" value="1" required></td>
                                            <td>
                                                <select name="unit_id[]" class="form-control" required>
                                                    <?php foreach ($units as $unit) { ?>
                                                        <option value="<?php echo $unit->unit_id; ?>"><?php echo html_escape($unit->unit_name); ?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                            <td><input type="number" name="selling_price[]" class="form-control price" min="0" step="0.01" value="0.00" required></td>
                                            <td>
                                                <div class="input-group">
                                                    <select name="discount_type[]" class="form-control discount-type">
                                                        <option value="percentage">%</option>
                                                        <option value="fixed">KSh</option>
                                                    </select>
                                                    <input type="number" name="discount_value[]" class="form-control discount-value" min="0" step="0.01" value="0">
                                                </div>
                                            </td>
                                            <td>
                                                <select name="tax_id[]" class="form-control tax-select">
                                                    <option value="" data-rate="0">No tax</option>
                                                    <?php foreach ($taxes as $tax) { ?>
                                                        <option value="<?php echo $tax->tax_id; ?>" data-rate="<?php echo (float) $tax->tax_rate; ?>"><?php echo html_escape($tax->tax_name.' '.$tax->tax_rate.'%'); ?></option>
                                                    <?php } ?>
                                                </select>
                                                <input type="hidden" name="tax_rate[]" class="tax-rate" value="0">
                                            </td>
                                            <td><input type="text" class="form-control line-total" readonly value="KSh 0.00"></td>
                                            <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fa fa-trash"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <button type="button" class="btn btn-info btn-sm" id="addRow"><i class="fa fa-plus"></i> Add Product</button>

                            <div class="row" style="margin-top: 20px;">
                                <div class="col-md-4 col-md-offset-8">
                                    <table class="table table-bordered">
                                        <tr><th>Subtotal</th><td class="text-right" id="subtotalText">KSh 0.00</td></tr>
                                        <tr><th>Discount</th><td class="text-right" id="discountText">KSh 0.00</td></tr>
                                        <tr><th>Tax</th><td class="text-right" id="taxText">KSh 0.00</td></tr>
                                        <tr><th>Grand Total</th><td class="text-right"><strong id="grandText">KSh 0.00</strong></td></tr>
                                    </table>
                                </div>
                            </div>

                            <div class="button center">
                                <button class="btn btn-success btn-sm" type="submit" name="simpan" value="1">Save Sale</button>
                                <a class="btn btn-danger btn-sm" href="<?php echo site_url('sales/records'); ?>">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
(function($) {
    function money(value) {
        return 'KSh ' + Number(value || 0).toLocaleString('en-KE', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
    function calculate() {
        var subtotal = 0, discount = 0, tax = 0, grand = 0;
        $('#saleItems tbody tr').each(function() {
            var row = $(this);
            var stock = Number(row.find('.product-select option:selected').data('stock') || 0);
            var qty = Math.max(0, Number(row.find('.qty').val() || 0));
            var price = Math.max(0, Number(row.find('.price').val() || 0));
            var lineSubtotal = qty * price;
            var discValue = Math.max(0, Number(row.find('.discount-value').val() || 0));
            var discType = row.find('.discount-type').val();
            var discAmount = discType === 'fixed' ? Math.min(discValue, lineSubtotal) : lineSubtotal * Math.min(discValue, 100) / 100;
            var rate = Number(row.find('.tax-select option:selected').data('rate') || 0);
            var taxable = Math.max(0, lineSubtotal - discAmount);
            var taxAmount = taxable * rate / 100;
            var lineTotal = taxable + taxAmount;

            row.find('.stock-view').val(stock);
            row.find('.tax-rate').val(rate);
            row.find('.line-total').val(money(lineTotal));
            row.toggleClass('danger', qty > stock && row.find('.product-select').val() !== '');

            subtotal += lineSubtotal;
            discount += discAmount;
            tax += taxAmount;
            grand += lineTotal;
        });
        $('#subtotalText').text(money(subtotal));
        $('#discountText').text(money(discount));
        $('#taxText').text(money(tax));
        $('#grandText').text(money(grand));
    }
    $('#addRow').on('click', function() {
        var row = $('#saleItems tbody tr:first').clone();
        row.find('select').val('');
        row.find('.qty').val('1');
        row.find('.price').val('0.00');
        row.find('.discount-value').val('0');
        row.find('.stock-view').val('0');
        row.find('.tax-rate').val('0');
        row.find('.line-total').val('KSh 0.00');
        $('#saleItems tbody').append(row);
    });
    $(document).on('click', '.remove-row', function() {
        if ($('#saleItems tbody tr').length > 1) {
            $(this).closest('tr').remove();
            calculate();
        }
    });
    $(document).on('change keyup', '#saleItems input, #saleItems select', calculate);
    $('#saleForm').on('submit', function(e) {
        var invalid = false;
        $('#saleItems tbody tr').each(function() {
            var row = $(this);
            var stock = Number(row.find('.product-select option:selected').data('stock') || 0);
            var qty = Number(row.find('.qty').val() || 0);
            if (row.find('.product-select').val() !== '' && (qty < 1 || qty > stock)) {
                invalid = true;
            }
        });
        if (invalid) {
            alert('Please check quantities. Stock cannot go negative.');
            e.preventDefault();
        }
    });
    calculate();
})(jQuery);
</script>
