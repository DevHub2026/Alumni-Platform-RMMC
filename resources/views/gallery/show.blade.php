@extends('layouts.app')

@section('content')

{{-- ── Back link ─────────────────────────────────────────────────────── --}}
<a href="{{ route('gallery.index') }}"
   class="inline-flex items-center gap-1 text-sm text-blue-600
          hover:underline mb-6">
    ← Back to Gallery
</a>

{{-- ── Page Header ────────────────────────────────────────────────────── --}}
{{-- Shows event title, photo count, and the Upload button (if allowed)  --}}
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">{{ $event->title }}</h1>
        <p class="text-sm text-gray-400 mt-1">
            {{ $photos->count() }} photos ·
            {{ $event->event_date->format('F d, Y') }}
        </p>
    </div>

    @auth
        @if($canUpload)
            {{-- Toggle upload panel visibility --}}
            <button onclick="document.getElementById('upload-panel').classList.toggle('hidden')"
                    class="bg-blue-700 text-white px-5 py-2.5 rounded-xl
                           text-sm font-medium hover:bg-blue-800 transition
                           flex items-center gap-2">
                📷 Upload Photos
            </button>
        @elseif(Auth::user()->is_verified)
            {{-- Verified but not registered for this event --}}
            <div class="text-right">
                <p class="text-sm text-gray-400">
                    Only registered attendees can upload photos.
                </p>
                @if($event->event_date >= now())
                    <a href="{{ route('events.show', $event) }}"
                       class="text-xs text-blue-600 hover:underline">
                        Register for this event →
                    </a>
                @endif
            </div>
        @else
            {{-- Not verified — prompt to complete profile --}}
            <div class="text-right">
                <p class="text-sm text-gray-400">
                    Complete your profile to upload photos.
                </p>
            </div>
        @endif
    @endauth
</div>

{{-- ── Upload Panel ───────────────────────────────────────────────────── --}}
{{-- Hidden by default; toggled by the Upload Photos button above.       --}}
{{-- Uses Alpine.js (photoUpload) for drag-drop preview + caption input. --}}
@auth
@if($canUpload)
<div id="upload-panel" class="hidden mb-8">
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6">
        <h3 class="font-semibold text-blue-800 mb-4">
            📷 Upload Photos from {{ $event->title }}
        </h3>

        <form method="POST"
              action="{{ route('gallery.store', $event) }}"
              enctype="multipart/form-data"
              x-data="photoUpload()"
              class="space-y-4">
            @csrf

            {{-- Drop zone — click or drag files onto this area --}}
            <div class="border-2 border-dashed border-blue-300 rounded-xl p-8
                        text-center cursor-pointer hover:border-blue-500
                        hover:bg-blue-50 transition bg-white"
                 @click="$refs.fileInput.click()"
                 @dragover.prevent
                 @drop.prevent="handleDrop($event)">

                {{-- Hidden file input — triggered by clicking the drop zone --}}
                <input type="file"
                       name="photos[]"
                       multiple
                       accept="image/jpg,image/jpeg,image/png,image/webp"
                       x-ref="fileInput"
                       @change="handleFiles($event)"
                       class="hidden">

                {{-- Placeholder shown when no files are selected yet --}}
                <div x-show="previews.length === 0">
                    <p class="text-4xl mb-2">📸</p>
                    <p class="text-blue-700 font-medium text-sm">
                        Click to select or drag photos here
                    </p>
                    <p class="text-blue-400 text-xs mt-1">
                        JPG, PNG or WebP · Max 4MB each · Up to 10 photos
                    </p>
                </div>

                {{-- Preview grid — shown once files are selected --}}
                <div x-show="previews.length > 0"
                     class="grid grid-cols-3 md:grid-cols-5 gap-2">
                    <template x-for="(preview, index) in previews" :key="index">
                        <div class="relative group">
                            <img :src="preview"
                                 class="w-full h-20 object-cover rounded-lg">
                            {{-- Remove button appears on hover --}}
                            <button type="button"
                                    @click.stop="removePhoto(index)"
                                    class="absolute top-1 right-1 w-5 h-5
                                           bg-red-500 text-white rounded-full
                                           text-xs hidden group-hover:flex
                                           items-center justify-center">
                                ✕
                            </button>
                        </div>
                    </template>
                    {{-- "Add more" tile at the end of the preview grid --}}
                    <div class="w-full h-20 border-2 border-dashed border-blue-300
                                rounded-lg flex items-center justify-center
                                text-blue-400 text-2xl cursor-pointer
                                hover:border-blue-500"
                         @click.stop="$refs.fileInput.click()">
                        +
                    </div>
                </div>
            </div>

            {{-- Caption inputs — one per selected photo (optional) --}}
            <div x-show="previews.length > 0" class="space-y-2">
                <p class="text-xs font-medium text-gray-600">
                    Add captions (optional)
                </p>
                <template x-for="(preview, index) in previews" :key="index">
                    <div class="flex items-center gap-3">
                        <img :src="preview"
                             class="w-10 h-10 object-cover rounded-lg flex-shrink-0">
                        <input type="text"
                               :name="`captions[${index}]`"
                               :placeholder="`Caption for photo ${index + 1}`"
                               class="flex-1 border border-gray-200 rounded-lg
                                      px-3 py-1.5 text-xs focus:outline-none
                                      focus:ring-2 focus:ring-blue-500 bg-white">
                    </div>
                </template>
            </div>

            {{-- Submit / Cancel — visible only after photos are selected --}}
            <div x-show="previews.length > 0" class="flex gap-3">
                <button type="submit"
                        class="bg-blue-700 text-white px-6 py-2.5 rounded-xl
                               text-sm font-medium hover:bg-blue-800 transition">
                    Upload <span x-text="previews.length"></span> Photo(s)
                </button>
                <button type="button"
                        @click="document.getElementById('upload-panel').classList.add('hidden')"
                        class="border border-gray-200 text-gray-600 px-6 py-2.5
                               rounded-xl text-sm hover:bg-gray-50 transition">
                    Cancel
                </button>
            </div>

        </form>
    </div>
