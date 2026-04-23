<?php

use Livewire\Component;
use Livewire\Attributes\Reactive;

new class extends Component {
    public $id, $model, $type, $label, $placeholder, $dataset, $readonly, $calculatePriceMethod;
    #[Reactive]
    public $error;
};
?>

<div class="">
    @if (!empty($label))
        <label for="{{ $id }}" class="block text-sm italic">{{ $label }}</label>
    @endif
    <input wire:model.live.blur='$parent.{{ $model }}' placeholder="{{ $placeholder ?? 'Ketik...' }}"
        list="{{ $dataset ?? '' }}" id="{{ $id }}" type="{{ $type }}"
        {{ $readonly == true ? 'readonly' : '' }}
        @if ($calculatePriceMethod) wire:change='$parent.{{ $calculatePriceMethod }}' @endif
        class="h-fit w-full outline-none focus:border-b-2 {{ $error ? 'border-red-500' : '' }}">
    @if ($error)
        <div class="text-red-500 text-xs">{{ $error }}</div>
    @endif
</div>
