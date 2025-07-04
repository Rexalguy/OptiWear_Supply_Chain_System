<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\RawMaterial;
use Illuminate\Console\Command;
use App\Mail\SendManufacturerReport;
use Illuminate\Support\Facades\Mail;
use App\Models\RawMaterialsPurchaseOrder;

class ManufacturerReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:manufacturer-report';

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
        $user = User::where('role', 'manufacturer')->first();
        //     ];
        // if (!$user) {
        //     $this->error('No manufacturer user found.');
        //     return;
        // }
        // if ($user->isEmpty()) {
        //     $this->user= [
        //         'name' => 'Manufacturer',
        //         'email' => '<no-reply@example.com>'
        $this->info('Executing the manufacturer report command...');
        // }
        $deliveredCount = RawMaterialsPurchaseOrder::where('status', 'delivered')->count();
        $pendingCount = RawMaterialsPurchaseOrder::where('status', 'pending')->count();
        $confirmedCount = RawMaterialsPurchaseOrder::where('status', 'confirmed')->count();
        $cancelledCount = RawMaterialsPurchaseOrder::where('status', 'cancelled')->count();
        $totalCount = RawMaterialsPurchaseOrder::count();
        
        $date = now()->format('Y-m-d-H:i:s');
        $still = RawMaterial::whereColumn('current_stock', '>', \DB::raw('reorder_level * 2'))->count();
        $low = RawMaterial::whereColumn('current_stock', '>=', 'reorder_level')
            ->whereColumn('current_stock', '<', \DB::raw('reorder_level * 2'))
            ->count();
        $out = RawMaterial::whereColumn('current_stock', '<', 'reorder_level')->count();

        Mail::to('josephotai209@gmail.com')->send(new SendManufacturerReport( $user, $deliveredCount, $pendingCount, $confirmedCount, $cancelledCount, $totalCount,$still, $low,$out,$date));
    }
}