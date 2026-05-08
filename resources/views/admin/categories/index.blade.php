@extends('layouts.app')
@section('title', 'Admin — Categories')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-10">

    <div class="flex items-center justify-between mb-8">
        <div>
            <p class="text-sm text-indigo-600 font-medium uppercase tracking-wide">Admin Panel</p>
            <h1 class="text-3xl font-bold text-gray-900">Categories</h1>
        </div>
        <a href="{{ route('admin.categories.create') }}"
           class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-semibold text-sm transition">
            + Add Category
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Name</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Slug</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Parent</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Products</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($categories as $category)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $category->name }}</td>
                        <td class="px-6 py-4 text-gray-500 font-mono text-xs">{{ $category->slug }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $category->parent->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $category->products_count }}</td>
                        <td class="px-6 py-4">
                            @if ($category->is_active)
                                <span class="text-xs bg-green-50 text-green-700 px-2.5 py-0.5 rounded-full font-medium">Active</span>
                            @else
                                <span class="text-xs bg-gray-100 text-gray-500 px-2.5 py-0.5 rounded-full font-medium">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.categories.edit', $category) }}"
                                   class="text-xs bg-indigo-50 text-indigo-600 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition font-medium">
                                    Edit
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                      onsubmit="return confirm('Delete this category?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-xs bg-red-50 text-red-500 hover:bg-red-100 px-3 py-1.5 rounded-lg transition font-medium">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            No categories yet.
                            <a href="{{ route('admin.categories.create') }}" class="text-indigo-600 hover:underline ml-1">Add one →</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection