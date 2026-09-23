<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends Api_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('Product_service');
    }

    public function index()
    {
        $this->require_auth();

        $page = max(1, (int) $this->input->get('page'));
        $search = $this->input->get('search');
        $category_id = $this->input->get('category_id');

        $this->json(
            $this->product_service->paginate($page, $search, $category_id),
            200
        );
    }

    public function store()
    {
        if ($this->input->method() !== 'post') {
            $this->json_error('Method Not Allowed.', 405);
            return;
        }

        $this->require_auth();

        $input = $this->json_body();
        $this->form_validation->set_data($input);
        $this->form_validation->set_rules('name', 'Name', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('barcode', 'Barcode', 'trim|max_length[100]');
        $this->form_validation->set_rules('category_id', 'Category', 'required|integer');
        $this->form_validation->set_rules('cost_price', 'Cost price', 'required|numeric|greater_than_equal_to[0]');
        $this->form_validation->set_rules('sell_price', 'Sell price', 'required|numeric|greater_than_equal_to[0]');
        $this->form_validation->set_rules('stock_qty', 'Stock quantity', 'required|integer|greater_than_equal_to[0]');

        if ($this->form_validation->run() === FALSE) {
            $this->json_error(
                'The given data was invalid.',
                422,
                $this->validation_errors()
            );
            return;
        }

        $result = $this->product_service->create($input);

        if ( ! $result['valid']) {
            $this->json_error($result['message'], $result['code']);
            return;
        }

        $this->json($result['product'], 201);
    }

    public function show($id)
    {
        $this->require_auth();

        $product = $this->product_service->get($id);

        if ( ! $product) {
            $this->json_error('Product not found.', 404);
            return;
        }

        $this->json($product, 200);
    }

    public function update($id)
    {
        if ($this->input->method() !== 'put') {
            $this->json_error('Method Not Allowed.', 405);
            return;
        }

        $this->require_auth();

        if ( ! $this->product_service->get($id)) {
            $this->json_error('Product not found.', 404);
            return;
        }

        $input = $this->json_body();
        $this->form_validation->set_data($input);
        $this->set_update_rules($input);

        if ($this->form_validation->run() === FALSE) {
            $this->json_error(
                'The given data was invalid.',
                422,
                $this->validation_errors()
            );
            return;
        }

        $result = $this->product_service->update($id, $input);

        if ( ! $result['valid']) {
            $this->json_error($result['message'], $result['code']);
            return;
        }

        $this->json($result['product'], 200);
    }

    public function destroy($id)
    {
        if ($this->input->method() !== 'delete') {
            $this->json_error('Method Not Allowed.', 405);
            return;
        }

        $this->require_auth();

        if ( ! $this->product_service->get($id)) {
            $this->json_error('Product not found.', 404);
            return;
        }

        $result = $this->product_service->delete($id);

        if ( ! $result['success']) {
            $this->json_error($result['message'], $result['code']);
            return;
        }

        $this->json(['message' => $result['message']], 200);
    }

    protected function set_update_rules($input)
    {
        if (array_key_exists('name', $input)) {
            $this->form_validation->set_rules('name', 'Name', 'required|trim|max_length[255]');
        }

        if (array_key_exists('barcode', $input)) {
            $this->form_validation->set_rules('barcode', 'Barcode', 'trim|max_length[100]');
        }

        if (array_key_exists('category_id', $input)) {
            $this->form_validation->set_rules('category_id', 'Category', 'required|integer');
        }

        if (array_key_exists('cost_price', $input)) {
            $this->form_validation->set_rules('cost_price', 'Cost price', 'required|numeric|greater_than_equal_to[0]');
        }

        if (array_key_exists('sell_price', $input)) {
            $this->form_validation->set_rules('sell_price', 'Sell price', 'required|numeric|greater_than_equal_to[0]');
        }

        if (array_key_exists('stock_qty', $input)) {
            $this->form_validation->set_rules('stock_qty', 'Stock quantity', 'required|integer|greater_than_equal_to[0]');
        }
    }
}
