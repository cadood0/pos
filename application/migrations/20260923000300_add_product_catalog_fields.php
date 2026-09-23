<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_product_catalog_fields extends CI_Migration
{
    public function up()
    {
        $this->dbforge->modify_column('products', [
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
        ]);

        $this->dbforge->add_column('products', [
            'barcode' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => TRUE,
                'after'      => 'name',
            ],
            'cost_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'unsigned'   => TRUE,
                'default'    => 0,
                'after'      => 'barcode',
            ],
            'sell_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'unsigned'   => TRUE,
                'default'    => 0,
                'after'      => 'cost_price',
            ],
            'stock_qty' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => TRUE,
                'default'    => 0,
                'after'      => 'sell_price',
            ],
        ]);

        $this->db->query(
            'ALTER TABLE `products`
             ADD UNIQUE KEY `products_barcode_unique` (`barcode`)'
        );
    }

    public function down()
    {
        $this->db->query(
            'ALTER TABLE `products`
             DROP INDEX `products_barcode_unique`'
        );

        $this->dbforge->drop_column('products', 'barcode');
        $this->dbforge->drop_column('products', 'cost_price');
        $this->dbforge->drop_column('products', 'sell_price');
        $this->dbforge->drop_column('products', 'stock_qty');

        $this->dbforge->modify_column('products', [
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
        ]);
    }
}
