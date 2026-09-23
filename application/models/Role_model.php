<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Role_model extends MY_Model
{
    protected $table = 'roles';

    public function find($id)
    {
        return $this->db
            ->select('id, name, description, created_at, updated_at')
            ->where('id', (int) $id)
            ->limit(1)
            ->get($this->table)
            ->row();
    }

    public function get_all()
    {
        return $this->db
            ->select('id, name, description, created_at, updated_at')
            ->from($this->table)
            ->order_by('name', 'ASC')
            ->get()
            ->result();
    }

    public function find_by_name($name)
    {
        return $this->db
            ->select('id, name, description, created_at, updated_at')
            ->where('name', trim($name))
            ->limit(1)
            ->get($this->table)
            ->row();
    }

    public function exists($id)
    {
        return $this->db
            ->where('id', (int) $id)
            ->count_all_results($this->table) > 0;
    }
}
