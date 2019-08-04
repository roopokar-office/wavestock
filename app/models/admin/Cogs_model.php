<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Cogs_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getProductNames($term, $warehouse_id, $limit = 5) {
        $this->db->select('products.*, warehouses_products.quantity')
            ->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left')
            ->group_by('products.id');

        $this->db->where("(name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR  concat(name, ' (', code, ')') LIKE '%" . $term . "%')");

        $this->db->limit($limit);
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    function lastquery() {
        return "query: " . $this->db->last_query();
    }

    public function getAllPurchaseItems() {
        $query = $this->db->get("purchase_items");
        $this->db->save_queries = TRUE;
        $str = $this->db->last_query();
        if ($query->num_rows() > 0) {
            echo "Query: ";
            echo "<pre>";
            // print_r($query->result());
            echo $str;
            echo "</pre>";
            exit;
        }

    }

    public function getRemainingQty($filter = NULL) {
        $product_purchase = "( SELECT product_id, product_name, quantity, SUM(quantity) AS total_purchase_qty, SUM(real_unit_cost*quantity) AS total_cost FROM {$this->db->dbprefix('purchase_items')} pi
            LEFT JOIN {$this->db->dbprefix('purchases')} p on p.id = pi.purchase_id
            WHERE p.status != 'pending' AND p.status != 'ordered' AND transfer_id IS NULL ";
        $product_sale = "( SELECT product_id, product_name, SUM(quantity) AS total_sale_qty, `date` 
             FROM {$this->db->dbprefix('sales')} s JOIN {$this->db->dbprefix('sale_items')} si 
             ON s.id = si.sale_id ";


        $start_date = isset($filter['start_date']) ? $filter['start_date'] : NULL;

        if ($start_date) {
            $product_sale .= " WHERE ";
            $product_purchase .= " AND p.date < '{$start_date}' ";
            $product_sale .= " s.date < '{$start_date}' ";
        }

        $product_purchase .= " GROUP BY pi.product_id ) PPurchase";
        $product_sale .= " GROUP BY si.product_id ) PSales";

        $this->db
            ->select("{$this->db->dbprefix('products')}.id, {$this->db->dbprefix('products')}.name,
                (PPurchase.total_cost/PPurchase.total_purchase_qty) AS avg_cost,
                COALESCE(PPurchase.total_purchase_qty, 0) AS total_purchase_qty,
                IFNULL(PSales.total_sale_qty, 0) AS total_sale_qty,
                (IFNULL(SUM(PPurchase.total_purchase_qty), 0)-IFNULL(PSales.total_sale_qty, 0)) AS remaining_qty", FALSE)
            ->from('products');

        $this->db
            ->join($product_sale, 'products.id = PSales.product_id', 'left')
            ->join($product_purchase, 'products.id = PPurchase.product_id', 'left')
            ->group_by('products.id');

        if (isset($filter['product_id'])){
            $this->db->where('products.id', $filter['product_id']);
        }

        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getPurchaseInDateRange($product_id = NULL, $start_date = NULL, $end_date = NULL) {
        $this->db
            ->select("{$this->db->dbprefix('purchase_items')}.product_id, {$this->db->dbprefix('purchase_items')}.product_name, SUM(quantity) AS total_purchase_qty, SUM(real_unit_cost*quantity) AS total_cost", FALSE)
            ->from('purchase_items');

        if ($start_date) {
            $this->db
                ->where('date >=', $start_date);
        }
        if ($end_date) {
            $this->db
                ->where('date <=', $end_date);
        }
        $this->db
            ->group_by('purchase_items.product_id')
            ->where('purchase_items.product_id', $product_id);

        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->first_row();
        }else {
            return FALSE;
        }
    }

    public function getSellInDateRange($product_id = NULL, $start_date = NULL, $end_date = NULL) {
        $this->db
            ->select("product_id, product_name, SUM(quantity) AS total_sale_qty", FALSE)
            ->from('sales s')
            ->join('sale_items si', 's.id = si.sale_id');

        if ($start_date) {
            $this->db
                ->where('date >=', $start_date);
        }
        if ($end_date) {
            $this->db
                ->where('date <=', $end_date);
        }
        $this->db
            ->group_by('si.product_id')
            ->where('si.product_id', $product_id);

        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->first_row();
        }else {
            return FALSE;
        }
    }

}
