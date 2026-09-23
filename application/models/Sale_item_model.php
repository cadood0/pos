<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sale_item_model extends MY_Model
{
    protected $table = 'sale_items';

    public function for_sale($sale_id)
    {
        return $this->db
            ->select('sale_items.*, products.name AS product_name')
            ->from($this->table)
            ->join('products', 'products.id = sale_items.product_id')
            ->where('sale_items.sale_id', (int) $sale_id)
            ->order_by('sale_items.id', 'ASC')
            ->get()
            ->result();
    }
}
