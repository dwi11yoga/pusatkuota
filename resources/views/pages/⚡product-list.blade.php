<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\Product;

new class extends Component {
    use WithPagination;
    //
    public $type, $provider;

    // filter
    #[Url]
    public $sort = 'Murah-mahal';
    #[Url]
    #[Validate('nullable')]
    public $search;
    #[Url]
    #[Validate('nullable')]
    public $category;
    #[Url]
    #[Validate('nullable|integer')]
    public $minPrice;
    #[Url]
    #[Validate('nullable|integer')]
    public $maxPrice;
    #[Url]
    #[Validate('nullable|integer')]
    public $limit;
    #[Url]
    #[Validate('in:all,available,unavailable')]
    public $status = 'all';

    #[Computed]
    public function categoryOptions()
    {
        return Product::distinct()->where('type', $this->type)->where('provider', $this->provider)->pluck('category');
    }

    public $filterOpen = false;

    // dapatkan data produk
    // public $products;
    #[Computed]
    public function products()
    {
        $data = Product::select();
        // jika ada type
        if (!empty($this->type)) {
            $data = $data->where('type', $this->type);
        }
        // jika ada provider
        if ($this->provider) {
            $data = $data->where('provider', $this->provider);
        }
        // kategori
        if ($this->category) {
            $data = $data->where('category', $this->category);
        }
        // pencarian
        if ($this->search) {
            $data = $data->whereLike('name', '%' . $this->search . '%');
        }
        // harga terendah
        if ($this->minPrice) {
            $data = $data->whereRaw('(base_price + real_profit) >= ?', [$this->minPrice]);
        }
        // harga terendah
        if ($this->maxPrice) {
            $data = $data->whereRaw('(base_price + real_profit) <= ?', [$this->maxPrice]);
        }

        // status
        if ($this->status === 'available') {
            $data = $data->where('available', 1);
        }
        if ($this->status === 'unavailable') {
            $data = $data->where('available', 0);
        }

        // urutkan
        if ($this->sort == 'A-Z') {
            $data = $data->orderBy('name');
        } elseif ($this->sort == 'Z-A') {
            $data = $data->orderByDesc('name');
        } elseif ($this->sort == 'Murah-mahal') {
            $data = $data->orderByRaw('(base_price + real_profit) ASC');
        } elseif ($this->sort == 'Mahal-murah') {
            $data = $data->orderByRaw('(base_price + real_profit) DESC');
        } elseif ($this->sort == 'Terlama-terbaru') {
            $data = $data->orderBy('updated_at');
        } elseif ($this->sort == 'Terbaru-terlama') {
            $data = $data->orderByDesc('updated_at');
        }
        // jika ada limit, maka poaginate
        if ($this->limit) {
            $data = $data->simplePaginate($this->limit);
            // $data = $data->limit($this->limit);
        } else {
            $data = $data->get();
        }
        // $this->products = $data;
        return $data;
    }

    // reset filter
    public function resetFilter()
    {
        // kembalikan ke nilai default
        $this->sort = 'Murah-mahal';
        $this->search = null;
        $this->category = null;
        $this->minPrice = null;
        $this->maxPrice = null;
        $this->limit = null;

        // ambil kembali produk
        $this->products();
    }

    // fitur select
    public $onSelect = false;
    public array $selected = [];
    // pilih semua
    public function selectAll()
    {
        $this->selected = $this->products->pluck('id')->toArray();
    }
    public function unselect()
    {
        $this->selected = [];
    }
    public function selectInverse()
    {
        $selected = $this->selected;
        $unselected = [];
        foreach ($this->products as $product) {
            $check = in_array($product->id, $selected);
            if (!$check) {
                $unselected[] = $product->id;
            }
        }
        // dd($selected, $unselected);
        $this->selected = $unselected;
    }
    public function closeSelect()
    {
        $this->onSelect = false;
        $this->selected = [];
    }

    // AKSI SELECT
    // ubah status
    public function setAvailable()
    {
        Product::whereIn('id', $this->selected)->update(['available' => 1]);
    }
    public function setUnavailable()
    {
        Product::whereIn('id', $this->selected)->update(['available' => 0]);
    }

    // hapus data yang dipilih
    public function deleteSelected()
    {
        Product::whereIn('id', $this->selected)->delete();
        $this->selected = [];
    }
};
?>

