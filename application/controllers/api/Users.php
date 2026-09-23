<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends Api_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('UserService');
    }

    public function index()
    {
        $this->require_ability('user.manage');

        $this->json_ok(
            $this->userservice->all(),
            'Users loaded.'
        );
    }

    public function store()
    {
        if ($this->input->method() !== 'post') {
            $this->json_error('Method Not Allowed.', 405);
            return;
        }

        $this->require_ability('user.manage');

        $input = $this->json_body();
        $this->form_validation->set_data($input);
        $this->form_validation->set_rules('name', 'Name', 'required|trim|min_length[2]|max_length[100]');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|max_length[150]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]|max_length[255]');
        $this->form_validation->set_rules('role_id', 'Role', 'required|integer');
        $this->form_validation->set_rules('status', 'Status', 'integer');

        if ($this->form_validation->run() === FALSE) {
            $this->json_error(
                'The given data was invalid.',
                422,
                $this->validation_errors()
            );
            return;
        }

        if ( ! array_key_exists('status', $input) || $input['status'] === '' || $input['status'] === NULL) {
            $input['status'] = 1;
        }

        try {
            $user_id = $this->userservice->create($input);
        } catch (RuntimeException $e) {
            $this->json_error($e->getMessage(), 422);
            return;
        }

        $this->json_ok(
            $this->userservice->get($user_id),
            'User created.',
            201
        );
    }

    public function show($id)
    {
        $this->require_ability('user.manage');

        $user = $this->userservice->get($id);

        if ( ! $user) {
            $this->json_error('User not found.', 404);
            return;
        }

        $this->json_ok($user, 'User loaded.');
    }

    public function destroy($id)
    {
        if ($this->input->method() !== 'delete') {
            $this->json_error('Method Not Allowed.', 405);
            return;
        }

        $this->require_ability('user.manage');

        $result = $this->userservice->delete($id, (int) $this->current_user->id);

        if ( ! $result['success']) {
            $this->json_error($result['message'], $result['code']);
            return;
        }

        $this->json_ok([], $result['message']);
    }

    public function roles()
    {
        $this->require_ability('user.manage');

        $this->json_ok(
            $this->userservice->roles(),
            'Roles loaded.'
        );
    }
}
