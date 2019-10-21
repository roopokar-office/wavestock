<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Remaining_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getProductNames($term, $warehouse_id, $limit = 5)
    {
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
        return "query: ".$this->db->last_query();
    }

    public function getAllPurchaseItems()
    {
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

}
