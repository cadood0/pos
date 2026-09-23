<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_customers_and_sales extends CI_Migration
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
                'constraint' => 150,
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => TRUE,
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
        $this->dbforge->create_table('customers', TRUE);

        $this->dbforge->add_field([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => TRUE,
                'auto_increment' => TRUE,
            ],
            'invoice_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 32,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => TRUE,
            ],
            'customer_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => TRUE,
                'null'       => TRUE,
            ],
            'total_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'unsigned'   => TRUE,
                'default'    => 0,
            ],
            'paid_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'unsigned'   => TRUE,
                'default'    => 0,
            ],
            'payment_method' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'cash',
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
        $this->dbforge->add_key('user_id');
        $this->dbforge->add_key('customer_id');
        $this->dbforge->create_table('sales', TRUE);

        $this->db->query(
            'ALTER TABLE `sales`
             ADD UNIQUE KEY `sales_invoice_no_unique` (`invoice_no`)'
        );

        $this->db->query(
            'ALTER TABLE sales
             ADD CONSTRAINT fk_sales_user
             FOREIGN KEY (user_id)
             REFERENCES users(id)
             ON UPDATE CASCADE
             ON DELETE RESTRICT'
        );

        $this->db->query(
            'ALTER TABLE sales
             ADD CONSTRAINT fk_sales_customer
             FOREIGN KEY (customer_id)
             REFERENCES customers(id)
             ON UPDATE CASCADE
             ON DELETE RESTRICT'
        );

        if ($this->db->table_exists('sale_items')) {
            if ($this->db->field_exists('product_id', 'sale_items')) {
                $this->db->query(
                    'ALTER TABLE sale_items
                     DROP FOREIGN KEY fk_sale_items_product'
                );
            }

            $this->dbforge->drop_table('sale_items', TRUE);
        }

        $this->dbforge->add_field([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => TRUE,
                'auto_increment' => TRUE,
            ],
            'sale_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => TRUE,
            ],
            'product_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => TRUE,
            ],
            'quantity' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => TRUE,
            ],
            'unit_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'unsigned'   => TRUE,
                'default'    => 0,
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
        $this->dbforge->add_key('sale_id');
        $this->dbforge->add_key('product_id');
        $this->dbforge->create_table('sale_items', TRUE);

        $this->db->query(
            'ALTER TABLE sale_items
             ADD CONSTRAINT fk_sale_items_sale
             FOREIGN KEY (sale_id)
             REFERENCES sales(id)
             ON UPDATE CASCADE
             ON DELETE RESTRICT'
        );

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
        $this->db->query('ALTER TABLE sale_items DROP FOREIGN KEY fk_sale_items_sale');
        $this->db->query('ALTER TABLE sale_items DROP FOREIGN KEY fk_sale_items_product');
        $this->dbforge->drop_table('sale_items', TRUE);

        $this->db->query('ALTER TABLE sales DROP FOREIGN KEY fk_sales_user');
        $this->db->query('ALTER TABLE sales DROP FOREIGN KEY fk_sales_customer');
        $this->dbforge->drop_table('sales', TRUE);

        $this->dbforge->drop_table('customers', TRUE);
    }
}
