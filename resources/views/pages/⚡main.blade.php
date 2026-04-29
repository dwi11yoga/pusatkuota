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

    // fungsi logout
    public function logout()
    {
        Auth::logout();
        session()->regenerate();
        return redirect()->to('/')->with('success', 'Anda berhasil logout');
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
        <x-list-header title="Admin corner" />
        {{-- list --}}
        <div class="divide-y divide-dashed">
            @auth
                <x-menu text="Tambah produk" url="/new" />
                {{-- logout --}}
                <button wire:click='logout' class="w-full" id="logout">
                    <x-menu text="Keluar sebagai admin" url="#logout" />
                </button>
            @else
                {{-- login --}}
                <x-menu text="Masuk sebagai admin" url="/auth" />
            @endauth
        </div>
    </div>

    {{-- tampilkan info session --}}
    @session('success')
        <div id="sessionSuccess" class="fixed bottom-0 right-5 bg-highlighter p-3 rounded-l-2xl rounded-t-2xl">
            {{ session('success') }}
        </div>
        {{-- hilangkan pesan sukses setelah 3 detik --}}
        <script>
            setTimeout(() => document.getElementById('sessionSuccess').remove(), 3000);
        </script>
    @endsession
</div>
