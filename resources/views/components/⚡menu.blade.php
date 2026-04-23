<?php

use Livewire\Component;

new class extends Component {
    public $text, $textRight, $url;
};
?>

@if (!empty($url))
    <a href="{{ $url }}"
        class="py-5 flex justify-between group hover:px-3 hover:font-bold hover:uppercase hover:italic transition-all ease-in-out">
        <div class="">{{ $text }}</div>
        <div class="group-hover:block group-focus:block hidden"><i data-lucide='arrow-right'></i></div>
    </a>
@else
    <div
        class="py-5 flex justify-between hover:px-3 hover:font-bold hover:uppercase hover:italic transition-all ease-in-out">
        <div class="">{{ $text }}</div>
        <div class="">{{ !empty($textRight) ? $textRight:'' }}</div>
    </div>
@endif
