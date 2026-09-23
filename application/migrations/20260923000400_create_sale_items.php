<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_sale_items extends CI_Migration
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
            'product_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => TRUE,
            ],
            'qty' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => TRUE,
                'default'    => 1,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE,
            ],
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('product_id');
        $this->dbforge->create_table('sale_items', TRUE);

        $this->db->query(
            'ALTER TABLE sale_items
             ADD CONSTRAINT fk_sale_items_product
             FOREIGN KEY (product_id)
             REFERENCES products(id)
             ON UPDATE CASCADE
             ON DELETE RESTRICT'
        );
    }

    public function down()
    {
        $this->db->query(
            'ALTER TABLE sale_items
             DROP FOREIGN KEY fk_sale_items_product'
        );

        $this->dbforge->drop_table('sale_items', TRUE);
    }
}
