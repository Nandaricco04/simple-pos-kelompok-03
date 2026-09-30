@extends('layouts.app')

@section('title', 'Kasir')

@section('content')
    <h1 class="text-lg font-semibold mb-4">Transaksi Kasir</h1>

    @if (session('success'))
        <div class="bg-green-50 text-green-700 p-3 rounded-md mb-4">
            {{ session('success') }}
        </div>
    @endif

    @error('items')
        <div class="bg-red-50 text-red-700 p-3 rounded-md mb-4">
            {{ $message }}
        </div>
    @enderror

    <form
        method="POST"
        action="{{ route('transactions.store') }}"
        x-data="{
            cart: [],

            addToCart(id, name, price) {
                let existingItem = this.cart.find(item => item.id === id);

                if (existingItem) {
                    existingItem.qty++;
                } else {
                    this.cart.push({
                        id: id,
                        name: name,
                        price: price,
                        qty: 1
                    });
                }
            },

            increaseQty(index) {
                this.cart[index].qty++;
            },

            decreaseQty(index) {
                if (this.cart[index].qty > 1) {
                    this.cart[index].qty--;
                } else {
                    this.cart.splice(index, 1);
                }
            },

            subtotal() {
                return this.cart.reduce((sum, item) => {
                    return sum + (item.price * item.qty);
                }, 0);
            }
        }"
    >

        @csrf
        <div class="grid grid-cols-3 gap-4">
            @foreach ($products as $product)
                <div
                    class="border rounded-md p-3 cursor-pointer hover:bg-gray-50 transition"
                    @click="addToCart(
                        {{ $product->id }},
                        '{{ $product->name }}',
                        {{ $product->price }}
                    )"
                >
                    <div class="flex items-center justify-between mb-1">
                        <p class="font-medium">
                            {{ $product->name }}
                        </p>

                        @if ($product->stock < 10)
                            <span class="text-xs font-medium px-2 py-0.5 rounded bg-amber-100 text-amber-700">
                                Stok Menipis
                            </span>
                        @endif
                    </div>

                    <p class="text-sm text-slate-500">
                        Rp {{ number_format($product->price) }}
                    </p>
                </div>
            @endforeach
        </div>

        @if ($products->hasPages())
            <div class="flex items-center justify-center gap-2 mt-6">

                @if ($products->onFirstPage())
                    <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-md">
                        ←
                    </span>
                @else
                    <a
                        href="{{ $products->previousPageUrl() }}"
                        class="px-3 py-2 text-sm text-gray-600 bg-white border rounded-md hover:bg-gray-100"
                    >
                        ←
                    </a>
                @endif

                @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                    @if ($page == $products->currentPage())
                        <span class="px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded-md">
                            {{ $page }}
                        </span>
                    @else
                        <a
                            href="{{ $url }}"
                            class="px-3 py-2 text-sm text-gray-600 bg-white border rounded-md hover:bg-blue-50"
                        >
                            {{ $page }}
                        </a>
                    @endif
                @endforeach

                @if ($products->hasMorePages())
                    <a
                        href="{{ $products->nextPageUrl() }}"
                        class="px-3 py-2 text-sm text-gray-600 bg-white border rounded-md hover:bg-gray-100"
                    >
                        →
                    </a>
                @else
                    <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-md">
                        →
                    </span>
                @endif

            </div>
        @endif

        <div class="mt-4 border-t pt-3">

            <template x-for="(item, index) in cart" :key="item.id">
                <div class="flex items-center justify-between border-b py-3">

                    <div>
                        <p class="font-medium" x-text="item.name"></p>

                        <p class="text-sm text-slate-500">
                            Rp <span x-text="item.price"></span>
                        </p>

                        <input
                            type="hidden"
                            :name="'items[' + index + '][product_id]'"
                            :value="item.id"
                        >

                        <input
                            type="hidden"
                            :name="'items[' + index + '][qty]'"
                            :value="item.qty"
                        >
                    </div>

                    {{-- Kontrol Qty --}}
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            @click="decreaseQty(index)"
                            class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300"
                        >
                            -
                        </button>

                        <span
                            class="w-6 text-center"
                            x-text="item.qty"
                        ></span>

                        <button
                            type="button"
                            @click="increaseQty(index)"
                            class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300"
                        >
                            +
                        </button>
                    </div>

                    {{-- Total Item --}}
                    <p class="font-medium">
                        Rp <span x-text="item.price * item.qty"></span>
                    </p>

                </div>
            </template>

            {{-- Subtotal --}}
            <p class="font-semibold mt-4">
                Subtotal:
                Rp <span x-text="subtotal()"></span>
            </p>

            {{-- Tombol Bayar --}}
            <button
                type="submit"
                class="mt-3 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
            >
                Bayar
            </button>

        </div>

    </form>
@endsection
