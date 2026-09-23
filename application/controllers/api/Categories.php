<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Categories extends Api_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('Category_service');
    }

    public function index()
    {
        $this->require_auth();

        $this->json_ok(
            $this->category_service->all(),
            'Categories loaded.'
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
        $this->form_validation->set_rules('name', 'Name', 'required|trim|max_length[100]');

        if ($this->form_validation->run() === FALSE) {
            $this->json_error(
                'The given data was invalid.',
                422,
                $this->validation_errors()
            );
            return;
        }

        if ($this->category_service->get_by_name($input['name'])) {
            $this->json_error('This category already exists.', 422);
            return;
        }

        $this->json_ok(
            $this->category_service->create($input),
            'Category created.',
            201
        );
    }

    public function show($id)
    {
        $this->require_auth();

        $category = $this->category_service->get($id);

        if ( ! $category) {
            $this->json_error('Category not found.', 404);
            return;
        }

        $this->json_ok($category, 'Category loaded.');
    }

    public function update($id)
    {
        if ($this->input->method() !== 'put') {
            $this->json_error('Method Not Allowed.', 405);
            return;
        }

        $this->require_auth();

        $input = $this->json_body();
        $this->form_validation->set_data($input);
        $this->form_validation->set_rules('name', 'Name', 'required|trim|max_length[100]');

        if ($this->form_validation->run() === FALSE) {
            $this->json_error(
                'The given data was invalid.',
                422,
                $this->validation_errors()
            );
            return;
        }

        if ( ! $this->category_service->get($id)) {
            $this->json_error('Category not found.', 404);
            return;
        }

        if ($this->category_service->get_by_name($input['name'], $id)) {
            $this->json_error('This category already exists.', 422);
            return;
        }

        $this->json_ok(
            $this->category_service->update($id, $input),
            'Category updated.'
        );
    }

    public function destroy($id)
    {
        if ($this->input->method() !== 'delete') {
            $this->json_error('Method Not Allowed.', 405);
            return;
        }

        $this->require_auth();

        if ( ! $this->category_service->get($id)) {
            $this->json_error('Category not found.', 404);
            return;
        }

        $result = $this->category_service->delete($id);

        if ( ! $result['success']) {
            $this->json_error($result['message'], $result['code']);
            return;
        }

        $this->json_ok([], 'Category deleted.');
    }
}
