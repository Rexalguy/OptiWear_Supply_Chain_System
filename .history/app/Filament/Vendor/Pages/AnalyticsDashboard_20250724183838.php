<?php

namespace App\Filament\Vendor\Pages;

use Carbon\Carbon;
use App\Models\Order;
use Filament\Pages\Page;
use App\Models\OrderItem;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Support\Htmlable;
<<<<<<< HEAD
use Illuminate\Support\Facades\Auth;
=======
use App\Filament\Vendor\Widgets\ProductRadar;
use App\Filament\Vendor\Widgets\VendorStatsOverview;
use App\Filament\Vendor\Widgets\MonthlyOrdersLineChart;
use App\Filament\Vendor\Widgets\CatergoryDoughnutAndPieChart;
>>>>>>> ac59f7b464b7c1998116c6dd48251bf54d49c255

class AnalyticsDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = "Business Analytics";
    protected static ?string $navigationGroup = 'Analytics';
    protected static string $view = 'filament.vendor.pages.analytics-dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            VendorStatsOverview::class,
        ];
    }



    protected function getFooterWidgets(): array
    {
        return [
            MonthlyOrdersLineChart::class,
            CatergoryDoughnutAndPieChart::class,
            ProductRadar::class,
        ];
    }

<<<<<<< HEAD
    public function getSubHeading(): string | Htmlable
    {
        return __('Track your business performance and growth metrics.');
    }

    public function mount()
    {
        $this->loadAnalytics();
    }

    public function loadAnalytics()
    {
        $vendorId = Auth::user()->id;
        
        // Basic metrics
        $this->totalRevenue = Order::where('vendor_id', $vendorId)->sum('total');
        $this->totalOrders = Order::where('vendor_id', $vendorId)->count();
        $this->pendingOrders = Order::where('vendor_id', $vendorId)->where('status', 'pending')->count();
        $this->completedOrders = Order::where('vendor_id', $vendorId)->where('status', 'completed')->count();

        // Monthly revenue for the last 6 months
        $this->monthlyRevenue = collect(range(5, 0))->map(function ($monthsAgo) use ($vendorId) {
            $date = Carbon::now()->subMonths($monthsAgo);
            $revenue = Order::where('vendor_id', $vendorId)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('total');
            
            return [
                'month' => $date->format('M Y'),
                'revenue' => $revenue
            ];
        })->toArray();

        // Top selling products
        $this->topProducts = OrderItem::select('product_id')
            ->selectRaw('SUM(quantity) as total_quantity')
            ->selectRaw('SUM(unit_price * quantity) as total_revenue')
            ->with(['product:id,name'])
            ->whereHas('order', function ($query) use ($vendorId) {
                $query->where('vendor_id', $vendorId);
            })
            ->groupBy('product_id')
            ->orderBy('total_quantity', 'desc')
            ->limit(5)
            ->get()
            ->toArray();

        // Order status distribution
        $this->orderStatusDistribution = Order::where('vendor_id', $vendorId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->toArray();

        // Recent activity
        $this->recentActivity = Order::where('vendor_id', $vendorId)
            ->with(['orderItems.product:id,name'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->toArray();
    }

    public function refreshData()
    {
        $this->loadAnalytics();
        $this->dispatch('analytics-refreshed');
    }
=======
>>>>>>> ac59f7b464b7c1998116c6dd48251bf54d49c255
}
