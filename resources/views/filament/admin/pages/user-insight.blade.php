<x-filament-panels::page>
    <div>
<x-filament::card>
    <div class="space-y-4">
        <h3 class="text-xl font-bold">👥 Onboarding Progress: First 100 Customers</h3>

        <p class="text-sm text-gray-600 dark:text-gray-400">
            You've registered <strong>{{ number_format($this->getCurrent()) }}</strong> out of 100 customers!
        </p>

        {{-- Progress Bar --}}
        <div class="progress-bar">
            <div
                class="progress-fill animate-fill"
                style="width: {{ $this->getProgressPercentage() }}%;">
            </div>

            @foreach ($this->getMilestones() as $milestone)
                <div
                    class="progress-milestone"
                    style="left: {{ ($milestone / $this->getTarget()) * 100 }}%;">
                </div>
            @endforeach
        </div>

        <div class="flex justify-between text-xs text-gray-500">
            @foreach ($this->getMilestones() as $milestone)
                <span class="w-1/5 text-center">{{ $milestone }}</span>
            @endforeach
        </div>
    </div>
</x-filament::card>

    </div>
{{$this->table}}
</x-filament-panels::page>
