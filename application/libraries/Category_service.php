<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category_service
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('Category_model');
    }

    public function all()
    {
        return $this->CI->Category_model->all();
    }

    public function get($id)
    {
        return $this->CI->Category_model->find($id);
    }

    public function get_by_name($name, $ignore_id = NULL)
    {
        return $this->CI->Category_model->find_by_name($name, $ignore_id);
    }

    public function create($data)
    {
        return $this->CI->Category_model->create([
            'name' => trim($data['name']),
        ]);
    }

    public function update($id, $data)
    {
        if ( ! $this->CI->Category_model->update_by_id($id, [
            'name' => trim($data['name']),
        ])) {
            return FALSE;
        }

        return $this->get($id);
    }

    public function delete($id)
    {
        if ($this->CI->Category_model->has_products($id)) {
            return [
                'success' => FALSE,
                'code'    => 409,
                'message' => 'This category cannot be deleted because it still has products.',
            ];
        }

        return [
            'success' => (bool) $this->CI->Category_model->delete_by_id($id),
            'code'    => 200,
            'message' => 'Category deleted.',
        ];
    }
}
