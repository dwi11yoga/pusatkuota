<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Models\Product;

new class extends Component {
    public $data;
    public function mount()
    {
        // dapatkan daftar type produk
        $types = Product::distinct('type')->pluck('type');
        foreach ($types as $type) {
            $data[$type] = Product::where('type', $type)->distinct('provider')->pluck('provider');
        }
        $this->data = $data;
        // dd($data);
    }

    // fungsi logout
    public function logout()
    {
        Auth::logout();
        session()->regenerate();
        return redirect()->to('/')->with('success', 'You have been logged out. See you next time!');
    }
};
?>

<div class="space-y-5">
    <x-header title="PUSAT KUOTA" :underline="false" />
    @foreach ($data as $type => $providers)
        <div class="">
            <x-list-header title="{{ $type }}" />
            {{-- list --}}
            <div class="divide-y divide-dashed">
                @foreach ($providers as $provider)
                    <x-menu text="{{ $provider }}" url="/{{ strtolower($type) }}/{{ strtolower($provider) }}" />
                @endforeach
            </div>
        </div>
    @endforeach
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
</div>
