<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

@if ($paginator->hasPages())
    <div class="py-1 flex items-center justify-between">
        <label>Halaman {{$paginator->currentPage() }}</label>
        <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between">

            @if ($paginator->onFirstPage())
                <span class="p-1 hover:bg-neutral-200 rounded-sm cursor-not-allowed text-neutral-500">
                    {{-- {!! __('pagination.previous') !!} --}}
                    <i data-lucide='chevron-left' class="size-5"></i>
                </span>
            @else
                <button wire:click='previousPage' rel="prev" class="p-1 hover:bg-neutral-200 rounded-sm">
                    {{-- {!! __('pagination.previous') !!} --}}
                    <i data-lucide='chevron-left' class="size-5"></i>
                </button>
            @endif

            @if ($paginator->hasMorePages())
                <button wire:click='nextPage' rel="next" class="p-1 hover:bg-neutral-200 rounded-sm">
                    {{-- {!! __('pagination.next') !!} --}}
                    <i data-lucide='chevron-right' class="size-5"></i>
                </button>
            @else
                <span class="p-1 hover:bg-neutral-200 rounded-sm cursor-not-allowed text-neutral-500">
                    {{-- {!! __('pagination.next') !!} --}}
                    <i data-lucide='chevron-right' class="size-5"></i>
                </span>
            @endif

        </nav>
    </div>
@endif
