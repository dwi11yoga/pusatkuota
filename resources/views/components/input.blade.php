@props([
    'id',
    'type' => 'text',
    'model',
    'label' => '',
    'placeholder',
    'error' => null,
    'readonly' => false,
    'dataset' => null,
    'calculatePriceMethod' => null,
])

<div class="w-full">
    {{-- label --}}
    @if ($label)
        <label for="{{ $id }}" class="block text-sm italic">{{ $label }}</label>
    @endif
    {{-- input --}}
    @if ($type === 'select')
        <select wire:model.live='{{ $model }}' id="{{ $id }}"
            @if ($readonly) readonly @endif
            class="h-fit w-full outline-none focus:border-b-2 {{ $error ? 'border-red-500' : '' }}">
            <option value="">{{ $placeholder ?? 'Pilih...' }}</option>
            @foreach ($dataset ?? [] as $option)
                <option value="{{ $option }}">{{ $option }}</option>
            @endforeach
        </select>
    @else
        <input wire:model.live.blur='{{ $model }}' placeholder="{{ $placeholder ?? 'Ketik...' }}"
            list="{{ $dataset }}" id="{{ $id }}" type="{{ $type }}"
            @if ($readonly) readonly @endif
            @if ($calculatePriceMethod) wire:change='{{ $calculatePriceMethod }}' @endif
            class="h-fit w-full outline-none focus:border-b-2 {{ $error ? 'border-red-500' : '' }}">
    @endif

    {{-- error --}}
    @if ($error)
        <div class="text-red-500 text-xs">{{ $error }}</div>
    @endif
</div>
