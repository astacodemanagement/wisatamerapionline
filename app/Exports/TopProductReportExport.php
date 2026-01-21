<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class TopProductReportExport implements FromView
{
    protected $topProduct;

    public function __construct($topProduct)
    {
        $this->topProduct = $topProduct;
    }

    public function view(): View
    {
        // Mengirimkan data produk terlaris ke view Excel
        return view('report.top_product_report.excel', [
            'topProduct' => $this->topProduct
        ]);
    }
}

