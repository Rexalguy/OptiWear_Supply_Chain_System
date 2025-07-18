<div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
    <h3 class="text-lg font-semibold mb-4">Database Debug Information</h3>

    <div class="mb-4">
        <strong>Total Records:</strong> {{ $totalRecords }}
    </div>

    <div class="mb-4">
        <strong>Segment Counts (Raw):</strong>
        <ul class="list-disc ml-5">
            @foreach($segmentCounts as $segment)
                <li>{{ $segment->segment_label }}: {{ $segment->count }} records</li>
            @endforeach
        </ul>
    </div>

    <div class="mb-4">
        <strong>Segment Counts (With Percentage):</strong>
        <ul class="list-disc ml-5">
            @foreach($segmentCountsWithPercentage as $segment)
                <li>{{ $segment->segment_label }}: {{ $segment->count }} records ({{ $segment->percentage }}%)</li>
            @endforeach
        </ul>
    </div>
</div>