@extends('layouts.app')
@section('title', 'Add Product')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10">

    <div class="mb-8">
        <a href="{{ route('admin.products.index') }}" class="text-sm text-indigo-600 hover:underline">← Back to Products</a>
        <h1 class="text-3xl font-bold text-gray-900 mt-1">Add New Product</h1>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Main fields --}}
            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-5">
                    <h2 class="font-bold text-gray-900">Product Info</h2>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Product Name</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none @error('name') border-red-400 @enderror"
                               required>
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Short Description</label>
                        <input type="text" name="short_description" value="{{ old('short_description') }}"
                               class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                               maxlength="500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Description</label>
                        <textarea name="description" rows="5"
                                  class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none resize-none">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-5">
                    <h2 class="font-bold text-gray-900">Pricing & Inventory</h2>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                            <input type="text" name="sku" value="{{ old('sku') }}"
                                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none @error('sku') border-red-400 @enderror"
                                   required>
                            @error('sku') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stock Quantity</label>
                            <input type="number" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" min="0"
                                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Base Price ($)</label>
                            <input type="number" name="base_price" value="{{ old('base_price') }}" min="0" step="0.01"
                                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none @error('base_price') border-red-400 @enderror"
                                   required>
                            @error('base_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sale Price ($) <span class="text-gray-400">(optional)</span></label>
                            <input type="number" name="sale_price" value="{{ old('sale_price') }}" min="0" step="0.01"
                                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="font-bold text-gray-900 mb-4">Product Images</h2>
                    <input type="file" name="images[]" multiple accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100">
                    <p class="text-xs text-gray-400 mt-2">Upload up to 8 images (JPG, PNG, WebP, max 2MB each)</p>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
                    <h2 class="font-bold text-gray-900">Organisation</h2>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select name="category_id"
                                class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none @error('category_id') border-red-400 @enderror"
                                required>
                            <option value="">Select category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-between py-2">
                        <label class="text-sm font-medium text-gray-700">Active</label>
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}
                               class="w-4 h-4 text-indigo-600 rounded">
                    </div>

                    <div class="flex items-center justify-between py-2">
                        <label class="text-sm font-medium text-gray-700">Featured</label>
                        <input type="checkbox" name="is_featured" value="1"
                               {{ old('is_featured') ? 'checked' : '' }}
                               class="w-4 h-4 text-indigo-600 rounded">
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-xl transition shadow-sm">
                    Save Product
                </button>

                <a href="{{ route('admin.products.index') }}"
                   class="block w-full text-center text-sm text-gray-500 hover:text-gray-700 py-2">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>
@endsection