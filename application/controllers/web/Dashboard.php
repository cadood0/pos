<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller
{
    public function index()
    {
        $data = [
            'title'   => 'Dashboard',
            'content' => 'dashboard/index',
        ];

        $this->load->view('layouts/admin', $data);
    }
}
