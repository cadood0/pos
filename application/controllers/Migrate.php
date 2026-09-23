<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migrate extends CI_Controller
{
    public function index()
    {
        if ( ! is_cli() && ENVIRONMENT !== 'development') {
            show_error('Forbidden', 403);
        }

        $this->load->library('migration');

        if ($this->migration->current() === FALSE) {
            show_error($this->migration->error_string());
        }

        echo 'Database migrated successfully.';
    }
}
