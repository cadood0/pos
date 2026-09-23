<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Fix_user_integrity_and_seed_admin extends CI_Migration
{
    public function up()
    {
        $this->drop_foreign_key_if_exists('users', 'fk_users_role');
        $this->ensure_primary_key_on_id('users');
        $this->ensure_primary_key_on_id('roles');
        $this->add_unique_key_if_missing('roles', 'name', 'roles_name_unique');
        $this->add_unique_key_if_missing('users', 'email', 'users_email_unique');
        $this->restore_users_role_foreign_key();
        $this->seed_local_admin();
    }

    public function down()
    {
        $this->db
            ->where('email', 'admin@example.com')
            ->delete('users');
    }

    protected function ensure_primary_key_on_id($table)
    {
        $primary_columns = [];
        $indexes = $this->db->query('SHOW INDEX FROM `'.$this->db->escape_str($table).'` WHERE Key_name = "PRIMARY"')->result();

        foreach ($indexes as $index) {
            $primary_columns[] = $index->Column_name;
        }

        if ($primary_columns === ['id']) {
            return;
        }

        $this->db->query(
            'ALTER TABLE `'.$this->db->escape_str($table).'`
             DROP PRIMARY KEY,
             ADD PRIMARY KEY (`id`)'
        );
    }

    protected function add_unique_key_if_missing($table, $column, $index_name)
    {
        $exists = $this->db->query(
            'SHOW INDEX FROM `'.$this->db->escape_str($table).'`
             WHERE Key_name = '.$this->db->escape($index_name)
        )->num_rows() > 0;

        if ($exists) {
            return;
        }

        $this->db->query(
            'ALTER TABLE `'.$this->db->escape_str($table).'`
             ADD UNIQUE KEY `'.$this->db->escape_str($index_name).'` (`'.$this->db->escape_str($column).'`)'
        );
    }

    protected function drop_foreign_key_if_exists($table, $constraint)
    {
        $fk = $this->db->query(
            "SELECT CONSTRAINT_NAME
             FROM information_schema.TABLE_CONSTRAINTS
             WHERE CONSTRAINT_SCHEMA = DATABASE()
               AND TABLE_NAME = ".$this->db->escape($table)."
               AND CONSTRAINT_TYPE = 'FOREIGN KEY'
               AND CONSTRAINT_NAME = ".$this->db->escape($constraint)
        )->row();

        if ( ! $fk) {
            return;
        }

        $this->db->query(
            'ALTER TABLE `'.$this->db->escape_str($table).'`
             DROP FOREIGN KEY `'.$this->db->escape_str($constraint).'`'
        );
    }

    protected function restore_users_role_foreign_key()
    {
        $fk = $this->db->query(
            "SELECT CONSTRAINT_NAME
             FROM information_schema.TABLE_CONSTRAINTS
             WHERE CONSTRAINT_SCHEMA = DATABASE()
               AND TABLE_NAME = 'users'
               AND CONSTRAINT_TYPE = 'FOREIGN KEY'
               AND CONSTRAINT_NAME = 'fk_users_role'"
        )->row();

        if ($fk) {
            return;
        }

        $this->db->query(
            'ALTER TABLE users
             ADD CONSTRAINT fk_users_role
             FOREIGN KEY (role_id)
             REFERENCES roles(id)
             ON UPDATE CASCADE
             ON DELETE RESTRICT'
        );
    }

    protected function seed_local_admin()
    {
        if ($this->db->where('email', 'admin@example.com')->count_all_results('users') > 0) {
            return;
        }

        $admin_role = $this->db->where('name', 'Admin')->get('roles')->row();

        if ( ! $admin_role) {
            return;
        }

        $now = date('Y-m-d H:i:s');

        $this->db->insert('users', [
            'role_id'    => (int) $admin_role->id,
            'name'       => 'Administrator',
            'email'      => 'admin@example.com',
            'password'   => password_hash('ChangeMe123!', PASSWORD_DEFAULT),
            'status'     => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
