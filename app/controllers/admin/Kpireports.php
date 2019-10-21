<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Kpireports extends MY_Controller {

    function __construct() {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }

        $this->lang->admin_load('reports', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('kpi_model');
        $this->data['pb'] = array(
            'cash' => lang('cash'),
            'CC' => lang('CC'),
            'Cheque' => lang('Cheque'),
            'paypal_pro' => lang('paypal_pro'),
            'stripe' => lang('stripe'),
            'gift_card' => lang('gift_card'),
            'deposit' => lang('deposit'),
            'authorize' => lang('authorize'),
        );

    }

    function index(){
        $this->sma->checkPermissions('products');
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['suppliers'] = $this->kpi_model->getAllSuppliers();
        $filter = array();
        if ($this->input->post()){
            $start_date = $this->input->post('start_date') ? $this->input->post('start_date') : NULL;
            $end_date = $this->input->post('end_date') ? $this->input->post('end_date') : NULL;
            $start_date = $this->sma->fld($this->input->post('start_date'));
            $end_date = $end_date ? $this->sma->fld($this->input->post('end_date')) : date('Y-m-d');
            $filter['start_date'] = $start_date;
            $filter['end_date'] = $end_date;
            $filter['supplier_id'] = $this->input->post('supplier') ? $this->input->post('supplier') : NULL;
        }
        $this->data['all_kpi_reports'] = $this->kpi_model->getKpiReport($filter);
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('KPI_Report')));
        $meta = array('page_title' => lang('KPI_Report'), 'bc' => $bc);
        $this->page_construct('reports/kpi_reports', $meta, $this->data);
    }
}