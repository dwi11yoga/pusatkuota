@props(['closeFunction', 'resetFunction' => null])

<div id="filter" class="border border-neutral-300 p-4 rounded-lg space-y-2 bg-neutral-50 shadow-sm">
    {{ $slot }}
    {{-- tombol --}}
    <div class="flex justify-end mt-4">
        <div class="flex gap-1">
            {{-- toggle reset filter --}}
            @if ($resetFunction)
                <x-button id="resetFilter" type="button" text="Reset" icon="refresh-ccw" size="small"
                    function="{{ $resetFunction }}" />
                {{-- <button type="button" wire:click='{{ $resetFunction }}'
                    class="hover:bg-highlighter hover:rounded-4xl border-2 border-dashed border-neutral-800 p-2 rounded-xl flex items-center gap-2 group transition-all ease-in-out w-fit">
                    <div class="group-hover:block hidden">Reset</div>
                    <i data-lucide='refresh-ccw' class="size-5"></i>
                </button> --}}
            @endif
            {{-- toggle tutup filter --}}
            <x-button id="closeFilter" type="button" text="Tutup" icon="chevron-down" size="small"
                function="{{ $closeFunction }}" />
            {{-- <button type="button" wire:click='{{ $closeFunction }}'
                class="hover:bg-highlighter hover:rounded-4xl border-2 border-dashed border-neutral-800 p-2 rounded-xl flex items-center gap-2 group transition-all ease-in-out w-fit">
                <div class="group-hover:block hidden">Tutup</div>
                <i data-lucide='chevron-down' class="size-5"></i>
            </button> --}}
        </div>
    </div>
</div>
