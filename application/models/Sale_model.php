<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sale_model extends MY_Model
{
    protected $table = 'sales';

    public function paginate($page = 1, $per_page = 15)
    {
        $total = $this->db
            ->from($this->table)
            ->count_all_results();

        $offset = max(0, ((int) $page - 1) * (int) $per_page);

        $items = $this->db
            ->select('sales.*, users.name AS cashier_name, customers.name AS customer_name')
            ->from($this->table)
            ->join('users', 'users.id = sales.user_id')
            ->join('customers', 'customers.id = sales.customer_id', 'left')
            ->order_by('sales.id', 'DESC')
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
            ->select('sales.*, users.name AS cashier_name, customers.name AS customer_name')
            ->from($this->table)
            ->join('users', 'users.id = sales.user_id')
            ->join('customers', 'customers.id = sales.customer_id', 'left')
            ->where('sales.id', (int) $id)
            ->get()
            ->row();
    }

    public function customer_exists($id)
    {
        return $this->db
            ->from('customers')
            ->where('id', (int) $id)
            ->count_all_results() > 0;
    }
}
