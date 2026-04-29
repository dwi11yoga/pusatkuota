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
    public $sort = 'Termurah';
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
    // mode tampilan
    #[Url]
    public $adminView = false;
    public function mount()
    {
        if (auth()->user()) {
            $this->adminView = true;
        }
    }

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
        } elseif ($this->sort == 'Termurah') {
            $data = $data->orderByRaw('(base_price + real_profit) ASC');
        } elseif ($this->sort == 'Termahal') {
            $data = $data->orderByRaw('(base_price + real_profit) DESC');
        } elseif ($this->sort == 'Terlama') {
            $data = $data->orderBy('updated_at');
        } elseif ($this->sort == 'Terbaru') {
            $data = $data->orderByDesc('updated_at');
        }
        // jika ada limit, maka poaginate
        if ($this->limit) {
            $data = $data->simplePaginate($this->limit);
            // $data = $data->limit($this->limit);
        } else {
            $data = $data->get();
        }
        return $data;
    }

    // reset filter
    public function resetFilter()
    {
        // kembalikan ke nilai default
        $this->adminView = false;
        $this->sort = 'Termurah';
        $this->search = null;
        $this->category = null;
        $this->minPrice = null;
        $this->maxPrice = null;
        $this->limit = null;
        $this->status = 'all';

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
                <div class="relative">
                    @php
                        $sellingPrice = money_format($product->base_price + $product->real_profit);
                        $priceDetail =
                            '(' .
                            money_format($product->base_price) .
                            '+' .
                            money_format($product->expected_profit) .
                            ')';
                        $textRight =
                            $product->available == 1
                                ? ($adminView
                                    ? $sellingPrice . '<br>' . $priceDetail
                                    : $sellingPrice)
                                : 'Tidak tersedia';
                    @endphp
                    <x-list-item wire:key='{{ $product->id }}' id="{{ $product->id }}" text="{{ $product->name }}"
                        :textRight="$textRight" :onSelect="$onSelect" checkboxModel="selected" />
                </div>
            @endforeach
            <div class="text-sm py-3">Pembelian/paket internet lainnya bisa langsung hubungi admin 🤙.</div>
        @endif
    </div>

    {{-- aksi --}}
    <div class="fixed bottom-5 right-5 text-base space-y-2 flex flex-col items-end max-w-80">
        {{-- filter --}}
        @if ($onSelect != true)
            @if ($filterOpen)
                <div id="filter" class="border-2 border-dashed p-4 rounded-xl space-y-2 bg-neutral-50">
                    <div class="space-y-4 max-h-96 overflow-auto">
                        {{-- Mode tampilan --}}
                        @auth
                            <div class="">
                                <div class="font-bold">Mode tampilan</div>
                                <div class="">
                                    <x-radio id="adminView-true" model="adminView" value="1" icon="user-lock"
                                        text="Pengurus" />
                                    <x-radio id="adminView-false" model="adminView" value="0" icon="user"
                                        text="Pembeli" />
                                </div>
                            </div>
                        @endauth
                        {{-- sort --}}
                        <div class="">
                            <div class="font-bold">Sort</div>
                            <div class="">
                                <x-radio id="A-Z" model="sort" value="A-Z" icon="arrow-down-a-z"
                                    text="A-Z" />
                                <x-radio id="Z-A" model="sort" value="Z-A" icon="arrow-down-z-a"
                                    text="Z-A" />
                                <x-radio id="Termurah" model="sort" value="Termurah" icon="arrow-down-0-1"
                                    text="Termurah" />
                                <x-radio id="Termahal" model="sort" value="Termahal" icon="arrow-down-1-0"
                                    text="Termahal" />
                                <x-radio id="Terlama" model="sort" value="Terlama" icon="calendar-arrow-down"
                                    text="Terlama" />
                                <x-radio id="Terbaru" model="sort" value="Terbaru" icon="calendar-arrow-up"
                                    text="Terbaru" />
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
                                        <x-input id="minPrice" model="minPrice" type="number" placeholder="Min..." />
                                        <div class="">-</div>
                                        <x-input id="maxPrice" model="maxPrice" type="number" placeholder="Max..." />
                                    </div>
                                    @error('price')
                                        <div class="text-xs text-red-500">{{ $message }}</div>
                                    @enderror
                                </div>
                                {{-- status --}}
                                <div class="py-1">
                                    <div class="gap-2">
                                        <div>Status</div>
                                        <x-radio id="statusAll" model="status" icon="layers" text="Semua"
                                            value="all" />
                                        <x-radio id="statusAvailable" model="status" icon="circle-check"
                                            text="Tersedia" value="available" />
                                        <x-radio id="statusUnavailable" model="status" icon="circle-x"
                                            text="Tidak tersedia" value="unavailable" />
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
        @auth
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

                        {{-- aksi --}}
                        <div class="py-2 space-y-2">
                            <div class="font-bold">Aksi</div>
                            <div class="">
                                <a href="/edit?{{ http_build_query(['products' => $selected]) }}"
                                    class="p-1 inline-flex hover:bg-highlighter rounded-md items-center gap-1">
                                    <i data-lucide='Pencil' class="size-5"></i>
                                    <span class="">Edit</span>
                                </a>
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
        @endauth
        {{-- tambah --}}
        @if (auth()->user() && $onSelect !== true)
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
