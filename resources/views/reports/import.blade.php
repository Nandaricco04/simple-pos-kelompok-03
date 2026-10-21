@extends('layouts.app')
@section('title', 'Import Produk')
@section('content')

<h1 class="text-lg font-semibold mb-4">Import Produk dari CSV</h1>

@if (session('success'))
    <div class="bg-green-50 text-green-700 p-3 rounded-md mb-4">
        {{ session('success') }}
    </div>
@endif

<form method="POST" action="{{ route('import.store') }}" enctype="multipart/form-data" class="max-w-md mb-8">
    @csrf

    <label class="block mb-3">
        <span class="text-sm font-medium">File CSV</span>
        <input type="file" name="csv" accept=".csv" class="mt-1 block w-full text-sm">
        @error('csv')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </label>

    <p class="text-xs text-slate-500 mb-3">
        Column yang dibutuhkan: <code>name</code>, <code>category_id</code>, <code>price</code>.
    </p>

    <button type="submit" class="bg-slate-900 text-white px-4 py-2 rounded-md text-sm">
        Import
    </button>
</form>

<div class="mt-8">
    <h2 class="text-lg font-semibold mb-4">
        Riwayat Row yang Gagal Di-import
    </h2>

    <div class="overflow-x-auto bg-white border rounded-md max-w-3xl">
        <table class="w-full border-collapse text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border p-3 text-left">Nama File</th>
                    <th class="border p-3 text-left">Nomor Row</th>
                    <th class="border p-3 text-left">Pesan Error</th>
                    <th class="border p-3 text-left">Waktu</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($importErrors as $error)
                    <tr>
                        <td class="border p-3">
                            {{ $error->file_name }}
                        </td>
                        <td class="border p-3">
                            Baris {{ $error->row_number }}
                        </td>
                        <td class="border p-3 text-red-600">
                            {{ $error->error_message }}
                        </td>
                        <td class="border p-3">
                            {{ $error->created_at->format('d-m-Y H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="border p-4 text-center text-gray-500">
                            Belum ada row yang gagal di-import.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection