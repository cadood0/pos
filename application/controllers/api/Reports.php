<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends Api_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('Report_service');
    }

    public function summary()
    {
        if ($this->input->method() !== 'get') {
            $this->json_error('Method Not Allowed.', 405);
            return;
        }

        $this->require_ability('report:view', 'Reports unavailable for your role.');

        $result = $this->report_service->summary(
            $this->input->get('from'),
            $this->input->get('to')
        );

        if ( ! $result['valid']) {
            $this->json_error($result['message'], 422);
            return;
        }

        $this->json_ok($result['data'], 'Summary loaded.');
    }

    public function sales_by_day()
    {
        if ($this->input->method() !== 'get') {
            $this->json_error('Method Not Allowed.', 405);
            return;
        }

        $this->require_ability('report:view', 'Reports unavailable for your role.');

        $result = $this->report_service->sales_by_day(
            $this->input->get('from'),
            $this->input->get('to')
        );

        if ( ! $result['valid']) {
            $this->json_error($result['message'], 422);
            return;
        }

        $this->json_ok($result['data'], 'Sales by day loaded.');
    }

    public function top_products()
    {
        if ($this->input->method() !== 'get') {
            $this->json_error('Method Not Allowed.', 405);
            return;
        }

        $this->require_ability('report:view', 'Reports unavailable for your role.');

        $result = $this->report_service->top_products(
            $this->input->get('from'),
            $this->input->get('to'),
            $this->input->get('limit')
        );

        if ( ! $result['valid']) {
            $this->json_error($result['message'], 422);
            return;
        }

        $this->json_ok($result['data'], 'Top products loaded.');
    }

    public function low_stock()
    {
        if ($this->input->method() !== 'get') {
            $this->json_error('Method Not Allowed.', 405);
            return;
        }

        $this->require_ability('report:view', 'Reports unavailable for your role.');

        $result = $this->report_service->low_stock(
            $this->input->get('threshold')
        );

        if ( ! $result['valid']) {
            $this->json_error($result['message'], 422);
            return;
        }

        $this->json_ok($result['data'], 'Low stock loaded.');
    }
}
