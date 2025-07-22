<?php

require_once 'vendor/autoload.php';

use App\Models\SegmentationResult;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing Segment Top Products Table Widget\n";
echo "==========================================\n\n";

// Test 1: Check if we have data
$totalRecords = SegmentationResult::count();
echo "Total records in segmentation_results: {$totalRecords}\n";

$recordsWithPurchases = SegmentationResult::where('total_purchased', '>', 0)->count();
echo "Records with purchases > 0: {$recordsWithPurchases}\n";

$distinctSegments = SegmentationResult::distinct('segment_label')->count('segment_label');
echo "Distinct segments: {$distinctSegments}\n\n";

// Test 2: Test the aggregation logic
try {
    $segmentData = SegmentationResult::select([
        'segment_label',
        'gender', 
        'age_group',
        'shirt_category',
        DB::raw('SUM(total_purchased) as total_purchased'),
        DB::raw('SUM(customer_count) as customer_count')
    ])
    ->groupBy('segment_label', 'gender', 'age_group', 'shirt_category')
    ->havingRaw('SUM(total_purchased) > 0')
    ->orderBy('segment_label')
    ->orderByDesc('total_purchased')
    ->get();

    echo "Aggregated data records: " . $segmentData->count() . "\n";

    // Get top product for each segment
    $topProducts = $segmentData->groupBy('segment_label')->map(function ($products) {
        return $products->first();
    });

    echo "Top products by segment: " . $topProducts->count() . "\n\n";

    // Display sample results
    echo "Sample Top Products by Segment:\n";
    echo "-------------------------------\n";
    foreach ($topProducts->take(5) as $product) {
        echo sprintf(
            "%-20s | %-15s | %s units\n", 
            $product->segment_label, 
            $product->shirt_category, 
            number_format($product->total_purchased)
        );
    }

    echo "\nTest completed successfully! ✅\n";

} catch (Exception $e) {
    echo "Error during testing: " . $e->getMessage() . "\n";
    echo "❌ Test failed\n";
}
