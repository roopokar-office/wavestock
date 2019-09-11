<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Trend_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getCustomerById($customer_id) {
        $q = $this->db->get_where('companies', array('id' => $customer_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

}