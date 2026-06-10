<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('M_admin', 'ADM', TRUE);
		$this->load->model('M_config', 'CONF', TRUE);
		$this->load->model('M_sales', 'SALES', TRUE);
	}
	
	public function index()
	{
		if($this->session->userdata('logged_in') == TRUE){
			$where_admin['admin_user']		= $this->session->userdata('admin_user');
			$data['admin']					= $this->ADM->get_admin('',$where_admin);
			$data['web']					= $this->ADM->identitaswebsite();
			$data['dashboard_info']			= TRUE;
			$data['breadcrumb']				= 'Dashboard';
			$data['dashboard']				= 'admin/dashboard/statistik';
			$data['content']				= 'admin/dashboard/statistik';
			$transaction_counts				= $this->ADM->dashboard_transaction_counts();
			$master_counts					= $this->ADM->dashboard_master_counts();
			$data['jml_data_transaksi_masuk']	= $transaction_counts['masuk'];
			$data['jml_data_transaksi_keluar']	= $transaction_counts['keluar'];
			$data['jml_data_supplier']			= $master_counts['suppliers'];
			$data['jml_data_customer']			= $master_counts['customers'];
			$data['sales_dashboard']				= $this->SALES->dashboard_sales();
			$data['menu_terpilih']			= '1';
			$data['submenu_terpilih']		= '1';
			$this->load->vars($data);
			$this->load->view('admin/home');
		} else {
			redirect("login");
		}
	 }
}
