<?php

use Livewire\Component;
use App\Models\Product;

new class extends Component {
    public $products, $selected;

    // fungsi hapus produk
    public function deleteProducts()
    {
        $selectedTotal = count($this->selected);
        if ($selectedTotal > 0) {
            Product::whereIn('id', $this->selected)->delete();
            // selanjutnya akan dihandle oleh fungsi onDeletedProducts di parent
            $this->dispatch('onDeletedProducts');
        }
    }
};
?>

<div>
    <div class="fixed top-0 left-0 bg-black opacity-30 w-full h-full"></div>
    <div class="fixed top-1/2 left-1/2 bg-neutral-50 -translate-1/2 p-5 rounded-xl space-y-3 md:w-96 w-[80%]">
        <div class="text-xl flex gap-2 bg-red-400 p-2">
            <div><i data-lucide='triangle-alert'></i></div>
            <div>Hapus Produk?</div>
        </div>
        <div>{{ count($selected) }} produk berikut akan dihapus permanen dan tidak dapat
            dikembalikan.</div>
        <div class="text-sm max-h-30 overflow-y-auto p-1 rounded-sm bg-neutral-100">
            @foreach ($selected as $id)
                <div>{{ $products->firstWhere('id', $id)->name }}</div>
            @endforeach
        </div>
        <div class="flex gap-2 text-base">
            <button wire:click='$parent.$set("deleteSelected", false)'
                class="block w-full p-3 rounded-xl bg-neutral-100 hover:bg-neutral-200">Batal</button>
            <button wire:click='deleteProducts' class="block w-full p-3 rounded-xl bg-red-400 hover:bg-red-500">Ya,
                hapus</button>
        </div>
    </div>
</div>
