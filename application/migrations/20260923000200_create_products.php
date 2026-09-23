<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_products extends CI_Migration
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
            'category_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => TRUE,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
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
        $this->dbforge->add_key('category_id');
        $this->dbforge->create_table('products', TRUE);

        $this->db->query(
            'ALTER TABLE products
             ADD CONSTRAINT fk_products_category
             FOREIGN KEY (category_id)
             REFERENCES categories(id)
             ON UPDATE CASCADE
             ON DELETE RESTRICT'
        );
    }

    public function down()
    {
        $this->db->query(
            'ALTER TABLE products
             DROP FOREIGN KEY fk_products_category'
        );

        $this->dbforge->drop_table('products', TRUE);
    }
}
