<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Models\Product;

new class extends Component {
    #[Layout('layouts/app')]
    #[Computed]
    public function pulsaList()
    {
        return Product::distinct('provider')->where('type', 'Pulsa')->pluck('provider');
    }

    #[Computed]
    public function kuotaList()
    {
        return Product::distinct('provider')->where('type', 'Kuota')->pluck('provider');
    }
};
?>

<div class="space-y-5">
    <x-header title="PUSAT KUOTA" :underline="false" />
    {{-- pulsa --}}
    <div class="">
        <x-list-header title="Pulsa" />
        {{-- list --}}
        <div class="divide-y divide-dashed">
            @foreach ($this->pulsaList as $pulsa)
                <x-menu text="{{ $pulsa }}" url="/pulsa/{{ strtolower($pulsa) }}" />
            @endforeach
        </div>
    </div>
    {{-- kuota --}}
    <div class="">
        {{-- judul --}}
        <x-list-header title="Kuota" />
        {{-- list --}}
        <div class="divide-y divide-dashed">
            @foreach ($this->kuotaList as $kuota)
                <x-menu :text="$kuota" url="/kuota/{{ strtolower($kuota) }}" />
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
