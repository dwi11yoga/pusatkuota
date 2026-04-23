<?php

use Livewire\Component;

new class extends Component {
    public $title;
};
?>

<div class="">
    <div class="text-4xl mb-2 font-bold italic uppercase">{{ $title }}</div>
    <div class="border border-dashed"></div>
</div>
