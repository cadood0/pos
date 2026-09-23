<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_categories extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => TRUE,
                'auto_increment' => TRUE,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE,
            ],
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('categories', TRUE);

        $this->db->query(
            'ALTER TABLE `categories`
             ADD UNIQUE KEY `categories_name_unique` (`name`)'
        );
    }

    public function down()
    {
        $this->dbforge->drop_table('categories', TRUE);
    }
}
