<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends MY_Model
{
    protected $table = 'products';

    public function paginate($page = 1, $per_page = 15, $search = NULL, $category_id = NULL)
    {
        $this->apply_filters($search, $category_id);
        $total = $this->db
            ->from($this->table)
            ->join('categories', 'categories.id = products.category_id')
            ->count_all_results();

        $offset = max(0, ((int) $page - 1) * (int) $per_page);

        $this->apply_filters($search, $category_id);
        $items = $this->db
            ->select('products.*, categories.name AS category_name')
            ->from($this->table)
            ->join('categories', 'categories.id = products.category_id')
            ->order_by('products.name', 'ASC')
            ->limit((int) $per_page, $offset)
            ->get()
            ->result();

        return [
            'items'        => $items,
            'current_page' => (int) $page,
            'per_page'     => (int) $per_page,
            'total'        => (int) $total,
            'last_page'    => max(1, (int) ceil($total / $per_page)),
        ];
    }

    public function find($id)
    {
        return $this->db
            ->select('products.*, categories.name AS category_name')
            ->from($this->table)
            ->join('categories', 'categories.id = products.category_id')
            ->where('products.id', (int) $id)
            ->get()
            ->row();
    }

    public function barcode_exists($barcode, $ignore_id = NULL)
    {
        if ($barcode === NULL || trim($barcode) === '') {
            return FALSE;
        }

        $this->db
            ->from($this->table)
            ->where('barcode', trim($barcode));

        if ($ignore_id !== NULL) {
            $this->db->where('id !=', (int) $ignore_id);
        }

        return $this->db->count_all_results() > 0;
    }

    public function has_sales_history($id)
    {
        return $this->db
            ->from('sale_items')
            ->where('product_id', (int) $id)
            ->count_all_results() > 0;
    }

    public function create($data)
    {
        $now = date('Y-m-d H:i:s');
        $data['created_at'] = $now;
        $data['updated_at'] = $now;

        $this->db->insert($this->table, $data);

        return $this->find($this->db->insert_id());
    }

    public function update_by_id($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->db
            ->where('id', (int) $id)
            ->update($this->table, $data);
    }

    public function delete_by_id($id)
    {
        return $this->db
            ->where('id', (int) $id)
            ->delete($this->table);
    }

    protected function apply_filters($search, $category_id)
    {
        if ($search !== NULL && $search !== '') {
            $this->db
                ->group_start()
                ->like('products.name', $search)
                ->or_where('products.barcode', $search)
                ->group_end();
        }

        if ($category_id !== NULL && $category_id !== '') {
            $this->db->where('products.category_id', (int) $category_id);
        }
    }
}
