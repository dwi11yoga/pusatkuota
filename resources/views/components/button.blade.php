@props([
    'id',
    'type' => null,
    'url' => null,
    'text' => null,
    'icon' => null,
    'hideText' => true,
    'color' => 'light',
    'function' => null,
    'size' => 'normal',
])

@php
    if ($size == 'small') {
        $buttonPadding = 'p-3';
        $iconSize = 'size-4 m-0.5';
        $textSize = 'text-sm';
    } else {
        $buttonPadding = 'p-4';
        $iconSize = 'size-5 m-0.5';
        $textSize = 'text-base';
    }
@endphp

<div wire:key='{{ $id }}' id="{{ $id }}"
    class="relative group w-fit {{ $color == 'light' ? 'text-neutral-800' : 'text-neutral-50' }}">
    {{-- spacer tidak terlihat, hanya untuk ukuran --}}
    <div class="{{ $buttonPadding }} rounded-lg border border-neutral-300 text-nowrap flex gap-5 invisible">
        {{ $slot }}
        @if ($text)
            <div class="{{ $hideText ? 'group-hover:block hidden' : '' }} {{ $textSize }}">{{ $text }}</div>
        @endif
        @if ($icon)
            <i data-lucide='{{ $icon }}' class="{{ $iconSize }}"></i>
        @endif
    </div>
    <div
        class="absolute top-0 left-0 {{ $buttonPadding }} inset w-full h-full rounded-lg {{ $color == 'light' ? 'bg-neutral-200' : 'bg-neutral-700' }}">
    </div>
    @if ($type == 'link')
        <a href="{{ $url }}"
            class="absolute bottom-1.5 right-1 group-active:bottom-0 group-active:right-0 cursor-pointer flex gap-5 group-active:inset-shadow-sm shadow-sm group-active:shadow-none {{ $buttonPadding }} rounded-lg border {{ $color == 'light' ? 'bg-neutral-50 border-neutral-300' : 'bg-neutral-800 border-neutral-700' }} items-center justify-center text-nowrap transition-all ease-in-out">
            {{ $slot }}
            @if ($text)
                <div class="{{ $hideText ? 'group-hover:block hidden' : '' }} {{ $textSize }}">
                    {{ $text }}</div>
            @endif
            @if ($icon)
                <i data-lucide='{{ $icon }}' class="{{ $iconSize }}"></i>
            @endif
        </a>
    @else
        <button type="{{ $type }}" wire:click='{{ $function }}'
            class="absolute bottom-1.5 right-1 group-active:bottom-0 group-active:right-0 cursor-pointer flex gap-5 group-active:inset-shadow-sm shadow-sm group-active:shadow-none {{ $buttonPadding }} rounded-lg border {{ $color == 'light' ? 'bg-neutral-50 border-neutral-300' : 'bg-neutral-800 border-neutral-700' }} items-center justify-center text-nowrap transition-all ease-in-out">
            {{ $slot }}
            @if ($text)
                <div class="{{ $hideText ? 'group-hover:block hidden' : '' }} {{ $textSize }}">
                    {{ $text }}</div>
            @endif
            @if ($icon)
                <i data-lucide='{{ $icon }}' class="{{ $iconSize }}"></i>
            @endif
        </button>
    @endif
</div>
