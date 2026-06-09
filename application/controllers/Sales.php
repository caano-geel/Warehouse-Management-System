<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sales extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('M_admin', 'ADM', TRUE);
		$this->load->model('M_config', 'CONF', TRUE);
		$this->load->model('M_sales', 'SALES', TRUE);
	}

	public function index()
	{
		redirect('sales/new_sale');
	}

	private function require_login()
	{
		if ($this->session->userdata('logged_in') != TRUE) {
			redirect('login');
		}

		$where_admin['admin_user'] = $this->session->userdata('admin_user');
		return $this->ADM->get_admin('', $where_admin);
	}

	private function render($content, $breadcrumb, $data = array())
	{
		$admin = $this->require_login();
		$data['admin'] = $admin;
		$data['web'] = $this->ADM->identitaswebsite();
		$data['dashboard_info'] = FALSE;
		$data['breadcrumb'] = $breadcrumb;
		$data['content'] = $content;
		$data['menu_terpilih'] = 'sales';
		$data['submenu_terpilih'] = '';
		$this->load->vars($data);
		$this->load->view('admin/home');
	}

	private function ensure_sales_tables()
	{
		$tables = array('sales', 'sale_items', 'invoices', 'taxes', 'discounts', 'units');
		foreach ($tables as $table) {
			if (!$this->db->table_exists($table)) {
				$this->session->set_flashdata('error', 'Sales tables are not installed yet. Import DATABASE FILE/2026_06_08_sales_invoicing.sql first.');
				redirect('admin');
			}
		}
	}

	public function new_sale()
	{
		$this->ensure_sales_tables();
		$admin = $this->require_login();

		if ($this->input->post('simpan')) {
			$result = $this->save_sale($admin);
			if ($result['success']) {
				$this->session->set_flashdata('success', 'Sale saved. Invoice '.$result['invoice_number'].' is ready.');
				redirect('sales/detail/'.$result['sale_id']);
			}
			$this->session->set_flashdata('error', $result['message']);
		}

		$data['customers'] = $this->SALES->get_customers();
		$data['goods'] = $this->SALES->get_goods();
		$data['units'] = $this->SALES->get_units(TRUE);
		$data['taxes'] = $this->SALES->get_taxes(TRUE);
		$data['discounts'] = $this->SALES->get_discounts(TRUE);
		$data['invoice_number'] = $this->SALES->generate_invoice_number();
		$this->render('admin/content/sales/new_sale', 'New Sale', $data);
	}

	private function save_sale($admin)
	{
		$id_customer = (int) $this->input->post('id_customer');
		$id_barang = (array) $this->input->post('id_barang');
		$unit_id = (array) $this->input->post('unit_id');
		$quantity = (array) $this->input->post('quantity');
		$selling_price = (array) $this->input->post('selling_price');
		$discount_type = (array) $this->input->post('discount_type');
		$discount_value = (array) $this->input->post('discount_value');
		$tax_id = (array) $this->input->post('tax_id');
		$tax_rate = (array) $this->input->post('tax_rate');

		if ($id_customer < 1) {
			return array('success' => FALSE, 'message' => 'Please select a customer.');
		}

		$items = array();
		$subtotal_total = 0;
		$discount_total = 0;
		$tax_total = 0;
		$grand_total = 0;

		foreach ($id_barang as $idx => $goods_id) {
			$goods_id = (int) $goods_id;
			if ($goods_id < 1) {
				continue;
			}

			$qty = max(0, (int) (isset($quantity[$idx]) ? $quantity[$idx] : 0));
			$price = max(0, (float) (isset($selling_price[$idx]) ? $selling_price[$idx] : 0));
			$disc_type = isset($discount_type[$idx]) && $discount_type[$idx] === 'fixed' ? 'fixed' : 'percentage';
			$disc_value = max(0, (float) (isset($discount_value[$idx]) ? $discount_value[$idx] : 0));
			$rate = max(0, (float) (isset($tax_rate[$idx]) ? $tax_rate[$idx] : 0));
			$line_subtotal = $qty * $price;
			$discount_amount = $disc_type === 'percentage' ? ($line_subtotal * min($disc_value, 100) / 100) : min($disc_value, $line_subtotal);
			$taxable = max(0, $line_subtotal - $discount_amount);
			$tax_amount = $taxable * $rate / 100;
			$line_total = $taxable + $tax_amount;

			$items[] = array(
				'id_barang' => $goods_id,
				'unit_id' => (int) (isset($unit_id[$idx]) ? $unit_id[$idx] : 0),
				'quantity' => $qty,
				'selling_price' => $price,
				'discount_type' => $disc_type,
				'discount_value' => $disc_value,
				'discount_amount' => $discount_amount,
				'tax_id' => !empty($tax_id[$idx]) ? (int) $tax_id[$idx] : NULL,
				'tax_rate' => $rate,
				'tax_amount' => $tax_amount,
				'subtotal' => $line_subtotal,
				'line_total' => $line_total,
			);

			$subtotal_total += $line_subtotal;
			$discount_total += $discount_amount;
			$tax_total += $tax_amount;
			$grand_total += $line_total;
		}

		return $this->SALES->create_sale(array(
			'invoice_number' => $this->SALES->generate_invoice_number(),
			'id_customer' => $id_customer,
			'admin_user' => $admin->admin_user,
			'sale_date' => date('Y-m-d H:i:s'),
			'subtotal' => $subtotal_total,
			'discount_total' => $discount_total,
			'tax_total' => $tax_total,
			'grand_total' => $grand_total,
			'items' => $items,
		));
	}

	public function records()
	{
		$this->ensure_sales_tables();
		$filters = array(
			'invoice_number' => $this->input->get('invoice_number', TRUE),
			'customer' => $this->input->get('customer', TRUE),
			'date_from' => $this->input->get('date_from', TRUE),
			'date_to' => $this->input->get('date_to', TRUE),
		);
		$data['filters'] = $filters;
		$data['sales'] = $this->SALES->get_sales($filters, 100, 0);
		$this->render('admin/content/sales/records', 'Sales Records', $data);
	}

	public function print_invoices()
	{
		$this->records();
	}

	public function detail($sale_id)
	{
		$this->ensure_sales_tables();
		$data['sale'] = $this->SALES->get_sale($sale_id);
		if (!$data['sale']) {
			show_404();
		}
		$data['items'] = $this->SALES->get_sale_items($sale_id);
		$this->render('admin/content/sales/detail', 'Invoice Details', $data);
	}

	public function invoice_pdf($sale_id)
	{
		$this->ensure_sales_tables();
		$data['sale'] = $this->SALES->get_sale($sale_id);
		if (!$data['sale']) {
			show_404();
		}
		$data['items'] = $this->SALES->get_sale_items($sale_id);
		$data['web'] = $this->ADM->identitaswebsite();

		$this->load->library('dompdf_gen');
		$html = $this->load->view('admin/content/sales/pdf/invoice', $data, TRUE);
		$this->dompdf_gen->set_paper('A4', 'portrait');
		$this->dompdf_gen->load_html($html);
		$this->dompdf_gen->render();
		$this->dompdf_gen->stream($data['sale']->invoice_number.'.pdf', array('Attachment' => 1));
	}

	public function cancel($sale_id)
	{
		$admin = $this->require_login();
		$this->ensure_sales_tables();
		if ((string) $admin->admin_level_kode !== '1') {
			$this->session->set_flashdata('error', 'Only administrators can cancel sales.');
			redirect('sales/records');
		}
		$result = $this->SALES->cancel_sale($sale_id, $admin->admin_user);
		$this->session->set_flashdata($result['success'] ? 'success' : 'error', $result['message']);
		redirect('sales/records');
	}

	public function report()
	{
		$this->ensure_sales_tables();
		$range = $this->report_range();
		$data = $this->report_data($range['from'], $range['to']);
		$data['range'] = $range;
		$this->render('admin/content/sales/report', 'Sales Report', $data);
	}

	public function report_pdf()
	{
		$this->ensure_sales_tables();
		$range = $this->report_range();
		$data = $this->report_data($range['from'], $range['to']);
		$data['range'] = $range;
		$data['web'] = $this->ADM->identitaswebsite();

		$this->load->library('dompdf_gen');
		$html = $this->load->view('admin/content/sales/pdf/report', $data, TRUE);
		$this->dompdf_gen->set_paper('A4', 'portrait');
		$this->dompdf_gen->load_html($html);
		$this->dompdf_gen->render();
		$this->dompdf_gen->stream('sales-report.pdf', array('Attachment' => 1));
	}

	private function report_range()
	{
		$type = $this->input->get('range', TRUE);
		$type = $type ? $type : 'daily';
		$today = date('Y-m-d');

		if ($type === 'weekly') {
			return array('type' => $type, 'from' => date('Y-m-d', strtotime('monday this week')), 'to' => date('Y-m-d', strtotime('sunday this week')));
		}
		if ($type === 'monthly') {
			return array('type' => $type, 'from' => date('Y-m-01'), 'to' => date('Y-m-t'));
		}
		if ($type === 'custom') {
			$from = $this->input->get('date_from', TRUE);
			$to = $this->input->get('date_to', TRUE);
			return array('type' => $type, 'from' => $from ? $from : $today, 'to' => $to ? $to : $today);
		}

		return array('type' => 'daily', 'from' => $today, 'to' => $today);
	}

	private function report_data($from, $to)
	{
		return array(
			'summary' => $this->SALES->report_summary($from, $to),
			'best_products' => $this->SALES->best_selling_products($from, $to),
			'top_customers' => $this->SALES->top_customers($from, $to),
		);
	}

	public function discounts()
	{
		$this->manage_simple('discounts', 'discount_id', 'admin/content/sales/discounts', 'Discounts');
	}

	public function taxes()
	{
		$this->manage_simple('taxes', 'tax_id', 'admin/content/sales/taxes', 'Tax Management');
	}

	public function units()
	{
		$this->manage_simple('units', 'unit_id', 'admin/content/sales/units', 'Unit Management');
	}

	private function manage_simple($table, $primary_key, $view, $breadcrumb)
	{
		$this->ensure_sales_tables();
		$action = $this->uri->segment(3) ? $this->uri->segment(3) : 'view';
		$id = (int) $this->uri->segment(4);

		if ($this->input->post('simpan')) {
			$data = $this->simple_post_data($table);
			$this->SALES->save_simple($table, $data, $primary_key, $id);
			$this->session->set_flashdata('success', $breadcrumb.' saved successfully.');
			redirect('sales/'.$this->uri->segment(2));
		}

		if ($action === 'hapus' && $id > 0) {
			$this->SALES->delete_simple($table, $primary_key, $id);
			$this->session->set_flashdata('success', $breadcrumb.' deleted successfully.');
			redirect('sales/'.$this->uri->segment(2));
		}

		$data['action'] = $action;
		$data['row'] = $id ? $this->SALES->get_simple($table, $primary_key, $id) : NULL;
		$data['rows'] = $this->db->order_by($primary_key, 'DESC')->get($table)->result();
		$this->render($view, $breadcrumb, $data);
	}

	private function simple_post_data($table)
	{
		if ($table === 'discounts') {
			return array(
				'discount_name' => validasi_sql($this->input->post('discount_name')),
				'discount_type' => $this->input->post('discount_type') === 'fixed' ? 'fixed' : 'percentage',
				'discount_value' => max(0, (float) $this->input->post('discount_value')),
				'status' => $this->input->post('status') === 'inactive' ? 'inactive' : 'active',
			);
		}
		if ($table === 'taxes') {
			return array(
				'tax_name' => validasi_sql($this->input->post('tax_name')),
				'tax_rate' => max(0, (float) $this->input->post('tax_rate')),
				'status' => $this->input->post('status') === 'inactive' ? 'inactive' : 'active',
			);
		}

		return array(
			'unit_name' => validasi_sql($this->input->post('unit_name')),
			'status' => $this->input->post('status') === 'inactive' ? 'inactive' : 'active',
		);
	}
}
