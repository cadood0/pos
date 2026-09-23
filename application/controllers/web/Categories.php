<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Categories extends MY_Controller
{
    public function index()
    {
        $this->render('categories/index', 'Categories');
    }

    public function create()
    {
        $this->render('categories/create', 'Add category');
    }

    public function show($id)
    {
        $this->render('categories/show', 'Category', ['category_id' => (int) $id]);
    }

    public function edit($id)
    {
        $this->render('categories/edit', 'Edit category', ['category_id' => (int) $id]);
    }

    protected function render($content, $title, $data = [])
    {
        $this->load->view('layouts/admin', array_merge($data, [
            'title'   => $title,
            'content' => $content,
            'scripts' => ['assets/js/categories.js'],
        ]));
    }
}
