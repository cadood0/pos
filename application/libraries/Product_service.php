<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_service
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('Product_model');
        $this->CI->load->model('Category_model');
    }

    public function paginate($page = 1, $search = NULL, $category_id = NULL)
    {
        $result = $this->CI->Product_model->paginate($page, 15, $search, $category_id);
        $result['items'] = array_map([$this, 'present'], $result['items']);

        return $result;
    }

    public function get($id)
    {
        $product = $this->CI->Product_model->find($id);

        return $product ? $this->present($product) : NULL;
    }

    public function create($data)
    {
        $category = $this->CI->Category_model->find($data['category_id']);

        if ( ! $category) {
            return [
                'valid'   => FALSE,
                'code'    => 422,
                'message' => 'The selected category is invalid.',
            ];
        }

        $barcode = $this->normalize_barcode(isset($data['barcode']) ? $data['barcode'] : NULL);

        if ($this->CI->Product_model->barcode_exists($barcode)) {
            return [
                'valid'   => FALSE,
                'code'    => 422,
                'message' => 'This barcode already exists.',
            ];
        }

        if ((float) $data['sell_price'] < (float) $data['cost_price']) {
            return [
                'valid'   => FALSE,
                'code'    => 422,
                'message' => 'Sell price cannot be lower than cost price.',
            ];
        }

        $product = $this->CI->Product_model->create([
            'name'        => trim($data['name']),
            'barcode'     => $barcode,
            'category_id' => (int) $data['category_id'],
            'cost_price'  => $data['cost_price'],
            'sell_price'  => $data['sell_price'],
            'stock_qty'   => (int) $data['stock_qty'],
        ]);

        return [
            'valid'   => TRUE,
            'product' => $this->present($product),
        ];
    }

    public function update($id, $data)
    {
        $payload = $this->build_update_payload($data);

        if (empty($payload)) {
            return [
                'valid'   => FALSE,
                'code'    => 422,
                'message' => 'The given data was invalid.',
            ];
        }

        if (isset($payload['category_id']) && ! $this->CI->Category_model->find($payload['category_id'])) {
            return [
                'valid'   => FALSE,
                'code'    => 422,
                'message' => 'The selected category is invalid.',
            ];
        }

        if (array_key_exists('barcode', $payload) && $this->CI->Product_model->barcode_exists($payload['barcode'], $id)) {
            return [
                'valid'   => FALSE,
                'code'    => 422,
                'message' => 'This barcode already exists.',
            ];
        }

        $this->CI->Product_model->update_by_id($id, $payload);

        return [
            'valid'   => TRUE,
            'product' => $this->get($id),
        ];
    }

    public function delete($id)
    {
        if ($this->CI->Product_model->has_sales_history($id)) {
            return [
                'success' => FALSE,
                'code'    => 409,
                'message' => 'This product cannot be deleted because it has sales history.',
            ];
        }

        return [
            'success' => (bool) $this->CI->Product_model->delete_by_id($id),
            'code'    => 200,
            'message' => 'Product deleted successfully.',
        ];
    }

    public function present($product)
    {
        if ( ! $product) {
            return NULL;
        }

        return [
            'id'         => (int) $product->id,
            'name'       => $product->name,
            'barcode'    => $product->barcode,
            'category'   => [
                'id'   => (int) $product->category_id,
                'name' => $product->category_name,
            ],
            'cost_price' => (float) $product->cost_price,
            'sell_price' => (float) $product->sell_price,
            'stock_qty'  => (int) $product->stock_qty,
            'created_at' => $this->iso8601($product->created_at),
        ];
    }

    protected function build_update_payload($data)
    {
        $payload = [];

        if (array_key_exists('name', $data)) {
            $payload['name'] = trim($data['name']);
        }

        if (array_key_exists('barcode', $data)) {
            $payload['barcode'] = $this->normalize_barcode($data['barcode']);
        }

        if (array_key_exists('category_id', $data)) {
            $payload['category_id'] = (int) $data['category_id'];
        }

        if (array_key_exists('cost_price', $data)) {
            $payload['cost_price'] = $data['cost_price'];
        }

        if (array_key_exists('sell_price', $data)) {
            $payload['sell_price'] = $data['sell_price'];
        }

        if (array_key_exists('stock_qty', $data)) {
            $payload['stock_qty'] = (int) $data['stock_qty'];
        }

        return $payload;
    }

    protected function normalize_barcode($barcode)
    {
        if ($barcode === NULL || trim((string) $barcode) === '') {
            return NULL;
        }

        return trim((string) $barcode);
    }

    protected function iso8601($value)
    {
        if ( ! $value) {
            return NULL;
        }

        return date('c', strtotime($value));
    }
}
