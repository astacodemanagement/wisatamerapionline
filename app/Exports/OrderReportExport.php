<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class OrderReportExport implements FromView
{
    protected $startDate;
    protected $endDate;

    // Terima parameter tanggal dari controller
    public function __construct($startDate, $endDate, $typePayment = null, $cashIds = [])
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->typePayment = $typePayment;
        $this->cashIds = $cashIds;
    }
    

    public function view(): View
    {
        $query = Order::with(['customer', 'user', 'cash'])
            ->whereBetween('order_date', [$this->startDate, $this->endDate]);
    
        // Filter type_payment jika ada
        if ($this->typePayment) {
            $query->where('type_payment', $this->typePayment);
        }
    
        // Filter cash_id jika ada
        if (!empty($this->cashIds)) {
            $query->whereIn('cash_id', $this->cashIds);
        }
    
        $data_orders = $query->orderBy('id', 'desc')->get();
    
        return view('report.order_report.excel', compact('data_orders'));
    }
    
}
