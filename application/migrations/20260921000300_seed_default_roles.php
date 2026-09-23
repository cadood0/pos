<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Seed_default_roles extends CI_Migration
{
    public function up()
    {
        $now = date('Y-m-d H:i:s');

        $roles = [
            [
                'name'        => 'Admin',
                'description' => 'Full system access',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'Cashier',
                'description' => 'Sales operations',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ];

        $this->db->insert_batch('roles', $roles);
    }

    public function down()
    {
        $this->db
            ->where_in('name', ['Admin', 'Cashier'])
            ->delete('roles');
    }
}