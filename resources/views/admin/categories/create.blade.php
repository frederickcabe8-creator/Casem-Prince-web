@extends('layouts.app')
@section('title', 'Add Category')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10">

    <div class="mb-8">
        <a href="{{ route('admin.categories.index') }}" class="text-sm text-indigo-600 hover:underline">← Back to Categories</a>
        <h1 class="text-3xl font-bold text-gray-900 mt-1">Add New Category</h1>
    </div>

    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf

        <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-5">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category Name</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none @error('name') border-red-400 @enderror"
                       required>
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-gray-400">(optional)</span></label>
                <textarea name="description" rows="3"
                          class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none resize-none">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Parent Category <span class="text-gray-400">(optional)</span></label>
                <select name="parent_id"
                        class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option value="">None (top-level)</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('parent_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                       class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>

            <div class="flex items-center justify-between py-2">
                <label class="text-sm font-medium text-gray-700">Active</label>
                <input type="checkbox" name="is_active" value="1" checked
                       class="w-4 h-4 text-indigo-600 rounded">
            </div>

            <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-xl transition">
                Save Category
            </button>
        </div>
    </form>
</div>
@endsection