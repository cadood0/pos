<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category_model extends MY_Model
{
    protected $table = 'categories';

    public function all()
    {
        return $this->db
            ->select('categories.id, categories.name, categories.created_at, categories.updated_at, COUNT(products.id) AS products_count')
            ->from($this->table)
            ->join('products', 'products.category_id = categories.id', 'left')
            ->group_by([
                'categories.id',
                'categories.name',
                'categories.created_at',
                'categories.updated_at',
            ])
            ->order_by('categories.name', 'ASC')
            ->get()
            ->result();
    }

    public function find($id)
    {
        return $this->db
            ->select('categories.id, categories.name, categories.created_at, categories.updated_at, COUNT(products.id) AS products_count')
            ->from($this->table)
            ->join('products', 'products.category_id = categories.id', 'left')
            ->where('categories.id', (int) $id)
            ->group_by([
                'categories.id',
                'categories.name',
                'categories.created_at',
                'categories.updated_at',
            ])
            ->get()
            ->row();
    }

    public function find_by_name($name, $ignore_id = NULL)
    {
        $this->db
            ->from($this->table)
            ->where('LOWER(name) = '.$this->db->escape(strtolower(trim($name))), NULL, FALSE);

        if ($ignore_id !== NULL) {
            $this->db->where('id !=', (int) $ignore_id);
        }

        return $this->db->limit(1)->get()->row();
    }

    public function has_products($id)
    {
        return $this->db
            ->from('products')
            ->where('category_id', (int) $id)
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
}
