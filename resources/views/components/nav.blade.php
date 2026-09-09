<nav class="bg-slate-900 text-white px-4 py-3 flex justify-between items-center">
    <span class="font-semibold">Simple POS</span>

    <div class="flex gap-6">
        <a href="{{ route('pos.create') }}"
           @class([
               'hover:underline',
               'font-semibold text-white' => request()->routeIs('pos.create'),
               'text-slate-300' => ! request()->routeIs('pos.create'),
           ])>
            Kasir
        </a>
        <a href="{{ route('transactions.index') }}"
           @class([
               'hover:underline',
               'font-semibold text-white' => request()->routeIs('transactions.*'),
               'text-slate-300' => ! request()->routeIs('transactions.*'),
           ])>
            Transaksi
        </a>
    </div>
</nav>