<div>
    <livewire:header title="{{ $type . ' ' . $provider }}" />
    {{-- daftar produk --}}
    <div class="divide-y divide-dashed">
        @if ($this->products->isEmpty())
            <x-list-item text="Belum ada produk." />
        @else
            @foreach ($this->products as $product)
                <x-list-item wire:key='{{ $product->id }}' id="{{ $product->id }}" text="{{ $product->name }}"
                    textRight="{{ $product->available == 1 ? money_format($product->base_price + $product->real_profit) : 'Tidak tersedia' }}"
                    :onSelect="$onSelect" checkboxModel="selected" />
            @endforeach
            <div class="text-sm py-3">Silahkan bisa langsung hubungi admin ya gaes 🤙.</div>
        @endif
    </div>

    {{-- aksi --}}
    <div class="fixed bottom-5 right-5 text-base space-y-2 flex flex-col items-end max-w-80">
        {{-- filter --}}
        @if ($onSelect != true)
            @if ($filterOpen)
                <div id="filter" class="border-2 border-dashed p-4 rounded-xl space-y-4 bg-neutral-50">
                    {{-- sort --}}
                    <div class="">
                        <div class="font-bold">Sort</div>
                        <div class="divide-y divide-dashed">
                            <div class="py-1 flex items-center justify-between">
                                <div class="">Nama</div>
                                <div class="-space-x-2 flex gap-1">
                                    <label for="A-Z" title="A-Z"
                                        class="p-1 hover:bg-neutral-200 rounded-sm {{ $sort == 'A-Z' ? 'bg-highlighter' : '' }}">
                                        <i data-lucide='arrow-down-a-z' class="size-5"></i>
                                        <input wire:model.live='sort' type="radio" id="A-Z" value="A-Z"
                                            class="hidden">
                                    </label>
                                    <label for="Z-A" title="Z-A"
                                        class="p-1 hover:bg-neutral-200 rounded-sm {{ $sort == 'Z-A' ? 'bg-highlighter' : '' }}">
                                        <i data-lucide='arrow-down-z-a' class="size-5"></i>
                                        <input wire:model.live='sort' type="radio" id="Z-A" value="Z-A"
                                            class="hidden">
                                    </label>
                                </div>
                            </div>
                            <div class="py-1 flex items-center justify-between">
                                <div class="">Harga</div>
                                <div class="-space-x-2 flex gap-1">
                                    <label for="Murah-mahal" title="Murah-mahal"
                                        class="p-1 hover:bg-neutral-200 rounded-sm {{ $sort == 'Murah-mahal' ? 'bg-highlighter' : '' }}">
                                        <i data-lucide='arrow-down-0-1' class="size-5"></i>
                                        <input wire:model.live='sort' type="radio" id="Murah-mahal"
                                            value="Murah-mahal" class="hidden">
                                    </label>
                                    <label for="Mahal-murah" title="Mahal-murah"
                                        class="p-1 hover:bg-neutral-200 rounded-sm {{ $sort == 'Mahal-murah' ? 'bg-highlighter' : '' }}">
                                        <i data-lucide='arrow-down-1-0' class="size-5"></i>
                                        <input wire:model.live='sort' type="radio" id="Mahal-murah"
                                            value="Mahal-murah" class="hidden">
                                    </label>
                                </div>
                            </div>
                            <div class="py-1 flex items-center justify-between">
                                <div class="">Ditambahkan</div>
                                <div class="-space-x-2 flex gap-1">
                                    <label for="Terlama-terbaru" title="Terlama-terbaru"
                                        class="p-1 hover:bg-neutral-200 rounded-sm {{ $sort == 'Terlama-terbaru' ? 'bg-highlighter' : '' }}">
                                        <i data-lucide='calendar-arrow-down' class="size-5"></i>
                                        <input wire:model.live='sort' type="radio" id="Terlama-terbaru"
                                            value="Terlama-terbaru" class="hidden">
                                    </label>
                                    <label for="Terbaru-terlama" title="Terbaru-terlama"
                                        class="p-1 hover:bg-neutral-200 rounded-sm {{ $sort == 'Terbaru-terlama' ? 'bg-highlighter' : '' }}">
                                        <i data-lucide='calendar-arrow-up' class="size-5"></i>
                                        <input wire:model.live='sort' type="radio" id="Terbaru-terlama"
                                            value="Terbaru-terlama" class="hidden">
                                    </label>
                                </div>
                            </div>
                        </div>
                        {{-- filter --}}
                        <div class="">
                            <div class="font-bold">Filter</div>
                            <div class="divide-y divide-dashed">
                                <div class="py-1">
                                    <div class="flex gap-2">
                                        <label for="search">Cari</label>
                                        <x-input wire:key='filter-search' id="search" model="search"
                                            type="text" />
                                    </div>
                                    @error('search')
                                        <div class="text-xs text-red-500">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="py-1">
                                    <div class="flex gap-2">
                                        <label for="category">Kategori</label>
                                        <x-input id="category" model="category" type="select" :dataset="$this->categoryOptions"
                                            placeholder="Semua data" />
                                    </div>
                                    @error('category')
                                        <div class="text-xs text-red-500">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="py-1">
                                    <div class="flex gap-2">
                                        <label for="minPrice">Harga</label>
                                        <x-input id="minPrice" model="minPrice" type="number"
                                            placeholder="Min..." />
                                        <div class="">-</div>
                                        <x-input id="maxPrice" model="maxPrice" type="number"
                                            placeholder="Max..." />
                                    </div>
                                    @error('price')
                                        <div class="text-xs text-red-500">{{ $message }}</div>
                                    @enderror
                                </div>
                                {{-- status --}}
                                <div class="py-1">
                                    <div class="gap-2">
                                        <div>Status</div>
                                        <label for="statusAll"
                                            class="inline-flex items-center gap-1 text-neutral-700 hover:text-black p-1 rounded-md {{ $status == 'all' ? 'bg-highlighter' : '' }} hover:bg-neutral-200">
                                            <input type="radio" wire:model.live='status' id="statusAll"
                                                value="all" hidden>
                                            <i data-lucide='layers' class="size-5"></i>
                                            <span>Semua</span>
                                        </label>
                                        <label for="statusAvailable"
                                            class="inline-flex items-center gap-1 text-neutral-700 hover:text-black p-1 rounded-md {{ $status == 'available' ? 'bg-highlighter' : '' }} hover:bg-neutral-200">
                                            <input type="radio" wire:model.live='status' id="statusAvailable"
                                                value="available" hidden>
                                            <i data-lucide='circle-check' class="size-5"></i>
                                            <span>Tersedia</span>
                                        </label>
                                        <label for="statusUnavailable"
                                            class="inline-flex items-center gap-1 text-neutral-700 hover:text-black p-1 rounded-md {{ $status == 'unavailable' ? 'bg-highlighter' : '' }} hover:bg-neutral-200">
                                            <input type="radio" wire:model.live='status' id="statusUnavailable"
                                                value="unavailable" hidden>
                                            <i data-lucide='circle-x' class="size-5"></i>
                                            <span>Tidak tersedia</span>
                                        </label>
                                    </div>
                                    @error('status')
                                        <div class="text-xs text-red-500">{{ $message }}</div>
                                    @enderror
                                </div>
                                {{-- paginate --}}
                                <div class="py-1">
                                    <div class="flex gap-2">
                                        <label for="limit">Limit</label>
                                        <x-input id="limit" model="limit" type="number" />
                                    </div>
                                    @error('limit')
                                        <div class="text-xs text-red-500">{{ $message }}</div>
                                    @enderror
                                </div>
                                {{-- paginate --}}
                                @if ($limit)
                                    {{ $this->products->links('components.pagination.⚡simple') }}
                                @endif
                            </div>
                        </div>
                    </div>
                    {{-- tombol --}}
                    <div class="flex justify-end">
                        <div class="flex gap-1">
                            {{-- toggle reset filter --}}
                            <button type="button" wire:click='resetFilter'
                                class="hover:bg-highlighter hover:rounded-4xl border-2 border-dashed border-neutral-800 p-2 rounded-xl flex items-center gap-2 group transition-all ease-in-out w-fit">
                                <div class="group-hover:block hidden">Reset</div>
                                <i data-lucide='refresh-ccw' class="size-5"></i>
                            </button>
                            {{-- toggle tutup filter --}}
                            <button type="button" wire:click='$toggle("filterOpen")' {{-- onclick="toggleClass('filter', 'hidden'); toggleClass('filterToggle', 'hidden')" --}}
                                class="hover:bg-highlighter hover:rounded-4xl border-2 border-dashed border-neutral-800 p-2 rounded-xl flex items-center gap-2 group transition-all ease-in-out w-fit">
                                <div class="group-hover:block hidden">Tutup</div>
                                <i data-lucide='chevron-down' class="size-5"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @else
                {{-- toggle filter --}}
                <button id="filterToggle" wire:click='$toggle("filterOpen")'
                    class="hover:bg-highlighter bg-neutral-50 hover:rounded-4xl border-2 border-dashed border-neutral-800 p-4 rounded-xl flex items-center gap-2 group transition-all ease-in-out w-fit">
                    <div class="group-hover:block hidden">Filter</div>
                    <i data-lucide='funnel' class="size-5"></i>
                </button>
            @endif
        @endif
        {{-- tombol pilih --}}
        @if ($onSelect)
            <div id="filter" class="border-2 border-dashed p-4 rounded-xl space-y-4 bg-neutral-50">
                <div class="divide-y divide-dashed">
                    {{-- menu pilih --}}
                    <div class="py-2 space-y-2">
                        <div class="font-bold">Pilih</div>
                        <div class="">
                            <button wire:click='selectAll'
                                class="p-1 hover:bg-highlighter rounded-md inline-flex items-center gap-1">
                                <i data-lucide='check-check' class="size-5"></i>
                                <span class="">Semua</span>
                            </button>
                            <button wire:click="unselect"
                                class="p-1 hover:bg-highlighter rounded-md inline-flex items-center gap-1">
                                <i data-lucide='square-dashed' class="size-5"></i>
                                <span class="">Batalkan</span>
                            </button>
                            <button wire:click="selectInverse"
                                class="p-1 hover:bg-highlighter rounded-md inline-flex items-center gap-1">
                                <i data-lucide='refresh-ccw' class="size-5"></i>
                                <span class="text-nowrap">Balik pilihan</span>
                            </button>
                        </div>
                    </div>

                    {{-- jumlah dipilih --}}
                    <div class="py-2 space-y-2">
                        <div class="font-bold">Aksi</div>
                        <div class="">
                            <button class="p-1 inline-flex hover:bg-highlighter rounded-md items-center gap-1">
                                <i data-lucide='Pencil' class="size-5"></i>
                                <span class="">Edit</span>
                            </button>
                            <button wire:click='deleteSelected'
                                class="p-1 inline-flex hover:bg-highlighter rounded-md items-center gap-1">
                                <i data-lucide='trash' class="size-5"></i>
                                <span class="">Hapus</span>
                            </button>
                            <button wire:click='setAvailable'
                                class="p-1 inline-flex hover:bg-highlighter rounded-md items-center gap-1">
                                <i data-lucide='eye' class="size-5"></i>
                                <span class="">Tersedia</span>
                            </button>
                            <button wire:click='setUnavailable'
                                class="p-1 inline-flex hover:bg-highlighter rounded-md items-center gap-1">
                                <i data-lucide='eye-off' class="size-5"></i>
                                <span class="">Tidak tersedia</span>
                            </button>
                        </div>
                    </div>
                </div>
                {{-- tombol --}}
                <div class="flex justify-between items-center">
                    <div class="text-sm">
                        Dipilih <span class="font-bold">{{ count($selected) }}</span>
                    </div>
                    {{-- toggle tutup filter --}}
                    <button type="button" wire:click='closeSelect'
                        class="hover:bg-highlighter hover:rounded-4xl border-2 border-dashed border-neutral-800 p-2 rounded-xl flex items-center gap-2 group transition-all ease-in-out w-fit">
                        <div class="group-hover:block hidden">Tutup</div>
                        <i data-lucide='chevron-down' class="size-5"></i>
                    </button>
                </div>
            </div>
        @else
            <button wire:click='$toggle("onSelect")'
                class="hover:bg-highlighter {{ $onSelect ? 'bg-highlighter' : 'bg-neutral-50' }} hover:rounded-4xl border-2 border-dashed border-neutral-800 p-4 rounded-xl flex items-center gap-2 group transition-all ease-in-out w-fit">
                @if ($onSelect)
                    <div class="group-hover:block hidden">Tutup pilih</div>
                    <i data-lucide='x' class="size-5"></i>
                @else
                    <div class="group-hover:block hidden">Pilih</div>
                    <i data-lucide='square-check' class="size-5"></i>
                @endif
            </button>
        @endif
        @if ($onSelect)
            {{-- <div
                class="bg-neutral-50 hover:rounded-4xl border-2 border-dashed border-neutral-800 {{ count($selected) < 10 ? 'px-5 py-4' : 'p-4' }} rounded-xl flex items-center gap-2 group transition-all ease-in-out w-fit">
                <div class="group-hover:block hidden">Dipilih:</div>
                <div class="">{{ count($selected) }}</div>
            </div>
            <button
                class="hover:bg-highlighter bg-neutral-50 hover:rounded-4xl border-2 border-dashed border-neutral-800 p-4 rounded-xl flex items-center gap-2 group transition-all ease-in-out w-fit">
                <div class="group-hover:block hidden">Tidak tersedia</div>
                <i data-lucide='eye-off' class="size-5"></i>
            </button>
            <button
                class="hover:bg-highlighter bg-neutral-50 hover:rounded-4xl border-2 border-dashed border-neutral-800 p-4 rounded-xl flex items-center gap-2 group transition-all ease-in-out w-fit">
                <div class="group-hover:block hidden">Hapus</div>
                <i data-lucide='trash' class="size-5"></i>
            </button> --}}
        @endif
        {{-- tambah --}}
        @if ($onSelect !== true)
            <a href="/new?{{ http_build_query([
                'type' => ucfirst($type),
                'provider' => ucfirst($provider),
                'category' => ucfirst($category),
            ]) }}"
                class="hover:bg-highlighter bg-neutral-50 hover:rounded-4xl border-2 border-dashed border-neutral-800 p-4 rounded-xl flex items-center gap-2 group transition-all ease-in-out w-fit">
                <div class="group-hover:block hidden">Tambah</div>
                <i data-lucide='plus' class="size-5"></i>
            </a>
        @endif
    </div>
</div>
