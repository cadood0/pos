<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales extends Api_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('Sale_service');
    }

    public function index()
    {
        $this->require_auth();

        $page = max(1, (int) $this->input->get('page'));

        $this->json(
            $this->sale_service->paginate($page),
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
        $errors = $this->validate_checkout($input);

        if ( ! empty($errors)) {
            $this->json_error('The given data was invalid.', 422, $errors);
            return;
        }

        $result = $this->sale_service->checkout($input, (int) $this->current_user->id);

        if ( ! $result['success']) {
            $this->json_error($result['message'], $result['code']);
            return;
        }

        $this->json(
            $this->sale_service->get($result['sale_id']),
            201
        );
    }

    public function show($id)
    {
        $this->require_auth();

        $sale = $this->sale_service->get($id);

        if ( ! $sale) {
            $this->json_error('Sale not found.', 404);
            return;
        }

        $this->json($sale, 200);
    }

    protected function validate_checkout($input)
    {
        $errors = [];

        if ( ! isset($input['items']) || ! is_array($input['items']) || count($input['items']) < 1) {
            $errors['items'] = 'The items field is required.';
        } else {
            $seen = [];

            foreach ($input['items'] as $index => $item) {
                if ( ! is_array($item)) {
                    $errors['items'] = 'The items field is invalid.';
                    break;
                }

                $product_id = isset($item['product_id']) ? $item['product_id'] : NULL;
                $quantity = isset($item['quantity']) ? $item['quantity'] : NULL;

                if ( ! $this->is_positive_int($product_id)) {
                    $errors['items.'.$index.'.product_id'] = 'The product id field is required.';
                } else {
                    $product_id = (int) $product_id;

                    if (isset($seen[$product_id])) {
                        $errors['items'] = 'Duplicate product IDs are not allowed.';
                    }

                    $seen[$product_id] = TRUE;
                }

                if ( ! $this->is_positive_int($quantity) || (int) $quantity > 1000) {
                    $errors['items.'.$index.'.quantity'] = 'The quantity must be an integer between 1 and 1000.';
                }
            }
        }

        $this->form_validation->set_data($input);
        $this->form_validation->set_rules('paid_amount', 'Paid amount', 'required|numeric|greater_than_equal_to[0]');

        if (array_key_exists('payment_method', $input) && $input['payment_method'] !== NULL && $input['payment_method'] !== '') {
            $this->form_validation->set_rules('payment_method', 'Payment method', 'in_list[cash,mobile]');
        }

        if (array_key_exists('customer_id', $input) && $input['customer_id'] !== NULL && $input['customer_id'] !== '') {
            $this->form_validation->set_rules('customer_id', 'Customer', 'integer');
        }

        if ($this->form_validation->run() === FALSE) {
            $errors = array_merge($errors, $this->validation_errors());
        }

        return $errors;
    }

    protected function is_positive_int($value)
    {
        if (is_int($value)) {
            return $value >= 1;
        }

        return is_string($value) && ctype_digit($value) && (int) $value >= 1;
    }
}
