<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Alerts_model extends CI_Model
{
    public function __construct() {
        parent::__construct();
    }

    public function dueDateAlert(){
        $date = date('Y-m-d', strtotime('+15 days'));
        $this->db->select('COUNT(*) as alert_num')
            ->where('due_date !=', NULL)->where('due_date !=', '0000-00-00')
            ->where('due_date <', $date);
        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            $res = $q->row();
            return (INT) $res->alert_num;
        }
        return FALSE;
    }

    public function get_due_date_qty_alerts(){
        $date = date('Y-m-d', strtotime('+15 days'));
        $this->db->select('COUNT(*) as alert_num')
            ->where('due_date !=', NULL)->where('due_date !=', '0000-00-00')
            ->where('due_date <', $date);
        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            $res = $q->row();
            return (INT) $res->alert_num;
        }
        return FALSE;
    }
}