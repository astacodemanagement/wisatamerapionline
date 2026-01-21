<?php
namespace App\Exports;

use App\Models\Profit;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ProfitReportExport implements FromView
{
    protected $startDate;
    protected $endDate;
    protected $transactionCategoryId;
    protected $cashIds;

    public function __construct($startDate, $endDate, $transactionCategoryId = null, $cashIds = [])
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->transactionCategoryId = $transactionCategoryId;
        $this->cashIds = $cashIds;
    }

    public function view(): View
    {
        $query = Profit::with(['cash', 'transaction', 'order', 'purchase'])
            ->whereBetween('date', [$this->startDate, $this->endDate]);
    
        if ($this->transactionCategoryId) {
            $query->where('category', $this->transactionCategoryId);
        }
    
        if (!empty($this->cashIds)) {
            $query->whereIn('cash_id', $this->cashIds);
        }
    
        $data_profit = $query->orderBy('date', 'desc')->get();
    
        // Menghapus pemisah ribuan dan memastikan angka yang diproses adalah angka biasa
        foreach ($data_profit as $profit) {
            $profit->amount = (float) str_replace([',', '.'], '', $profit->amount); // Menghapus separator dan konversi ke float
        }
    
        return view('report.profit_report.excel', compact('data_profit'));
    }
    
}
