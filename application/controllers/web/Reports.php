<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends MY_Controller
{
    public function index()
    {
        $this->load->view('layouts/admin', [
            'title'   => 'Reports',
            'content' => 'reports/index',
            'scripts' => ['assets/js/reports.js'],
        ]);
    }
}
