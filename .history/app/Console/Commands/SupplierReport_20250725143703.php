<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use App\Mail\SendSupplierReport;
use Illuminate\Support\Facades\Mail;
use App\Models\RawMaterialsPurchaseOrder;

class SupplierReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:supplier-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Executing the supplier report command...');
        $supplier = User::where('role', 'supplier')->first();
        $reportDate = now()->format('Y-m-d-H:i:s');
        $pendingOrdersCount = RawMaterialsPurchaseOrder::where('status', 'pending')->count();
        $deliveredOrdersCount = RawMaterialsPurchaseOrder::where('status', 'delivered')->count();
        $confirmedOrdersCount = RawMaterialsPurchaseOrder::where('status', 'confirmed')->count();
        $cancelledOrdersCount = RawMaterialsPurchaseOrder::where('status', 'cancelled')->count();
        $totalOrdersCount = RawMaterialsPurchaseOrder::count();
        $totalSales = RawMaterialsPurchaseOrder::whereIn('status', ['delivered', 'confirmed'])->sum('total_price');
        Mail::to($)->send(new SendSupplierReport($supplier, $pendingOrdersCount, $deliveredOrdersCount, $confirmedOrdersCount, $cancelledOrdersCount, $totalOrdersCount, $totalSales, $reportDate));
    }
}