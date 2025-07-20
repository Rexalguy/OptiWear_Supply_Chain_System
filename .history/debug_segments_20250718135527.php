<?php

use App\Models\SegmentationResult;
use Illuminate\Support\Facades\DB;

// Test script to debug segmentation data
echo "=== SEGMENTATION DEBUG ===" . PHP_EOL;

// Check total count
$total = SegmentationResult::count();
echo "Total records: " . $total . PHP_EOL . PHP_EOL;

// Get actual segment distribution
$segments = DB::table('segmentation_results')
    ->select('segment_label', DB::raw('COUNT(*) as count'))
    ->groupBy('segment_label')
    ->orderBy('count', 'desc')
    ->get();

echo "Segment distribution:" . PHP_EOL;
foreach ($segments as $segment) {
    $percentage = round(($segment->count / $total) * 100, 1);
    echo "- {$segment->segment_label}: {$segment->count} records ({$percentage}%)" . PHP_EOL;
}

echo PHP_EOL . "=== SAMPLE RECORDS ===" . PHP_EOL;
$sample = SegmentationResult::limit(5)->get(['segment_label', 'gender', 'age_group', 'total_purchased']);
foreach ($sample as $record) {
    echo "Label: {$record->segment_label}, Gender: {$record->gender}, Age: {$record->age_group}, Purchased: {$record->total_purchased}" . PHP_EOL;
}
