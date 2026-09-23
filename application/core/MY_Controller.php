<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    protected $current_user = NULL;

    public function __construct()
    {
        parent::__construct();

        $this->load->library('AuthService');
        $this->current_user = $this->authservice->current_user();
    }
}

class Api_Controller extends MY_Controller
{
    protected function json_ok($data = NULL, $message = NULL, $status = 200)
    {
        $body = [];

        if ($message !== NULL) {
            $body['message'] = $message;
        }

        if ($data !== NULL) {
            $body['data'] = $data;
        }

        return $this->json($body, $status);
    }

    protected function json_error($message, $status = 400, $errors = NULL)
    {
        $body = ['message' => $message];

        if ($errors !== NULL) {
            $body['errors'] = $errors;
        }

        return $this->json($body, $status);
    }

    protected function json(array $body, $status)
    {
        $this->output
            ->set_status_header((int) $status)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($body));

        return $this->output;
    }

    protected function json_body()
    {
        $decoded = json_decode($this->input->raw_input_stream, TRUE);

        return is_array($decoded) ? $decoded : [];
    }

    protected function validation_errors()
    {
        return $this->form_validation->error_array();
    }

    protected function require_auth()
    {
        if ( ! $this->authservice->check() || ! $this->current_user || (int) $this->current_user->status !== 1) {
            $this->json_error('Unauthenticated.', 401);
            $this->output->_display();
            exit;
        }
    }
}
