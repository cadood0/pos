<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sale_service
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model([
            'Sale_model',
            'Sale_item_model',
            'Product_model',
        ]);
    }

    public function paginate($page = 1)
    {
        $result = $this->CI->Sale_model->paginate($page, 15);
        $result['items'] = array_map(function ($sale) {
            return $this->present($sale);
        }, $result['items']);

        return $result;
    }

    public function get($id)
    {
        $sale = $this->CI->Sale_model->find($id);

        return $sale ? $this->present($sale) : NULL;
    }

    public function checkout(array $payload, $user_id)
    {
        if ( ! empty($payload['customer_id']) && ! $this->CI->Sale_model->customer_exists($payload['customer_id'])) {
            return [
                'success' => FALSE,
                'code'    => 422,
                'message' => 'The selected customer is invalid.',
            ];
        }

        $this->CI->db->trans_begin();

        $productIds = array_map(
            'intval',
            array_column($payload['items'], 'product_id')
        );

        $placeholders = implode(',', array_fill(0, count($productIds), '?'));

        $products = $this->CI->db->query(
            "SELECT * FROM products WHERE id IN ($placeholders) FOR UPDATE",
            $productIds
        )->result();

        $byId = [];
        foreach ($products as $product) {
            $byId[(int) $product->id] = $product;
        }

        $total = 0.0;

        foreach ($payload['items'] as $item) {
            $productId = (int) $item['product_id'];
            $quantity  = (int) $item['quantity'];

            if ( ! isset($byId[$productId])) {
                $this->CI->db->trans_rollback();
                return [
                    'success' => FALSE,
                    'code'    => 422,
                    'message' => 'One of the products in the cart does not exist.',
                ];
            }

            $product = $byId[$productId];

            if ((int) $product->stock_qty < $quantity) {
                $this->CI->db->trans_rollback();
                return [
                    'success' => FALSE,
                    'code'    => 422,
                    'message' => sprintf(
                        'Not enough stock for %s (available: %d).',
                        $product->name,
                        $product->stock_qty
                    ),
                ];
            }

            $total += ((float) $product->sell_price) * $quantity;
        }

        $total = round($total, 2);
        $paid = round((float) $payload['paid_amount'], 2);

        if ($paid < $total) {
            $this->CI->db->trans_rollback();
            return [
                'success' => FALSE,
                'code'    => 422,
                'message' => sprintf(
                    'Paid amount (%.2f) is less than the total (%.2f).',
                    $paid,
                    $total
                ),
            ];
        }

        $sale = [
            'invoice_no'     => $this->invoice_number(),
            'user_id'        => (int) $user_id,
            'customer_id'    => ! empty($payload['customer_id'])
                ? (int) $payload['customer_id']
                : NULL,
            'total_amount'   => $total,
            'paid_amount'    => $paid,
            'payment_method' => ! empty($payload['payment_method'])
                ? $payload['payment_method']
                : 'cash',
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ];

        $this->CI->db->insert('sales', $sale);
        $saleId = $this->CI->db->insert_id();

        foreach ($payload['items'] as $item) {
            $product = $byId[(int) $item['product_id']];
            $quantity = (int) $item['quantity'];
            $unitPrice = (float) $product->sell_price;

            $this->CI->db->insert('sale_items', [
                'sale_id'    => $saleId,
                'product_id' => (int) $product->id,
                'quantity'   => $quantity,
                'unit_price' => $unitPrice,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $this->CI->db
                ->set('stock_qty', 'stock_qty - '.$quantity, FALSE)
                ->where('id', (int) $product->id)
                ->update('products');
        }

        if ($this->CI->db->trans_status() === FALSE) {
            $this->CI->db->trans_rollback();
            return [
                'success' => FALSE,
                'code'    => 500,
                'message' => 'Unable to complete sale.',
            ];
        }

        $this->CI->db->trans_commit();

        return [
            'success' => TRUE,
            'code'    => 201,
            'sale_id' => $saleId,
        ];
    }

    public function present($sale)
    {
        $items = $this->CI->Sale_item_model->for_sale($sale->id);

        return [
            'id'             => (int) $sale->id,
            'invoice_no'     => $sale->invoice_no,
            'cashier'        => $sale->cashier_name,
            'customer'       => $sale->customer_id
                ? [
                    'id'   => (int) $sale->customer_id,
                    'name' => $sale->customer_name,
                ]
                : NULL,
            'items'          => array_map(function ($item) {
                $quantity = (int) $item->quantity;
                $unit_price = (float) $item->unit_price;

                return [
                    'product_id' => (int) $item->product_id,
                    'name'       => $item->product_name,
                    'quantity'   => $quantity,
                    'unit_price' => $unit_price,
                    'line_total' => round($unit_price * $quantity, 2),
                ];
            }, $items),
            'total_amount'   => (float) $sale->total_amount,
            'paid_amount'    => (float) $sale->paid_amount,
            'change'         => round((float) $sale->paid_amount - (float) $sale->total_amount, 2),
            'payment_method' => $sale->payment_method,
            'created_at'     => $this->iso8601($sale->created_at),
        ];
    }

    private function invoice_number()
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $suffix = '';

        for ($i = 0; $i < 6; $i++) {
            $suffix .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }

        return 'INV-'.date('Ymd').'-'.$suffix;
    }

    protected function iso8601($value)
    {
        if ( ! $value) {
            return NULL;
        }

        return date('c', strtotime($value));
    }
}
