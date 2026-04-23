<?php

use Livewire\Component;
use Livewire\Attributes\Layout;

new class extends Component {
    #[Layout('layouts/app')]
    public $kuotas = [
        [
            'provider' => 'Axis',
            'url' => '/kuota/axis',
        ],
        [
            'provider' => 'Indosat',
            'url' => '/kuota/indosat',
        ],
        [
            'provider' => 'Smartfren',
            'url' => '/kuota/smartfren',
        ],
        [
            'provider' => 'Telkomsel',
            'url' => '/kuota/telkomsel',
        ],
        [
            'provider' => 'Tri',
            'url' => '/kuota/tri',
        ],
        [
            'provider' => 'XL',
            'url' => '/kuota/xl',
        ],
        [
            'provider' => 'By.U',
            'url' => '/kuota/by.u',
        ],
    ];
};
?>

<div class="space-y-5">
    <livewire:header title="PUSAT KUOTA" :underline="false" />
    {{-- pulsa --}}
    <div class="">
        <livewire:menu-header title="Pulsa" />
        {{-- list --}}
        <div class="divide-y divide-dashed">
            <livewire:menu text="Daftar harga pulsa" url="/" />
        </div>
    </div>
    {{-- kuota --}}
    <div class="">
        {{-- judul --}}
        <livewire:menu-header title="Kuota" />
        {{-- list --}}
        <div class="divide-y divide-dashed">
            @foreach ($kuotas as $kuota)
                <livewire:menu :text="$kuota['provider']" :url="$kuota['url']" />
                {{-- <a href="{{ $kuota['url'] }}" class="py-5 flex justify-between group">
                    <div class="">{{ $kuota['provider'] }}</div>
                    <div class="group-hover:block group-focus:block hidden">-></div>
                </a> --}}
            @endforeach
        </div>
    </div>
    {{-- Admin corner --}}
    <div class="">
        {{-- judul --}}
        <livewire:menu-header title="Admin corner" />
        {{-- list --}}
        <div class="divide-y divide-dashed">
            <livewire:menu text="Tambah produk" url="/new" />
        </div>
    </div>
</div>
