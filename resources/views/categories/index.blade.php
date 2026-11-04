@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-xl font-bold">Daftar Kategori</h1>
    <a href="{{ route('categories.create') }}" class="bg-slate-900 text-white px-4 py-2 rounded-md text-sm">
        + Tambah Kategori
    </a>
</div>

@if (session('success'))
    <div class="bg-green-50 text-green-700 p-3 rounded-md mb-4 text-sm">
        {{ session('success') }}
    </div>
@endif

<form method="GET" action="{{ route('categories.index') }}" class="mb-4">
    <input type="text" name="q" value="{{ $q }}" placeholder="Cari kategori..." class="border rounded-md px-3 py-2 text-sm w-64">
    <button type="submit" class="bg-slate-900 text-white px-3 py-2 rounded-md text-sm">Cari</button>
</form>

<div class="bg-white border rounded-md overflow-hidden">
    <table class="w-full text-left border-collapse text-sm">
        <thead class="bg-slate-100 border-b">
            <tr>
                <th class="p-3">Nama Kategori</th>
                <th class="p-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categories as $category)
                <tr class="border-b last:border-none">
                    <td class="p-3 font-medium">{{ $category->name }}</td>
                    <td class="p-3 text-right">
                        <a href="{{ route('categories.edit', $category) }}" class="text-blue-600 hover:underline mr-3">Edit</a>
                        <form method="POST" action="{{ route('categories.destroy', $category) }}" class="inline" onsubmit="return confirm('Yakin hapus kategori ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 hover:underline">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="p-4 text-center text-slate-500">Belum ada kategori.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $categories->links() }}
</div>
@endsection