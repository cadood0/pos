<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AuthService
{
    protected $CI;
    protected $user_model;
    protected $payload_cache = FALSE;
    protected $current_user_cache = FALSE;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('User_model');
        $this->CI->load->library('JwtService');
        $this->user_model = $this->CI->User_model;
    }

    /**
     * @return array{success: bool, message: string, token?: string}
     */
    public function attempt($email, $password, $device_name = NULL)
    {
        $email = $this->normalize_email($email);
        $user = $this->user_model->find_by_email_for_auth($email);

        if ( ! $user || ! password_verify($password, $user->password)) {
            return [
                'success' => FALSE,
                'message' => 'Invalid email or password.',
            ];
        }

        if ((int) $user->status !== 1) {
            return [
                'success' => FALSE,
                'message' => 'Invalid email or password.',
            ];
        }

        return [
            'success' => TRUE,
            'message' => 'Login successful.',
            'token'   => $this->issue_token($user, $device_name),
        ];
    }

    public function issue_token($user, $device_name = NULL)
    {
        $claims = [
            'sub'     => (int) $user->id,
            'role_id' => (int) $user->role_id,
        ];

        if ($device_name !== NULL && trim((string) $device_name) !== '') {
            $claims['device'] = substr(trim((string) $device_name), 0, 100);
        }

        $this->payload_cache = FALSE;
        $this->current_user_cache = FALSE;

        return $this->CI->jwtservice->encode($claims);
    }

    public function login($user, $device_name = NULL)
    {
        return $this->issue_token($user, $device_name);
    }

    public function logout()
    {
        $this->payload_cache = NULL;
        $this->current_user_cache = NULL;
    }

    public function check()
    {
        return $this->payload() !== NULL;
    }

    public function current_user()
    {
        if ($this->current_user_cache !== FALSE) {
            return $this->current_user_cache;
        }

        $payload = $this->payload();

        if ( ! $payload || empty($payload['sub']) || $payload['sub'] === 'flash') {
            $this->current_user_cache = NULL;
            return NULL;
        }

        $this->current_user_cache = $this->user_model->find((int) $payload['sub']);

        return $this->current_user_cache;
    }

    public function is_admin()
    {
        return $this->role_name() === 'Admin';
    }

    public function role_name($user = NULL)
    {
        $user = $user ?: $this->current_user();

        if ( ! $user) {
            return NULL;
        }

        $this->CI->load->model('Role_model');
        $role = $this->CI->Role_model->find((int) $user->role_id);

        return $role ? $role->name : NULL;
    }

    public function permissions($user = NULL)
    {
        $role_name = $this->role_name($user);

        if ($role_name === 'Admin') {
            return [
                'report:view',
                'user.manage',
                'category.manage',
                'product.manage',
                'sale.create',
            ];
        }

        if ($role_name === 'Cashier') {
            return [
                'sale.create',
            ];
        }

        return [];
    }

    public function can($permission, $user = NULL)
    {
        return in_array($permission, $this->permissions($user), TRUE);
    }

    public function token()
    {
        $header = $this->CI->input->get_request_header('Authorization', FALSE);

        if (is_string($header) && preg_match('/^Bearer\s+(\S+)/', $header, $matches)) {
            return $matches[1];
        }

        return NULL;
    }

    protected function payload()
    {
        if ($this->payload_cache !== FALSE) {
            return $this->payload_cache;
        }

        $token = $this->token();

        if ( ! $token) {
            $this->payload_cache = NULL;
            return NULL;
        }

        $payload = $this->CI->jwtservice->decode($token);

        if ( ! $payload || empty($payload['sub']) || $payload['sub'] === 'flash') {
            $this->payload_cache = NULL;
            return NULL;
        }

        $this->payload_cache = $payload;

        return $this->payload_cache;
    }

    protected function normalize_email($email)
    {
        return strtolower(trim((string) $email));
    }
}
