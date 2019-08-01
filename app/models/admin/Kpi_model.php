<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Kpi_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
    public function addKpi($data) {
        if ($this->db->insert("kpi", $data)) {
            return true;
        }
        return false;
    }

    public function updateKpi($id, $data = array()) {
        if ($this->db->update("kpi", $data, array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function tagHasProducts($tag_id) {
        $q = $this->db->get_where('products', array('tags' => $tag_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllKpi() {
        $q = $this->db->get('kpi');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function deleteKpi($id) {
        if ($this->db->delete("kpi", array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function getKpiValueByID($purchase_id, $kpi_id) {
        $q = $this->db->get_where('kpi_purchases', array('kpi_id' => $kpi_id, 'purchase_id' => $purchase_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    // Get the selected kpi by the purchase id
    public function getKpiValue($id) {
        $query = $this->db->from('sma_kpi_purchases')
                ->where(array('purchase_id' => $id))
                ->get();
        if($query->num_rows() > 0){
            return $query->result();
        }else{
            return false;
        }
    }

    public function getKpiReport($data = NULL){
        $this->db->select("purchases.id as tbl_purchase_id, supplier, reference_no, kpi.name, kpi_purchases.value, FORMAT((AVG(sma_kpi_purchases.value)), 2) as avg_kpi")
                ->from("purchases")
                ->join("kpi_purchases", "kpi_purchases.purchase_id = purchases.id", "left")
                ->join("kpi", "kpi.id = kpi_purchases.kpi_id")
                ->group_by("purchases.id");
        if (isset($data['start_date'])){
            $this->db->where("purchases.date >=", $data['start_date']);
        }
        if (isset($data['end_date'])){
            $this->db->where("purchases.date <=", $data['end_date']);
        }
        if (isset($data['supplier_id'])){
            $this->db->where("purchases.supplier_id =", $data['supplier_id']);
        }
        $query = $this->db->get();
        if($query->num_rows() > 0){
            return $query->result();
        }else{
            return false;
        }
    }

    public function getAllSuppliers(){
        $query = $this->db
                ->select("supplier, supplier_id")
                ->from("purchases")
                ->group_by("supplier_id")
                ->get();
        if($query->num_rows() > 0){
            return $query->result();
        }else{
            return false;
        }
    }

    // Add kpi by product id
    public function addTagsByProduct($product_id, $tag_ids){
        $response = false;
        foreach ($tag_ids as $tag_id){
            $data = array(
                'tag_id' => $tag_id,
                'product_id' => $product_id,
            );
            if ($this->db->insert("kpi_products", $data)) {
                $response = true;
            } else {
                $response = false;
            }
        }
        return $response;
    }

    // Update kpi by product id
    public function updateKpiByPurchase($purchase_id, $kpi_s){
        $response = false;
        if ($this->db->delete("kpi_purchases", array('purchase_id' => $purchase_id))) {
            $response = true;
            foreach ($kpi_s as $kpi){
                $data = array(
                    'kpi_id' => $kpi->kpi_id,
                    'purchase_id' => $kpi->purchase_id,
                    'value' => $kpi->value,
                );
                if ($this->db->insert("kpi_purchases", $data)) {
                    $response = true;
                } else {
                    $response = false;
                }
            }
        }
        return $response;
    }

}