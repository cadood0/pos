<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends Api_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Role_model');
        $this->load->library('UserService');
    }

    public function register()
    {
        if ($this->input->method() !== 'post') {
            $this->json_error('Method Not Allowed.', 405);
            return;
        }

        $input = $this->json_body();
        $this->form_validation->set_data($input);
        $this->form_validation->set_rules('name', 'Name', 'required|trim|min_length[2]|max_length[100]');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|max_length[150]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]|max_length[255]');
        $this->form_validation->set_rules('device_name', 'Device name', 'trim|max_length[100]');

        if ($this->form_validation->run() === FALSE) {
            $this->json_error(strip_tags(validation_errors()), 422);
            return;
        }

        $cashier = $this->Role_model->find_by_name('Cashier');

        if ( ! $cashier) {
            $this->json_error('Unable to register user.', 500);
            return;
        }

        try {
            $user_id = $this->userservice->create([
                'name'     => $input['name'],
                'email'    => $input['email'],
                'password' => $input['password'],
                'role_id'  => (int) $cashier->id,
                'status'   => 1,
            ]);
        } catch (RuntimeException $e) {
            $this->json_error($e->getMessage(), 422);
            return;
        }

        $user = $this->User_model->find($user_id);

        $this->json_ok([
            'token' => $this->authservice->issue_token(
                $user,
                isset($input['device_name']) ? $input['device_name'] : NULL
            ),
            'user'  => $this->user_payload($user),
        ], 'Registration successful.', 201);
    }

    public function login()
    {
        if ($this->input->method() !== 'post') {
            $this->json_error('Method Not Allowed.', 405);
            return;
        }

        $input = $this->json_body();
        $this->form_validation->set_data($input);
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');
        $this->form_validation->set_rules('device_name', 'Device name', 'trim|max_length[100]');

        if ($this->form_validation->run() === FALSE) {
            $this->json_error(strip_tags(validation_errors()), 422);
            return;
        }

        $result = $this->authservice->attempt(
            $input['email'],
            $input['password'],
            isset($input['device_name']) ? $input['device_name'] : NULL
        );

        if ( ! $result['success']) {
            $this->json_error($result['message'], 401);
            return;
        }

        $user = $this->User_model->find_by_email_for_auth(strtolower(trim($input['email'])));

        $this->json_ok([
            'token' => $result['token'],
            'user'  => $this->user_payload($this->User_model->find((int) $user->id)),
        ], $result['message']);
    }

    public function me()
    {
        if ($this->input->method() !== 'get') {
            $this->json_error('Method Not Allowed.', 405);
            return;
        }

        $this->require_auth();

        $this->json_ok($this->user_payload($this->current_user), 'User loaded.');
    }

    public function logout()
    {
        if ($this->input->method() !== 'post') {
            $this->json_error('Method Not Allowed.', 405);
            return;
        }

        $this->require_auth();
        $this->authservice->logout();

        $this->json_ok(NULL, 'You have been logged out.');
    }

    protected function user_payload($user)
    {
        if ( ! $user) {
            return NULL;
        }

        return [
            'id'          => (int) $user->id,
            'name'        => $user->name,
            'email'       => $user->email,
            'status'      => (int) $user->status,
            'role'        => $this->authservice->role_name($user),
            'permissions' => $this->authservice->permissions($user),
        ];
    }
}
