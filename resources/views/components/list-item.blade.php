@props(['id' => null, 'text', 'textRight', 'onSelect' => false, 'checkboxModel', 'type'])

@if ($onSelect)
    <label for="{{ $checkboxModel . $id }}"
        class="py-5 flex justify-between hover:font-bold hover:uppercase hover:italic transition-all ease-in-out group">
        <div class="flex gap-2">
            <input id="{{ $checkboxModel . $id }}" type="checkbox" wire:model.live='{{ $checkboxModel }}'
                value="{{ $id }}" class="peer hidden">
            <i data-lucide='square-check' class="size-6 hidden peer-checked:block fill-highlighter"></i>
            <i data-lucide='square-dashed' class="size-6 block peer-checked:hidden"></i>
            <div class="peer-checked:ml-3">{{ $text }}</div>
        </div>
        <div class="text-right">{!! !empty($textRight) ? $textRight : '' !!}</div>
    </label>
@else
    <div
        class="py-5 gap-2 flex justify-between items-center hover:font-bold hover:uppercase hover:italic hover:px-3 transition-all ease-in-out group">
        <div class="">
            <span class="">{{ $text }}</span>
        </div>
        <div class="text-right">
            @if (!auth()->user() && $textRight !== 'Tidak tersedia')
                @php
                    $baseUrl = 'https://wa.me/' . env('CONTACT');
                    $message = 'Bang, aku mau beli ' . ucfirst($type) . ' ' . $text . ' dong';
                    $url = $baseUrl . '?text=' . $message;
                @endphp
                <a href="{{ $url }}" target="_blank" title="Beli sekarang!"
                    class="rounded-lg hover:rounded-2xl ease-in-out transition-all px-3 py-1 group-hover:border border-neutral-300 w-fit inline-flex items-center gap-1 hover:inset-shadow-sm active:bg-highlighter">
                    <i data-lucide='shopping-basket' class="size-5 group-hover:inline hidden"></i>
                    {{$textRight}}
                </a>
            @else
                {!! !empty($textRight) ? $textRight : '' !!}
            @endif
        </div>
    </div>
@endif
