@props(['id' => null, 'text', 'textRight', 'onSelect' => false, 'checkboxModel'])

@if ($onSelect)
    <label for="{{ $checkboxModel . $id }}"
        class="py-5 flex justify-between hover:font-bold hover:uppercase hover:italic transition-all ease-in-out group">
        <div class="flex gap-2">
            <input id="{{ $checkboxModel . $id }}" type="checkbox" wire:model.live='{{ $checkboxModel }}' value="{{ $id }}" class="peer hidden">
            <i data-lucide='square-check' class="size-6 hidden peer-checked:block fill-highlighter"></i>
            <i data-lucide='square-dashed' class="size-6 block peer-checked:hidden"></i>
            <div class="peer-checked:bg-highlighter peer-checked:ml-3">{{ $text }}</div>
        </div>
        <div class="">{{ !empty($textRight) ? $textRight : '' }}</div>
    </label>
@else
    <div class="py-5 flex justify-between hover:font-bold hover:uppercase hover:italic hover:px-3 transition-all ease-in-out">
        <div class="flex gap-2 items-center">
            <div class="">{{ $text }}</div>
        </div>
        <div class="">{{ !empty($textRight) ? $textRight : '' }}</div>
    </div>
@endif
