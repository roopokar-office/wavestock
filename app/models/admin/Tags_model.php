<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Tags_model  extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
    public function addTag($data) {
        if ($this->db->insert("tags", $data)) {
            return true;
        }
        return false;
    }

    public function addBrands($data) {
        if ($this->db->insert_batch('brands', $data)) {
            return true;
        }
        return false;
    }

    public function updateTag($id, $data = array()) {
        if ($this->db->update("tags", $data, array('id' => $id))) {
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

    public function deleteTag($id) {
        if ($this->db->delete("tags", array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function getTagByID($id) {
        $q = $this->db->get_where('tags', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    // Get the selected tags by the product id
    public function getSelectedTags($id) {
        $query = $this->db->from('sma_tags_products')
                ->where(array('product_id' => $id))
                ->get();
        if($query->num_rows() > 0){
            return $query->result();
        }else{
            return false;
        }
    }

    // Add tags by product id
    public function addTagsByProduct($product_id, $tag_ids){
        $response = false;
        foreach ($tag_ids as $tag_id){
            $data = array(
                'tag_id' => $tag_id,
                'product_id' => $product_id,
            );
            if ($this->db->insert("tags_products", $data)) {
                $response = true;
            } else {
                $response = false;
            }
        }
        return $response;
    }

    // Update tags by product id
    public function updateTagsByProduct($product_id, $tag_ids){
        $response = false;
        if ($this->db->delete("tags_products", array('product_id' => $product_id))) {
            $response = true;
            foreach ($tag_ids as $tag_id){
                $data = array(
                    'tag_id' => $tag_id,
                    'product_id' => $product_id,
                );
                if ($this->db->insert("tags_products", $data)) {
                    $response = true;
                } else {
                    $response = false;
                }
            }
        }
        return $response;
    }

}