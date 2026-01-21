<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data_orders;

    public function __construct($data_orders)
    {
        $this->data_orders = $data_orders;
    }

    public function headings(): array
    {
        return [
            'No',
            'No Order',
            'Tanggal Order',
            'Customer Name',
            'Product',
            'Quantity',
            'Order Price',
            'Total'
        ];
    }

    public function map($row): array
    {
        return [
            $row['no'],
            $row['no_order'],
            \Carbon\Carbon::parse($row['order_date'])->format('d-m-Y'), // Format tanggal
            $row['customer_name'],
            $row['product_name'],
            $row['quantity'],
            $row['order_price'],
            $row['total']
        ];
    }

    public function collection()
    {
        $exportData = collect();
        $no = 1;

        foreach ($this->data_orders as $order) {
            foreach ($order->orderItems as $orderItem) {
                $exportData->push([
                    'no' => $no++,
                    'no_order' => $order->no_order,
                    'order_date' => $order->order_date, // Tambahkan tanggal order
                    'customer_name' => $order->customer->name,
                    'product_name' => $orderItem->product->name,
                    'quantity' => $orderItem->quantity,
                    'order_price' => $orderItem->order_price,
                    'total' => $orderItem->quantity * $orderItem->order_price,
                ]);
            }
        }

        return $exportData;
    }
}
