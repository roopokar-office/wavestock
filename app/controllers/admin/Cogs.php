<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Cogs extends MY_Controller {
    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }

        $this->lang->admin_load('reports', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('reports_model');
        $this->load->admin_model('cogs_model');
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

    public function index(){
        $filter = array();
        $this->data['start_date'] = $this->data['end_date'] = NULL;
        if ($this->input->post()){
            $filter['product_id'] = $this->input->post('product');
            $start_date = $this->input->post('start_date');
            $start_date = $this->sma->fld($start_date);
            $end_date = $this->input->post('end_date');
            $end_date = $this->sma->fld($end_date);
            $this->data['start_date'] = $filter['start_date'] = $start_date ? $start_date : NULL;
            $this->data['end_date'] = $filter['end_date'] = $end_date ? $end_date : NULL;
        }
        $this->data['remaining_qty'] = $this->cogs_model->getRemainingQty($filter);
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('COGS_Report')));
        $meta = array('page_title' => lang('COGS_Report'), 'bc' => $bc);
        $this->page_construct('reports/cogs', $meta, $this->data);
    }

}