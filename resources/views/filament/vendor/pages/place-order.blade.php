<x-filament-panels::page>
    @if (session()->has('success'))
        <div class="mb-4 p-4 rounded bg-green-100 text-green-800 shadow">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="placeOrder" class="space-y-6">
        {{ $this->form }}

        <button 
            type="submit" 
            class="mt-4 bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded shadow"
        >
            Place Order
        </button>
    </form>
</x-filament-panels::page>
