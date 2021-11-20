<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\models\Orders;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $sortBy = $request->input('sort_by');
        $rep = $request->input('rep');

        if (!empty($rep)) {
            $orders = Orders::where('rep', '=', $rep)->where('is_valid', '=', '1')->get();
        } else if ($sortBy) {
            $orders = Orders::where('is_valid', '=', '1')->orderBy($sortBy, 'desc')->get();
        } else {
            $orders = Orders::where('is_valid', '=', '1')->get();
        }

        return json_encode($orders);
    }

    public function store(Request $request)
    {
        $orderString = $request->input('order_str');
        $orderDetails = explode(' – ', $orderString);

        $order = new Orders();
        // check if valid date and not any future date
        $order->order_date = trim($orderDetails[0]);
        // check if the region is valid and in allowed list
        $order->region = trim($orderDetails[1]);
        // if we have users table, check rep is valid
        $order->rep = trim($orderDetails[2]);
        // validate items from items table
        $order->item = trim($orderDetails[3]);
        // validate the quantity. should be an integer and in allowed range
        $order->quantity = trim($orderDetails[4]);
        // validate the cost from items table if exists
        $order->cost_per_unit = trim($orderDetails[5]);
        // validate if total is matching items * quantity
        $order->total = trim($orderDetails[6]);
        $order->is_valid = 1;

        $order->save();

        return json_encode($order);
    }

    public function update(Request $request)
    {
        $orderDate = $request->input('order_date');
        $quantity = $request->input('quantity');

        // validate input
        Orders::where('order_date', '=', $orderDate)->where('is_valid', '=', '1')->update([
            'quantity' => $quantity
        ]);

        $order = Orders::where('order_date', '=', $orderDate)->get();

        return json_encode($order);
    }

    public function delete(Request $request)
    {
        $key = $request->input('key');
        $expr = $request->input('expr');
        $val = $request->input('val');

        // validate input
        // soft deleting the records
        Orders::where($key, $expr, $val)->update([
            'is_valid' => 0
        ]);

        $order = Orders::where('is_valid', '=', '1')->get();

        return json_encode($order);
    }

    public function download()
    {
        $fileName = 'orders.csv';

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $orders = Orders::where('is_valid', '=', '1')->get();
        $columns = array('ID', 'Order Date', 'Region', 'Rep', 'Item', 'Quantity', 'Cost per item', 'Total', 'Created At', 'Update At');

        $callback = function() use($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($orders as $order) {
                fputcsv($file, $order->toArray());
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


}

