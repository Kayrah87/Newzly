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
    <div class="flex flex-wrap items-center gap-1 border border-ink/20 bg-white p-2">
        <button type="button" @click="run(c => c.toggleBold())" :class="{ 'bg-ink/5 text-press-600': isActive('bold') }" class="rounded px-2 py-1 text-sm font-semibold text-ink-soft hover:bg-ink/5 hover:text-press-600">B</button>
        <button type="button" @click="run(c => c.toggleItalic())" :class="{ 'bg-ink/5 text-press-600': isActive('italic') }" class="rounded px-2 py-1 text-sm italic text-ink-soft hover:bg-ink/5 hover:text-press-600">I</button>
        <button type="button" @click="run(c => c.toggleStrike())" :class="{ 'bg-ink/5 text-press-600': isActive('strike') }" class="rounded px-2 py-1 text-sm line-through text-ink-soft hover:bg-ink/5 hover:text-press-600">S</button>
        <span class="mx-1 h-5 w-px bg-ink/20"></span>
        <button type="button" @click="run(c => c.toggleHeading({ level: 2 }))" :class="{ 'bg-ink/5 text-press-600': isActive('heading', { level: 2 }) }" class="rounded px-2 py-1 text-sm font-semibold text-ink-soft hover:bg-ink/5 hover:text-press-600">H2</button>
        <button type="button" @click="run(c => c.toggleHeading({ level: 3 }))" :class="{ 'bg-ink/5 text-press-600': isActive('heading', { level: 3 }) }" class="rounded px-2 py-1 text-sm font-semibold text-ink-soft hover:bg-ink/5 hover:text-press-600">H3</button>
        <span class="mx-1 h-5 w-px bg-ink/20"></span>
        <button type="button" @click="run(c => c.toggleBulletList())" :class="{ 'bg-ink/5 text-press-600': isActive('bulletList') }" class="rounded px-2 py-1 text-sm text-ink-soft hover:bg-ink/5 hover:text-press-600">&bull; List</button>
        <button type="button" @click="run(c => c.toggleOrderedList())" :class="{ 'bg-ink/5 text-press-600': isActive('orderedList') }" class="rounded px-2 py-1 text-sm text-ink-soft hover:bg-ink/5 hover:text-press-600">1. List</button>
        <button type="button" @click="run(c => c.toggleBlockquote())" :class="{ 'bg-ink/5 text-press-600': isActive('blockquote') }" class="rounded px-2 py-1 text-sm text-ink-soft hover:bg-ink/5 hover:text-press-600">&ldquo;&rdquo;</button>
        <span class="mx-1 h-5 w-px bg-ink/20"></span>
        <button type="button" @click="setLink()" :class="{ 'bg-ink/5 text-press-600': isActive('link') }" class="rounded px-2 py-1 text-sm underline text-ink-soft hover:bg-ink/5 hover:text-press-600">Link</button>
        <button type="button" @click="run(c => c.unsetAllMarks().clearNodes())" class="rounded px-2 py-1 text-sm text-ink-soft hover:bg-ink/5 hover:text-press-600">Clear</button>
    </div>

    <div
        x-ref="editor"
        class="min-h-[18rem] border border-t-0 border-ink/20 bg-white p-3 focus-within:border-press-500 focus-within:ring-1 focus-within:ring-press-500"
    ></div>

    <textarea x-ref="input" name="{{ $name }}" class="hidden">{{ $value }}</textarea>
</div>
