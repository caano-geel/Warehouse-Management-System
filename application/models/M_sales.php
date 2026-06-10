<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_sales extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	private function apply_date_range($column, $date_from = NULL, $date_to = NULL)
	{
		if (!empty($date_from)) {
			$this->db->where($column.' >=', $date_from.' 00:00:00');
		}
		if (!empty($date_to)) {
			$this->db->where($column.' <', date('Y-m-d', strtotime($date_to.' +1 day')).' 00:00:00');
		}
	}

	public function get_customers()
	{
		return $this->db->select('id_customer, nama_customer, alamat_customer, notelp_customer')
			->order_by('nama_customer', 'ASC')
			->get('customer')
			->result();
	}

	public function get_goods()
	{
		return $this->db->select('id_barang, nama_barang, merek, stock')
			->order_by('nama_barang', 'ASC')
			->get('master_barang')
			->result();
	}

	public function get_units($active_only = TRUE)
	{
		if ($active_only) {
			$this->db->where('status', 'active');
		}

		return $this->db->order_by('unit_name', 'ASC')->get('units')->result();
	}

	public function get_taxes($active_only = TRUE)
	{
		if ($active_only) {
			$this->db->where('status', 'active');
		}

		return $this->db->order_by('tax_name', 'ASC')->get('taxes')->result();
	}

	public function get_discounts($active_only = TRUE)
	{
		if ($active_only) {
			$this->db->where('status', 'active');
		}

		return $this->db->order_by('discount_name', 'ASC')->get('discounts')->result();
	}

	public function generate_invoice_number()
	{
		$prefix = 'INV-'.date('Ymd').'-';
		$this->db->like('invoice_number', $prefix, 'after');
		$this->db->order_by('invoice_number', 'DESC');
		$row = $this->db->get('sales')->row();
		$next = 1;

		if ($row && !empty($row->invoice_number)) {
			$parts = explode('-', $row->invoice_number);
			$next = ((int) end($parts)) + 1;
		}

		return $prefix.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
	}

	public function create_sale($data)
	{
		$items = isset($data['items']) ? $data['items'] : array();
		if (empty($items)) {
			return array('success' => FALSE, 'message' => 'Please add at least one product.');
		}

		foreach ($items as $item) {
			$goods = $this->db->where('id_barang', (int) $item['id_barang'])->get('master_barang')->row();
			if (!$goods) {
				return array('success' => FALSE, 'message' => 'One selected product was not found.');
			}
			if ((int) $item['quantity'] <= 0) {
				return array('success' => FALSE, 'message' => 'Quantity must be greater than zero.');
			}
			if ((int) $goods->stock < (int) $item['quantity']) {
				return array('success' => FALSE, 'message' => $goods->nama_barang.' only has '.$goods->stock.' item(s) in stock.');
			}
		}

		$this->db->trans_start();

		$sale = array(
			'invoice_number' => $data['invoice_number'],
			'id_customer' => (int) $data['id_customer'],
			'admin_user' => $data['admin_user'],
			'sale_date' => $data['sale_date'],
			'subtotal' => $data['subtotal'],
			'discount_total' => $data['discount_total'],
			'tax_total' => $data['tax_total'],
			'grand_total' => $data['grand_total'],
			'status' => 'active',
			'notes' => isset($data['notes']) ? $data['notes'] : '',
		);
		$this->db->insert('sales', $sale);
		$sale_id = $this->db->insert_id();

		foreach ($items as $item) {
			$this->db->set('stock', 'stock - '.(int) $item['quantity'], FALSE);
			$this->db->where('id_barang', (int) $item['id_barang']);
			$this->db->where('stock >=', (int) $item['quantity']);
			$this->db->update('master_barang');

			if ($this->db->affected_rows() < 1) {
				$this->db->trans_rollback();
				return array('success' => FALSE, 'message' => 'Stock changed while saving. Please review the sale.');
			}

			$this->db->insert('sale_items', array(
				'sale_id' => $sale_id,
				'id_barang' => (int) $item['id_barang'],
				'unit_id' => (int) $item['unit_id'],
				'quantity' => (int) $item['quantity'],
				'selling_price' => $item['selling_price'],
				'discount_type' => $item['discount_type'],
				'discount_value' => $item['discount_value'],
				'discount_amount' => $item['discount_amount'],
				'tax_id' => $item['tax_id'] ? (int) $item['tax_id'] : NULL,
				'tax_rate' => $item['tax_rate'],
				'tax_amount' => $item['tax_amount'],
				'subtotal' => $item['subtotal'],
				'line_total' => $item['line_total'],
			));
		}

		$this->db->insert('invoices', array(
			'sale_id' => $sale_id,
			'invoice_number' => $data['invoice_number'],
			'invoice_date' => $data['sale_date'],
			'total_amount' => $data['grand_total'],
			'status' => 'issued',
		));

		$this->db->trans_complete();

		if (!$this->db->trans_status()) {
			return array('success' => FALSE, 'message' => 'Sale could not be saved.');
		}

		return array('success' => TRUE, 'sale_id' => $sale_id, 'invoice_number' => $data['invoice_number']);
	}

	public function get_sales($filters = array(), $limit = NULL, $start = NULL)
	{
		$this->sales_query($filters);
		$this->db->order_by('s.sale_date', 'DESC');
		$this->db->order_by('s.sale_id', 'DESC');
		if ($limit !== NULL) {
			$this->db->limit($limit, $start);
		}

		return $this->db->get()->result();
	}

	public function count_sales($filters = array())
	{
		$this->sales_query($filters, TRUE);
		$row = $this->db->get()->row();
		return $row ? (int) $row->total : 0;
	}

	private function sales_query($filters, $count_only = FALSE)
	{
		if ($count_only) {
			$this->db->select('COUNT(*) AS total', FALSE);
		} else {
			$this->db->select('s.*, c.nama_customer, c.alamat_customer, c.notelp_customer, a.admin_nama');
		}
		$this->db->from('sales s');
		$this->db->join('customer c', 'c.id_customer = s.id_customer', 'left');
		$this->db->join('admin a', 'a.admin_user = s.admin_user', 'left');

		if (!empty($filters['invoice_number'])) {
			$this->db->like('s.invoice_number', $filters['invoice_number']);
		}
		if (!empty($filters['customer'])) {
			$this->db->like('c.nama_customer', $filters['customer']);
		}
		$this->apply_date_range('s.sale_date', isset($filters['date_from']) ? $filters['date_from'] : NULL, isset($filters['date_to']) ? $filters['date_to'] : NULL);
	}

	public function get_sale($sale_id)
	{
		$this->db->select('s.*, c.nama_customer, c.alamat_customer, c.notelp_customer, a.admin_nama');
		$this->db->from('sales s');
		$this->db->join('customer c', 'c.id_customer = s.id_customer', 'left');
		$this->db->join('admin a', 'a.admin_user = s.admin_user', 'left');
		$this->db->where('s.sale_id', (int) $sale_id);

		return $this->db->get()->row();
	}

	public function get_sale_items($sale_id)
	{
		$this->db->select('si.*, mb.nama_barang, mb.merek, u.unit_name');
		$this->db->from('sale_items si');
		$this->db->join('master_barang mb', 'mb.id_barang = si.id_barang', 'left');
		$this->db->join('units u', 'u.unit_id = si.unit_id', 'left');
		$this->db->where('si.sale_id', (int) $sale_id);

		return $this->db->get()->result();
	}

	public function cancel_sale($sale_id, $admin_user)
	{
		$sale = $this->get_sale($sale_id);
		if (!$sale) {
			return array('success' => FALSE, 'message' => 'Sale was not found.');
		}
		if ($sale->status === 'cancelled') {
			return array('success' => FALSE, 'message' => 'Sale is already cancelled.');
		}

		$items = $this->get_sale_items($sale_id);
		$this->db->trans_start();
		foreach ($items as $item) {
			$this->db->set('stock', 'stock + '.(int) $item->quantity, FALSE);
			$this->db->where('id_barang', (int) $item->id_barang);
			$this->db->update('master_barang');
		}
		$this->db->where('sale_id', (int) $sale_id)->update('sales', array(
			'status' => 'cancelled',
			'cancelled_by' => $admin_user,
			'cancelled_at' => date('Y-m-d H:i:s'),
		));
		$this->db->where('sale_id', (int) $sale_id)->update('invoices', array('status' => 'cancelled'));
		$this->db->trans_complete();

		return array('success' => $this->db->trans_status(), 'message' => $this->db->trans_status() ? 'Sale cancelled and stock restored.' : 'Sale could not be cancelled.');
	}

	public function report_summary($date_from, $date_to)
	{
		$this->db->select('COALESCE(SUM(grand_total), 0) AS total_sales, COALESCE(SUM(tax_total), 0) AS total_tax, COALESCE(SUM(discount_total), 0) AS total_discounts, COUNT(*) AS total_invoices', FALSE);
		$this->db->from('sales');
		$this->db->where('status', 'active');
		$this->apply_date_range('sale_date', $date_from, $date_to);

		return $this->db->get()->row();
	}

	public function best_selling_products($date_from, $date_to)
	{
		$this->db->select('mb.nama_barang, SUM(si.quantity) AS quantity_sold, SUM(si.line_total) AS total_sales');
		$this->db->from('sale_items si');
		$this->db->join('sales s', 's.sale_id = si.sale_id', 'inner');
		$this->db->join('master_barang mb', 'mb.id_barang = si.id_barang', 'left');
		$this->db->where('s.status', 'active');
		$this->apply_date_range('s.sale_date', $date_from, $date_to);
		$this->db->group_by('si.id_barang');
		$this->db->order_by('quantity_sold', 'DESC');
		$this->db->limit(10);

		return $this->db->get()->result();
	}

	public function top_customers($date_from, $date_to)
	{
		$this->db->select('c.nama_customer, COUNT(s.sale_id) AS invoice_count, SUM(s.grand_total) AS total_sales');
		$this->db->from('sales s');
		$this->db->join('customer c', 'c.id_customer = s.id_customer', 'left');
		$this->db->where('s.status', 'active');
		$this->apply_date_range('s.sale_date', $date_from, $date_to);
		$this->db->group_by('s.id_customer');
		$this->db->order_by('total_sales', 'DESC');
		$this->db->limit(10);

		return $this->db->get()->result();
	}

	public function dashboard_sales()
	{
		if (!$this->db->table_exists('sales') || !$this->db->table_exists('invoices')) {
			return array(
				'today_sales' => 0,
				'monthly_sales' => 0,
				'total_invoices' => 0,
				'low_stock_items' => 0,
			);
		}

		$today_start = date('Y-m-d').' 00:00:00';
		$tomorrow_start = date('Y-m-d', strtotime('+1 day')).' 00:00:00';
		$month_start = date('Y-m-01').' 00:00:00';
		$next_month_start = date('Y-m-d', strtotime(date('Y-m-01').' +1 month')).' 00:00:00';

		$this->db->select(
			'COALESCE(SUM(CASE WHEN sale_date >= '.$this->db->escape($today_start).' AND sale_date < '.$this->db->escape($tomorrow_start).' THEN grand_total ELSE 0 END), 0) AS today_sales,
			COALESCE(SUM(CASE WHEN sale_date >= '.$this->db->escape($month_start).' AND sale_date < '.$this->db->escape($next_month_start).' THEN grand_total ELSE 0 END), 0) AS monthly_sales',
			FALSE
		);
		$this->db->from('sales');
		$this->db->where('status', 'active');
		$summary = $this->db->get()->row();

		$low_stock_limit = 2;
		$limit = $this->db->get('limitstock')->row();
		if ($limit) {
			$low_stock_limit = (int) $limit->stock;
		}

		$low_stock = $this->db->where('stock <=', $low_stock_limit)->count_all_results('master_barang');

		return array(
			'today_sales' => $summary ? (float) $summary->today_sales : 0,
			'monthly_sales' => $summary ? (float) $summary->monthly_sales : 0,
			'total_invoices' => $this->db->where('status', 'issued')->count_all_results('invoices'),
			'low_stock_items' => $low_stock,
		);
	}

	public function save_simple($table, $data, $primary_key = NULL, $id = NULL)
	{
		if ($id) {
			$this->db->where($primary_key, (int) $id)->update($table, $data);
			return $id;
		}
		$this->db->insert($table, $data);
		return $this->db->insert_id();
	}

	public function get_simple($table, $primary_key, $id)
	{
		return $this->db->where($primary_key, (int) $id)->get($table)->row();
	}

	public function delete_simple($table, $primary_key, $id)
	{
		$this->db->where($primary_key, (int) $id)->delete($table);
	}
}
