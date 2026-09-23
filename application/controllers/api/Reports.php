<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends Api_Controller
{
    public function summary()
    {
        if ($this->input->method() !== 'get') {
            $this->json_error('Method Not Allowed.', 405);
            return;
        }

        $this->require_auth();

        if ( ! $this->authservice->can('report:view')) {
            $this->json_error('Reports unavailable for your role.', 403);
            return;
        }

        $this->json_ok([
            'sales_count' => 0,
            'revenue'     => 0,
            'items_sold'  => 0,
            'profit'      => 0,
        ]);
    }
}
