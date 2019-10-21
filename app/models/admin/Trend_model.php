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

    public function getProductById($product_id) {
        $q = $this->db->get_where('products', array('id' => $product_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getPurchaseReportByProduct($product, $start_date, $end_date) {
        $this->db
            ->select("purchases.date, quantity AS purchased_qty, unit_cost AS unit_purchased_cost, supplier", FALSE)
            ->from('purchases')
            ->join('purchase_items', 'purchase_items.purchase_id=purchases.id', 'left')
            ->join('warehouses', 'warehouses.id=purchases.warehouse_id', 'left')
            ->order_by('purchases.date', 'DESC');

        if ($product) {
            $this->db->where('sma_purchase_items.product_id', $product, FALSE);
        }
        if ($start_date) {
            $this->db->where($this->db->dbprefix('purchases') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        }

        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->result();
        } else {
            return NULL;
        }
    }

    public function getSalesReportByProduct($product, $start_date, $end_date) {
        $this->db
            ->select("sales.date, quantity AS sales_qty, unit_price AS unit_selling_price, customer", FALSE)
            ->from('sales')
            ->join('sale_items', 'sale_items.sale_id=sales.id', 'left')
            ->join('warehouses', 'warehouses.id=sales.warehouse_id', 'left')
            ->order_by('sales.date', 'DESC');

        if ($product) {
            $this->db->where('sma_sale_items.product_id', $product, FALSE);
        }
        if ($start_date) {
            $this->db->where($this->db->dbprefix('sales') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        }

        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->result();
        } else {
            return NULL;
        }
    }

}