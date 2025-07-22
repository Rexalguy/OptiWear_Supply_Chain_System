@php
    \Illuminate\Pagination\Paginator::useTailwind();
@endphp

<x-filament-panels::page>
<div>
    <h1 class="text-2xl font-bold mb-4 flex justify-center">
        Checkout Page <span style="color: orange" class="px-2"> {{ $cartCount }} </span> Items in the Cart
    </h1>
    @forelse ($cart as $item)
        <div class="grid grid-cols-1 rounded-xl p-4 shadow bg-white dark:bg-gray-900 md:grid-cols-2  items-center justify-between mb-4">
            <div>
                <div class="">
                <div class="w-full h-40 flex items-center justify-center bg-gray-100 rounded-md mb-2 overflow-hidden">
                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="h-full w-auto object-contain">
                </div>
                <div class="flex gap-4 items-center align-top mt-0">
                    <div style="margin-top: -30px;" class="w-full text-start md:text-left">
                        <h4 class="font-semibold">{{ $item['name'] }}</h4>
                        <p>Price: UGX {{ $item['price'] }}</p>
                        <p>Quantity: {{ $item['quantity'] }}</p>
                    </div>
                    <div class="flex flex-col gap-4 items-center align-top mt-0">
                        <div class="flex space-x-5 gap-3">
                            <x-filament::button wire:click="reduceQuantity({{ $item['id'] }}, 100)" color="danger" size="xs" icon="heroicon-m-minus" icon-position="before">100</x-filament::button>
                            <x-filament::button wire:click="increaseQuantity({{ $item['id'] }}, 100)" color="success" size="xs" icon="heroicon-m-plus" icon-position="before">100</x-filament::button>
                        </div>
                        <div class="flex space-x-5 gap-3">
                            <x-filament::button wire:click="reduceQuantity({{ $item['id'] }}, 350)" color="danger" size="xs" icon="heroicon-m-minus" icon-position="before">350</x-filament::button>
                            <x-filament::button wire:click="increaseQuantity({{ $item['id'] }}, 350)" color="success" size="xs" icon="heroicon-m-plus" icon-position="before">350</x-filament::button>
                        </div>
                        <div class="flex space-x-5 gap-3">
                            <x-filament::button wire:click="reduceQuantity({{ $item['id'] }}, 750)" color="danger" size="xs" icon="heroicon-m-minus" icon-position="before">750</x-filament::button>
                            <x-filament::button wire:click="increaseQuantity({{ $item['id'] }}, 750)" color="success" size="xs" icon="heroicon-m-plus" icon-position="before">750</x-filament::button>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-16">
                <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-slate-700 dark:to-slate-600 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">No orders yet</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-md mx-auto">You haven't placed any orders yet. Start shopping to see your order history here.</p>
                <a href="{{ url('/vendor/product') }}">
                    <x-filament::button 
                        class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 border-0 shadow-lg transform hover:scale-105 transition-all duration-200"
                        size="lg"
                        icon="heroicon-m-shopping-bag"
                    >
                        Start Shopping
                    </x-filament::button>
                </a>
            </div>
        @endif
        {{ $this->orders->links() }}
    </div>
</x-filament-panels::page>

