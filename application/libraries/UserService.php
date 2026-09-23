<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserService
{
    protected $CI;
    protected $user_model;
    protected $role_model;

    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->model('User_model');
        $this->CI->load->model('Role_model');

        $this->user_model = $this->CI->User_model;
        $this->role_model = $this->CI->Role_model;
    }

    public function all()
    {
        return array_map([$this, 'present'], $this->user_model->get_all());
    }

    public function get($id)
    {
        $user = $this->user_model->find($id);

        return $user ? $this->present($user) : NULL;
    }

    public function roles()
    {
        $roles = $this->role_model->get_all();

        return array_map(function ($role) {
            return [
                'id'          => (int) $role->id,
                'name'        => $role->name,
                'description' => $role->description,
            ];
        }, $roles);
    }

    public function create($data)
    {
        $payload = $this->build_payload($data, TRUE);

        if ($this->user_model->exists_by_email($payload['email'])) {
            throw new RuntimeException('A user with this email already exists.');
        }

        return $this->user_model->create($payload);
    }

    public function update($id, $data, $actor_user_id = NULL)
    {
        $id = (int) $id;
        $target = $this->user_model->find($id);

        if ( ! $target) {
            throw new RuntimeException('User not found.');
        }

        $payload = $this->build_payload($data, FALSE);

        if ($this->user_model->exists_by_email($payload['email'], $id)) {
            throw new RuntimeException('A user with this email already exists.');
        }

        $this->assert_status_change_allowed($target, (int) $payload['status'], $actor_user_id);
        $this->assert_role_change_allowed($target, (int) $payload['role_id']);

        return $this->user_model->update_by_id($id, $payload);
    }

    public function delete($id, $actor_user_id)
    {
        $target = $this->user_model->find((int) $id);

        if ( ! $target) {
            return [
                'success' => FALSE,
                'code'    => 404,
                'message' => 'User not found.',
            ];
        }

        if ((int) $target->id === (int) $actor_user_id) {
            return [
                'success' => FALSE,
                'code'    => 422,
                'message' => 'You cannot delete your own account.',
            ];
        }

        if ($this->is_admin_user($target) && (int) $target->status === 1 && $this->user_model->count_active_admins() <= 1) {
            return [
                'success' => FALSE,
                'code'    => 422,
                'message' => 'The last active administrator cannot be deleted.',
            ];
        }

        if ($this->user_model->has_sales_history($id)) {
            return [
                'success' => FALSE,
                'code'    => 409,
                'message' => 'This user cannot be deleted because they have sales history.',
            ];
        }

        return [
            'success' => (bool) $this->user_model->delete_by_id($id),
            'code'    => 200,
            'message' => 'User deleted.',
        ];
    }

    public function change_status($target_user_id, $status, $actor_user_id)
    {
        $target = $this->user_model->find((int) $target_user_id);

        if ( ! $target) {
            throw new RuntimeException('User not found.');
        }

        $this->assert_status_change_allowed($target, (int) $status, $actor_user_id);

        return $this->user_model->set_status((int) $target_user_id, (int) $status);
    }

    protected function build_payload($data, $is_create)
    {
        $role_id = (int) $data['role_id'];

        if ( ! $this->role_model->exists($role_id)) {
            throw new RuntimeException('The selected role is invalid.');
        }

        $status = isset($data['status']) ? (int) $data['status'] : 1;

        if ( ! in_array($status, [0, 1], TRUE)) {
            throw new RuntimeException('The selected status is invalid.');
        }

        $payload = [
            'role_id' => $role_id,
            'name'    => trim($data['name']),
            'email'   => $this->normalize_email($data['email']),
            'status'  => $status,
        ];

        if ($is_create || ! empty($data['password'])) {
            $payload['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        return $payload;
    }

    protected function assert_status_change_allowed($target, $status, $actor_user_id)
    {
        if ($status === 1 || (int) $target->status === $status) {
            return;
        }

        if ((int) $target->id === (int) $actor_user_id) {
            throw new RuntimeException('You cannot deactivate your own account.');
        }

        if ($this->is_admin_user($target) && $this->user_model->count_active_admins() <= 1) {
            throw new RuntimeException('The last active administrator cannot be deactivated.');
        }
    }

    protected function assert_role_change_allowed($target, $new_role_id)
    {
        if ((int) $target->role_id === $new_role_id) {
            return;
        }

        if ( ! $this->is_admin_user($target)) {
            return;
        }

        $new_role = $this->role_model->find($new_role_id);

        if ($new_role && $new_role->name === 'Admin') {
            return;
        }

        if ((int) $target->status === 1 && $this->user_model->count_active_admins() <= 1) {
            throw new RuntimeException('The last active administrator cannot be demoted.');
        }
    }

    protected function is_admin_user($user)
    {
        $role = $this->role_model->find((int) $user->role_id);

        return $role && $role->name === 'Admin';
    }

    public function present($user)
    {
        if ( ! $user) {
            return NULL;
        }

        $role_name = isset($user->role_name) ? $user->role_name : NULL;

        if ($role_name === NULL) {
            $role = $this->role_model->find((int) $user->role_id);
            $role_name = $role ? $role->name : NULL;
        }

        return [
            'id'         => (int) $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'status'     => (int) $user->status,
            'role_id'    => (int) $user->role_id,
            'role'       => $role_name,
            'created_at' => $user->created_at,
        ];
    }

    protected function normalize_email($email)
    {
        return strtolower(trim((string) $email));
    }
}