</div>
@endif
@endauth

{{-- ── Photo Grid + Lightbox ──────────────────────────────────────────── --}}
{{-- Alpine.js lightbox() component wraps the grid so clicking any photo  --}}
{{-- opens the full-screen overlay. Keyboard: Esc=close, ←/→=navigate.   --}}
<div x-data="lightbox({{ $photos->count() }})"
     @keydown.escape.window="close()"
     @keydown.arrow-right.window="next()"
     @keydown.arrow-left.window="prev()">

    {{-- Masonry-style photo grid (CSS columns) --}}
    @if($photos->count() > 0)
        <div class="columns-1 md:columns-3 gap-3 space-y-3">
            @foreach($photos as $index => $photo)
                <div class="break-inside-avoid mb-3 group relative">

                    {{-- Clicking a photo opens the lightbox at that index --}}
                    <div class="cursor-pointer" @click="open({{ $index }})">
                        <img src="{{ Storage::url($photo->image_path) }}"
                             class="w-full rounded-xl object-cover
                                    hover:opacity-90 transition duration-200
                                    group-hover:shadow-lg"
                             alt="{{ $photo->caption ?? 'Gallery photo' }}">
                    </div>

                    {{-- Caption, uploader name, and delete button --}}
                    <div class="flex items-center justify-between mt-1 px-1">
                        <div>
                            @if($photo->caption)
                                <p class="text-xs text-gray-500">
                                    {{ $photo->caption }}
                                </p>
                            @endif
                            <p class="text-xs text-gray-400">
                                by {{ $photo->user->name }}
                            </p>
                        </div>

                        {{-- Delete button — visible on hover; owner or admin only --}}
                        @auth
                            @if($photo->user_id === Auth::id() || Auth::user()->isAdmin())
                                <form method="POST"
                                      action="{{ route('gallery.destroy', $photo) }}"
                                      onsubmit="return confirm('Delete this photo?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-xs text-red-400
                                                   hover:text-red-600 transition
                                                   opacity-0 group-hover:opacity-100">
                                        🗑
                                    </button>
                                </form>
                            @endif
                        @endauth
                    </div>

                </div>
            @endforeach
        </div>
    @else
        {{-- Empty state — shown when no photos have been uploaded yet --}}
        <div class="text-center py-16 text-gray-400">
            <p class="text-5xl mb-3">🖼️</p>
            <p class="font-medium">No photos yet.</p>
            @if($canUpload)
                <p class="text-sm mt-1">
                    Be the first to share photos from this event!
                </p>
            @endif
        </div>
    @endif

    {{-- ── Lightbox Overlay ───────────────────────────────────────────── --}}
    {{-- Full-screen dark overlay with the active photo, navigation arrows, --}}
    {{-- a photo counter, and a thumbnail strip at the bottom.             --}}
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-black bg-opacity-90
                flex items-center justify-center p-4"
         @click.self="close()">  {{-- Click outside image to close --}}

        {{-- Close button (top-right) --}}
        <button @click="close()"
                class="absolute top-4 right-4 text-white text-3xl
                       hover:text-gray-300 transition w-10 h-10
                       flex items-center justify-center rounded-full
                       hover:bg-white hover:bg-opacity-10">
            ✕
        </button>

        {{-- Photo counter: "3 / 12" --}}
        <div class="absolute top-4 left-1/2 -translate-x-1/2
                    text-white text-sm bg-black bg-opacity-40
                    px-4 py-1.5 rounded-full">
            <span x-text="current + 1"></span> / <span x-text="total"></span>
        </div>

        {{-- Previous photo button --}}
        <button @click="prev()" x-show="total > 1"
                class="absolute left-4 top-1/2 -translate-y-1/2 text-white
                       text-4xl hover:text-gray-300 transition w-12 h-12
                       flex items-center justify-center rounded-full
                       hover:bg-white hover:bg-opacity-10">
            ‹
        </button>

        {{-- Active photo + caption — one div per photo, only active one shown --}}
        <div class="max-w-5xl max-h-full w-full flex flex-col items-center">
            @foreach($photos as $index => $photo)
                <div x-show="current === {{ $index }}"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100">
                    <img src="{{ Storage::url($photo->image_path) }}"
                         class="max-w-full rounded-lg object-contain shadow-2xl"
                         style="max-height: 75vh;"
                         alt="{{ $photo->caption ?? 'Gallery photo' }}">
                    <div class="text-center mt-3">
                        @if($photo->caption)
                            <p class="text-white text-sm opacity-90">
                                {{ $photo->caption }}
                            </p>
                        @endif
                        <p class="text-gray-400 text-xs mt-1">
                            Uploaded by {{ $photo->user->name }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Next photo button --}}
        <button @click="next()" x-show="total > 1"
                class="absolute right-4 top-1/2 -translate-y-1/2 text-white
                       text-4xl hover:text-gray-300 transition w-12 h-12
                       flex items-center justify-center rounded-full
                       hover:bg-white hover:bg-opacity-10">
            ›
        </button>

        {{-- Thumbnail strip — shown only when there are multiple photos --}}
        @if($photos->count() > 1)
        <div class="absolute bottom-4 left-1/2 -translate-x-1/2
                    flex gap-2 overflow-x-auto max-w-lg px-2">
            @foreach($photos as $index => $photo)
                {{-- Active thumbnail gets white border; others are dimmed --}}
                <button @click="open({{ $index }})"
                        class="flex-shrink-0 w-12 h-12 rounded-lg
                               overflow-hidden border-2 transition"
                        :class="current === {{ $index }}
                            ? 'border-white opacity-100'
                            : 'border-transparent opacity-50 hover:opacity-80'">
                    <img src="{{ Storage::url($photo->image_path) }}"
                         class="w-full h-full object-cover" alt="">
                </button>
            @endforeach
        </div>
        @endif

    </div>
