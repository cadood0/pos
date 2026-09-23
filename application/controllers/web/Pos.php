<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pos extends MY_Controller
{
    public function index()
    {
        $this->render('pos/index', 'POS');
    }

    public function history()
    {
        $this->render('pos/history', 'Sale history');
    }

    public function receipt($id)
    {
        $this->render('pos/receipt', 'Receipt', ['sale_id' => (int) $id]);
    }

    protected function render($content, $title, $data = [])
    {
        $this->load->view('layouts/admin', array_merge($data, [
            'title'   => $title,
            'content' => $content,
            'scripts' => ['assets/js/pos.js'],
        ]));
    }
}
