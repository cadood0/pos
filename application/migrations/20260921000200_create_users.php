<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_users extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => TRUE,
                'auto_increment' => TRUE,
            ],
            'role_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => TRUE,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'status' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => TRUE,
                'default'    => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => TRUE,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => TRUE,
            ],
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('role_id');

        $this->dbforge->create_table('users', TRUE);

        $this->db->query(
            'ALTER TABLE `users`
             ADD UNIQUE KEY `users_email_unique` (`email`)'
        );

        $this->db->query(
            'ALTER TABLE users
             ADD CONSTRAINT fk_users_role
             FOREIGN KEY (role_id)
             REFERENCES roles(id)
             ON UPDATE CASCADE
             ON DELETE RESTRICT'
        );
    }

    public function down()
    {
        $this->db->query(
            'ALTER TABLE users
             DROP FOREIGN KEY fk_users_role'
        );

        $this->dbforge->drop_table('users', TRUE);
    }
}