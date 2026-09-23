<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends MY_Controller
{
    public function index()
    {
        $this->render('products/index', 'Products');
    }

    public function create()
    {
        $this->render('products/create', 'Add product');
    }

    public function edit($id)
    {
        $this->render('products/edit', 'Edit product', ['product_id' => (int) $id]);
    }

    protected function render($content, $title, $data = [])
    {
        $this->load->view('layouts/admin', array_merge($data, [
            'title'   => $title,
            'content' => $content,
            'scripts' => ['assets/js/products.js'],
        ]));
    }
}