</div>

{{-- ── Alpine.js Scripts ───────────────────────────────────────────────── --}}
<script>
/**
 * lightbox(total)
 * Alpine.js component for the full-screen photo viewer.
 * - open(index)  : opens the overlay at a specific photo
 * - close()      : closes the overlay and restores page scroll
 * - next() / prev(): cycle through photos (wraps around)
 */
function lightbox(total) {
    return {
        isOpen: false,
        current: 0,
        total: total,

        open(index) {
            this.current = index;
            this.isOpen = true;
            document.body.style.overflow = 'hidden'; // Prevent background scroll
        },

        close() {
            this.isOpen = false;
            document.body.style.overflow = ''; // Restore scroll
        },

        next() {
            if (!this.isOpen) return;
            this.current = (this.current + 1) % this.total; // Wrap to first
        },

        prev() {
            if (!this.isOpen) return;
            this.current = (this.current - 1 + this.total) % this.total; // Wrap to last
        }
    }
}

/**
 * photoUpload()
 * Alpine.js component for the photo upload panel.
 * Handles file selection, drag-and-drop, preview generation,
 * individual photo removal, and keeping the hidden file input in sync.
 */
function photoUpload() {
    return {
        previews: [], // Base64 data URLs for thumbnail preview
        files: [],    // Actual File objects to be submitted

        // Called when user selects files via the file picker
        handleFiles(event) {
            const newFiles = Array.from(event.target.files);
            this.addFiles(newFiles);
        },

        // Called when user drops files onto the drop zone
        handleDrop(event) {
            const newFiles = Array.from(event.dataTransfer.files)
                .filter(f => f.type.startsWith('image/')); // Images only
            this.addFiles(newFiles);
        },

        // Add files up to the 10-photo limit, generate previews
        addFiles(newFiles) {
            const remaining = 10 - this.files.length;
            const toAdd = newFiles.slice(0, remaining); // Enforce max 10

            toAdd.forEach(file => {
                this.files.push(file);
                const reader = new FileReader();
                reader.onload = e => this.previews.push(e.target.result);
                reader.readAsDataURL(file); // Generate base64 preview URL
            });

            this.updateFileInput();
        },

        // Remove a single photo by index
        removePhoto(index) {
            this.previews.splice(index, 1);
            this.files.splice(index, 1);
            this.updateFileInput();
        },

        // Sync the hidden <input type="file"> with the current files array.
        // Needed because file inputs are read-only — we use DataTransfer to rebuild.
        updateFileInput() {
            const dt = new DataTransfer();
            this.files.forEach(f => dt.items.add(f));
            this.$refs.fileInput.files = dt.files;
        }
    }
}
</script>

@endsection