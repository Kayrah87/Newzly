@props([
    'name',
    'value' => '',
])

{{--
    Self-hosted Tiptap rich-text editor (replaces the TinyMCE CDN integration).
    Renders a toolbar + editable surface and mirrors the HTML into a hidden
    <textarea name="{{ $name }}"> so the form still posts `content` as HTML.
    Client-side `required` is intentionally omitted (a hidden control can't be
    focused for validation); content is validated server-side.
--}}
<div
    x-data="wysiwyg"
    wire:ignore
    {{ $attributes->merge(['class' => 'mt-1']) }}
>
    <div class="flex flex-wrap items-center gap-1 rounded-t-md border border-gray-300 bg-gray-50 p-2">
        <button type="button" @click="run(c => c.toggleBold())" :class="{ 'bg-gray-300': isActive('bold') }" class="rounded px-2 py-1 text-sm font-semibold hover:bg-gray-200">B</button>
        <button type="button" @click="run(c => c.toggleItalic())" :class="{ 'bg-gray-300': isActive('italic') }" class="rounded px-2 py-1 text-sm italic hover:bg-gray-200">I</button>
        <button type="button" @click="run(c => c.toggleStrike())" :class="{ 'bg-gray-300': isActive('strike') }" class="rounded px-2 py-1 text-sm line-through hover:bg-gray-200">S</button>
        <span class="mx-1 h-5 w-px bg-gray-300"></span>
        <button type="button" @click="run(c => c.toggleHeading({ level: 2 }))" :class="{ 'bg-gray-300': isActive('heading', { level: 2 }) }" class="rounded px-2 py-1 text-sm font-semibold hover:bg-gray-200">H2</button>
        <button type="button" @click="run(c => c.toggleHeading({ level: 3 }))" :class="{ 'bg-gray-300': isActive('heading', { level: 3 }) }" class="rounded px-2 py-1 text-sm font-semibold hover:bg-gray-200">H3</button>
        <span class="mx-1 h-5 w-px bg-gray-300"></span>
        <button type="button" @click="run(c => c.toggleBulletList())" :class="{ 'bg-gray-300': isActive('bulletList') }" class="rounded px-2 py-1 text-sm hover:bg-gray-200">&bull; List</button>
        <button type="button" @click="run(c => c.toggleOrderedList())" :class="{ 'bg-gray-300': isActive('orderedList') }" class="rounded px-2 py-1 text-sm hover:bg-gray-200">1. List</button>
        <button type="button" @click="run(c => c.toggleBlockquote())" :class="{ 'bg-gray-300': isActive('blockquote') }" class="rounded px-2 py-1 text-sm hover:bg-gray-200">&ldquo;&rdquo;</button>
        <span class="mx-1 h-5 w-px bg-gray-300"></span>
        <button type="button" @click="setLink()" :class="{ 'bg-gray-300': isActive('link') }" class="rounded px-2 py-1 text-sm underline hover:bg-gray-200">Link</button>
        <button type="button" @click="run(c => c.unsetAllMarks().clearNodes())" class="rounded px-2 py-1 text-sm hover:bg-gray-200">Clear</button>
    </div>

    <div
        x-ref="editor"
        class="min-h-[18rem] rounded-b-md border border-t-0 border-gray-300 bg-white p-3 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500"
    ></div>

    <textarea x-ref="input" name="{{ $name }}" class="hidden">{{ $value }}</textarea>
</div>
