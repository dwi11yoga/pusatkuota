@props(['id', 'model', 'value', 'icon', 'text'])

<div class="inline-flex">
    <input type="radio" wire:model.live='{{ $model }}' id="{{ $id }}" value="{{ $value }}" class="hidden peer">
    <label for="{{ $id }}"
        class="flex items-center gap-1 text-neutral-700 hover:text-black p-1 rounded-md peer-checked:bg-highlighter hover:bg-neutral-200">
        <i data-lucide='{{ $icon }}' class="size-5"></i>
        <span>{{ $text }}</span>
    </label>
</div>
