@extends('layouts.admin-layout')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-4 text-gray-900 dark:text-gray-100">Tambah Kategori Baru</h1>

    <div class="max-w-lg mx-auto">
        <form action="{{ route('categories.store') }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            @csrf

            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nama Kategori</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-input mt-1 block w-full" required>
                @error('name')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="code" class="block text-gray-700 text-sm font-bold mb-2">Kode Kategori</label>
                <input type="text" id="code" name="code" value="{{ old('code') }}" class="form-input mt-1 block w-full" required>
                @error('code')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Simpan
                </button>
                <a href="{{ route('categories.index') }}" class="text-blue-500 hover:text-blue-700">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection
