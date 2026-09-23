<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller
{
    public function index()
    {
        $this->render('users/index', 'Users');
    }

    public function create()
    {
        $this->render('users/create', 'Add user');
    }

    protected function render($content, $title)
    {
        $this->load->view('layouts/admin', [
            'title'   => $title,
            'content' => $content,
            'scripts' => ['assets/js/users.js'],
        ]);
    }
}
