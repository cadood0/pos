<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_model extends MY_Model
{
    public function summary($from, $to)
    {
        $sales = $this->db
            ->select('COUNT(sales.id) AS sales_count, COALESCE(SUM(sales.total_amount), 0) AS revenue', FALSE)
            ->from('sales')
            ->where('sales.created_at >=', $from)
            ->where('sales.created_at <=', $to)
            ->get()
            ->row();

        $items = $this->db
            ->select(
                'COALESCE(SUM(sale_items.quantity), 0) AS items_sold, '
                .'COALESCE(SUM((sale_items.unit_price - products.cost_price) * sale_items.quantity), 0) AS profit',
                FALSE
            )
            ->from('sales')
            ->join('sale_items', 'sale_items.sale_id = sales.id')
            ->join('products', 'products.id = sale_items.product_id')
            ->where('sales.created_at >=', $from)
            ->where('sales.created_at <=', $to)
            ->get()
            ->row();

        return (object) [
            'sales_count' => $sales ? $sales->sales_count : 0,
            'revenue'     => $sales ? $sales->revenue : 0,
            'items_sold'  => $items ? $items->items_sold : 0,
            'profit'      => $items ? $items->profit : 0,
        ];
    }

    public function sales_by_day($from, $to)
    {
        return $this->db
            ->select(
                'DATE(sales.created_at) AS date, '
                .'COUNT(sales.id) AS sales_count, '
                .'COALESCE(SUM(sales.total_amount), 0) AS revenue',
                FALSE
            )
            ->from('sales')
            ->where('sales.created_at >=', $from)
            ->where('sales.created_at <=', $to)
            ->group_by('DATE(sales.created_at)')
            ->order_by('date', 'ASC')
            ->get()
            ->result();
    }

    public function top_products($from, $to, $limit = 10)
    {
        return $this->db
            ->select(
                'products.id AS product_id, '
                .'products.name, '
                .'COALESCE(SUM(sale_items.quantity), 0) AS units_sold, '
                .'COALESCE(SUM(sale_items.quantity * sale_items.unit_price), 0) AS revenue',
                FALSE
            )
            ->from('sale_items')
            ->join('sales', 'sales.id = sale_items.sale_id')
            ->join('products', 'products.id = sale_items.product_id')
            ->where('sales.created_at >=', $from)
            ->where('sales.created_at <=', $to)
            ->group_by(['products.id', 'products.name'])
            ->order_by('units_sold', 'DESC')
            ->limit((int) $limit)
            ->get()
            ->result();
    }

    public function low_stock($threshold = 10)
    {
        return $this->db
            ->select('products.id AS product_id, products.name, products.barcode, products.stock_qty')
            ->from('products')
            ->where('products.stock_qty <=', (int) $threshold)
            ->order_by('products.stock_qty', 'ASC')
            ->get()
            ->result();
    }
}
