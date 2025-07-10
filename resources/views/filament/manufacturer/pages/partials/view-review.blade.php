<div class="space-y-4 text-gray-800 dark:text-gray-200">
    <div>
        <strong>Rating:</strong>
        <div>
            @if ($record->rating)
                @for ($i = 0; $i < $record->rating; $i++)
                    ⭐
                @endfor
            @else
                <span>No rating</span>
            @endif
        </div>
    </div>

    <div>
        <strong>Review:</strong>
        <p class="mt-1">{{ $record->review ?? 'No review submitted.' }}</p>
    </div>
</div>
