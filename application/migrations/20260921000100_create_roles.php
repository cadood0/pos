<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_roles extends CI_Migration
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
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => TRUE,
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
        $this->dbforge->create_table('roles', TRUE);

        $this->db->query(
            'ALTER TABLE `roles`
             ADD UNIQUE KEY `roles_name_unique` (`name`)'
        );
    }

    public function down()
    {
        $this->dbforge->drop_table('roles', TRUE);
    }
}