<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_service
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('Report_model');
    }

    public function resolve_range($from = NULL, $to = NULL)
    {
        $from = ($from === NULL || $from === '')
            ? date('Y-m-d', strtotime('-29 days'))
            : trim((string) $from);

        $to = ($to === NULL || $to === '')
            ? date('Y-m-d')
            : trim((string) $to);

        if ( ! $this->is_date($from) || ! $this->is_date($to)) {
            return [
                'valid'   => FALSE,
                'message' => 'The given data was invalid.',
            ];
        }

        if (strtotime($to) < strtotime($from)) {
            return [
                'valid'   => FALSE,
                'message' => 'The end date must be after the start date.',
            ];
        }

        return [
            'valid' => TRUE,
            'from'  => $from.' 00:00:00',
            'to'    => $to.' 23:59:59',
        ];
    }

    public function summary($from = NULL, $to = NULL)
    {
        $range = $this->resolve_range($from, $to);

        if ( ! $range['valid']) {
            return $range;
        }

        $row = $this->CI->Report_model->summary($range['from'], $range['to']);

        return [
            'valid' => TRUE,
            'data'  => [
                'sales_count' => (int) $row->sales_count,
                'revenue'     => round((float) $row->revenue, 2),
                'items_sold'  => (int) $row->items_sold,
                'profit'      => round((float) $row->profit, 2),
            ],
        ];
    }

    public function sales_by_day($from = NULL, $to = NULL)
    {
        $range = $this->resolve_range($from, $to);

        if ( ! $range['valid']) {
            return $range;
        }

        $rows = $this->CI->Report_model->sales_by_day($range['from'], $range['to']);

        return [
            'valid' => TRUE,
            'data'  => array_map(function ($row) {
                return [
                    'date'        => $row->date,
                    'sales_count' => (int) $row->sales_count,
                    'revenue'     => round((float) $row->revenue, 2),
                ];
            }, $rows),
        ];
    }

    public function top_products($from = NULL, $to = NULL, $limit = 10)
    {
        $range = $this->resolve_range($from, $to);

        if ( ! $range['valid']) {
            return $range;
        }

        $limit = (int) $limit;
        if ($limit < 1) {
            $limit = 10;
        }
        if ($limit > 50) {
            $limit = 50;
        }

        $rows = $this->CI->Report_model->top_products($range['from'], $range['to'], $limit);

        return [
            'valid' => TRUE,
            'data'  => array_map(function ($row) {
                return [
                    'product_id' => (int) $row->product_id,
                    'name'       => $row->name,
                    'units_sold' => (int) $row->units_sold,
                    'revenue'    => round((float) $row->revenue, 2),
                ];
            }, $rows),
        ];
    }

    public function low_stock($threshold = 10)
    {
        $threshold = $threshold === NULL || $threshold === '' ? 10 : (int) $threshold;

        if ($threshold < 0) {
            return [
                'valid'   => FALSE,
                'message' => 'The given data was invalid.',
            ];
        }

        $rows = $this->CI->Report_model->low_stock($threshold);

        return [
            'valid' => TRUE,
            'data'  => array_map(function ($row) {
                return [
                    'product_id' => (int) $row->product_id,
                    'name'       => $row->name,
                    'barcode'    => $row->barcode,
                    'stock_qty'  => (int) $row->stock_qty,
                ];
            }, $rows),
        ];
    }

    protected function is_date($value)
    {
        $date = DateTime::createFromFormat('Y-m-d', $value);

        return $date && $date->format('Y-m-d') === $value;
    }
}
