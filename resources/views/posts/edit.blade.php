@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">

    <a href="{{ route('posts.show', $post) }}"
       class="text-sm text-blue-600 hover:underline mb-6 inline-block">
        ← Back to Post
    </a>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-blue-700 to-indigo-600 px-8 py-6">
            <h1 class="text-xl font-bold text-white">Edit Post</h1>
        </div>

        <form method="POST" action="{{ route('posts.update', $post) }}" class="p-8">
            @csrf @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                               text-sm bg-gray-50 focus:outline-none focus:ring-2
                               focus:ring-blue-500">
                    @foreach($categories as $key => $label)
                        <option value="{{ $key }}"
                            {{ $post->category === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input type="text" name="title"
                       value="{{ old('title', $post->title) }}"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                              text-sm bg-gray-50 focus:outline-none focus:ring-2
                              focus:ring-blue-500">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                <textarea name="body" rows="6"
                          class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                 text-sm bg-gray-50 focus:outline-none focus:ring-2
                                 focus:ring-blue-500 resize-none">{{ old('body', $post->body) }}</textarea>
            </div>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-4">
                    @foreach($errors->all() as $error)
                        <p class="text-red-600 text-sm">• {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="flex gap-3">
                <button type="submit"
                        class="bg-blue-700 text-white px-6 py-2.5 rounded-xl
                               text-sm font-medium hover:bg-blue-800">
                    Save Changes
                </button>
                <a href="{{ route('posts.show', $post) }}"
                   class="border border-gray-200 text-gray-600 px-6 py-2.5
                          rounded-xl text-sm hover:bg-gray-50">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection