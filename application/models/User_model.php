<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends MY_Model
{
    protected $table = 'users';

    /**
     * Standard user read. Never includes the password hash.
     */
    public function find($id)
    {
        return $this->db
            ->select('id, role_id, name, email, status, created_at, updated_at')
            ->where('id', (int) $id)
            ->limit(1)
            ->get($this->table)
            ->row();
    }

    /**
     * Authentication-only read. Password is included for AuthService.
     */
    public function find_by_email_for_auth($email)
    {
        return $this->db
            ->select('id, role_id, name, email, password, status')
            ->where('email', trim($email))
            ->limit(1)
            ->get($this->table)
            ->row();
    }

    public function get_all()
    {
        return $this->db
            ->select('
                users.id,
                users.role_id,
                users.name,
                users.email,
                users.status,
                roles.name AS role_name,
                users.created_at,
                users.updated_at
            ')
            ->from($this->table)
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->order_by('users.id', 'DESC')
            ->get()
            ->result();
    }

    public function exists_by_email($email, $exclude_id = NULL)
    {
        $this->db
            ->from($this->table)
            ->where('email', trim($email));

        if ($exclude_id !== NULL) {
            $this->db->where('id !=', (int) $exclude_id);
        }

        return $this->db->count_all_results() > 0;
    }

    public function create($data)
    {
        $now = date('Y-m-d H:i:s');

        $data['created_at'] = $now;
        $data['updated_at'] = $now;

        $this->db->insert($this->table, $data);

        return $this->db->insert_id();
    }

    public function update_by_id($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->db
            ->where('id', (int) $id)
            ->update($this->table, $data);
    }

    public function set_status($id, $status)
    {
        return $this->db
            ->where('id', (int) $id)
            ->update($this->table, [
                'status'     => (int) $status,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
    }

    public function count_active_admins()
    {
        return $this->db
            ->from($this->table)
            ->join('roles', 'roles.id = users.role_id')
            ->where('roles.name', 'Admin')
            ->where('users.status', 1)
            ->count_all_results();
    }

    public function has_sales_history($id)
    {
        return $this->db
            ->from('sales')
            ->where('user_id', (int) $id)
            ->count_all_results() > 0;
    }

    public function delete_by_id($id)
    {
        return $this->db
            ->where('id', (int) $id)
            ->delete($this->table);
    }
}
