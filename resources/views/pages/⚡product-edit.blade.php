<?php

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use App\Models\Product;

new class extends Component {
    #[Url]
    public $products; // id produk yang diedit

    // dapatkan data product
    #[Validate('required')]
    public $type;
    public $provider;
    public $category;
    #[Validate('nullable|url')]
    public $url;

    #[Validate(['names' => 'array', 'names.*' => 'required'])]
    public array $names = [];
    #[Validate(['costs' => 'array', 'costs.*' => 'required|integer|min:1'])]
    public array $costs = [];
    #[Validate(['profits' => 'array', 'profits.*' => 'required|integer|min:0'])]
    public array $profits = [];
    #[Validate(['prices' => 'array', 'prices.*' => 'required|integer|min:0'])]
    public array $prices = [];

    // opsi
    public $typeOptions, $providerOptions, $categoryOptions;
    public function mount()
    {
        $products = Product::whereIn('id', $this->products)->get();
        // set ke variabel
        $i = 1;
        foreach ($products as $product) {
            $this->names[$i] = $product->name;
            $this->costs[$i] = $product->base_price;
            $this->profits[$i] = $product->expected_profit;
            $this->prices[$i] = $product->base_price + $product->real_profit;
            $i++;
        }
        $types = $products->unique('type')->pluck('type');
        $this->type = count($types) == 1 ? $types->first() : null;

        $providers = $products->unique('provider')->pluck('provider');
        $this->provider = count($providers) == 1 ? $providers->first() : null;

        $categories = $products->unique('category')->pluck('category');
        $this->category = count($categories) == 1 ? $categories->first() : null;

        $urls = $products->unique('url')->pluck('url');
        $this->url = count($urls) == 1 ? $urls->first() : null;

        // ambil opsi tipe, kategori, dan provider
        $this->typeOptions = Product::distinct()->pluck('type');
        $this->providerOptions = Product::distinct()->pluck('provider');
        $this->categoryOptions = Product::distinct()->pluck('category');
    }

    // jalankan validasi nama saat ada perubahan pada input
    public function updatedNames()
    {
        $this->validateOnly('names.*');
    }
    public function updatedCosts()
    {
        $this->validateOnly('costs.*');
    }
    public function updatedProfits()
    {
        $this->validateOnly('profits.*');
    }

    // hitung berapa harga jual
    // #[Computed]
    public function calculatePrice($key)
    {
        $cost = (int) ($this->costs[$key] ?? 0);
        $profit = (int) ($this->profits[$key] ?? 0);
        $price = $cost + $profit;
        // bulatkan harga ke ribuan terdekat
        $price = (int) round($price / 1000) * 1000;
        $this->prices[$key] = $price;
    }

    // funsi simpan
    public $saved;
    public function save()
    {
        try {
            $this->validate();

            // set data yang akan disimpan
            $data = [];
            foreach ($this->names as $key => $name) {
                $id = $this->products[$key - 1];
                $data[$id] = [
                    'name' => $name,
                    'base_price' => $this->costs[$key],
                    'expected_profit' => $this->profits[$key],
                    'real_profit' => $this->prices[$key] - $this->costs[$key],
                ];
                if (!empty($this->type)) {
                    $data[$id]['type'] = $this->type;
                }
                if (!empty($this->provider)) {
                    $data[$id]['provider'] = $this->provider;
                }
                if (!empty($this->category)) {
                    $data[$id]['category'] = $this->category;
                }
                if (!empty($this->url)) {
                    $data[$id]['url'] = $this->url;
                }
            }
            // dd($data);
            foreach ($data as $key => $d) {
                Product::find($key)->update($d);
            }
            $this->saved = true;
        } catch (\Illuminate\Validation\ValidationException $err) {
            $this->saved = false;
            throw $err;
        } catch (\Exception $err) {
            $this->saved = false;
            throw $err;
        }
    }
};
?>

<div>
    <x-header title="Edit Produk" />
    <form wire:submit='save'>
        <div class="text-base divide-y divide-dashed">
            {{-- tipe --}}
            <div class="py-2">
                <x-input id="type" model="type" type="text" label="Tipe" dataset="type-list" :error="$errors->first('type')" />
                <datalist id="type-list">
                    @foreach ($typeOptions as $option)
                        <option>{{ $option }}</option>
                    @endforeach
                </datalist>
            </div>
            {{-- provider --}}
            <div class="py-2">
                {{-- <livewire:input id="provider" model="provider" type="text" label="Provider" dataset="provider-list"
                    :error="$errors->first('provider')" /> --}}
                <x-input id="provider" model="provider" type="text" label="Provider" dataset="provider-list"
                    :error="$errors->first('provider')" />
                <datalist id="provider-list">
                    @foreach ($providerOptions as $option)
                        <option>{{ $option }}</option>
                    @endforeach
                </datalist>
            </div>
            {{-- kategori --}}
            <div class="py-2">
                <x-input id="category" model="category" type="text" label="Kategori" dataset="category-list"
                    :error="$errors->first('category')" />
                <datalist id="category-list">
                    @foreach ($categoryOptions as $option)
                        <option>{{ $option }}</option>
                    @endforeach
                </datalist>
            </div>
            {{-- Url --}}
            <div class="py-2">
                <x-input id="url" model="url" type="text" label="Tautan produk" :error="$errors->first('url')" />
            </div>
        </div>
        <div class="">
            {{-- head --}}
            <div class="grid grid-cols-12 gap-2 text-base font-bold">
                <div class="">#</div>
                <div class="col-span-5">Nama</div>
                <div class="col-span-2">Modal</div>
                <div class="col-span-2">Profit</div>
                <div class="col-span-2">Harga</div>
            </div>
            {{-- input produk --}}
            <div class="divide-y divide-dashed">
                @for ($i = 1; $i <= count($products); $i++)
                    <div wire:key='row-{{ $i }}' class="grid grid-cols-12 gap-2 text-base pt-4 pb-1">
                        <div class="">{{ $i }}</div>
                        <div class="col-span-5">
                            <textarea wire:model.live.blur='names.{{ $i }}' placeholder="Ketik..."
                                oninput="this.style.height = 'auto'; this.style.height = this.scrollHeight + 'px'"
                                class="{{ empty($names[$i]) ? 'h-6.5' : '' }}"></textarea>
                            @error('names.' . $i)
                                <div class="text-red-500 text-xs">{{ $message }}</div>
                            @enderror

                        </div>
                        <label for="costs{{ $i }}" class="col-span-2">
                            <x-input wire:key='costs{{ $i }}' id="costs{{ $i }}"
                                calculatePriceMethod="calculatePrice({{ $i }})"
                                model="costs.{{ $i }}" type="number" :error="$errors->first('costs.' . $i)" />
                        </label>
                        <label for="profits{{ $i }}" class="col-span-2">
                            <x-input wire:key='profits{{ $i }}' id="profits{{ $i }}"
                                calculatePriceMethod="calculatePrice({{ $i }})"
                                model="profits.{{ $i }}" type="number" :error="$errors->first('profits.' . $i)" />
                        </label>
                        <label for="prices{{ $i }}" class="col-span-2">
                            <x-input wire:key='prices{{ $i }}' id="prices{{ $i }}"
                                model="prices.{{ $i }}" type="number" :error="$errors->first('prices.' . $i)" readonly="true" />
                        </label>
                    </div>
                @endfor
            </div>
        </div>
        {{-- simpan --}}
        <div class="fixed bottom-5 right-5 flex justify-center text-base gap-2">
            <button
                class="flex gap-2 items-center rounded-full border-2 bg-neutral-50 border-neutral-500 hover:bg-highlighter border-dashed px-5 py-3 cursor-pointer transition-all ease-in-out">
                @if ($saved === true)
                    <i wire:loading.remove wire:target='save' data-lucide='check' class="size-5"></i>
                    <div wire:loading.remove wire:target='save' class="">Data berhasil disimpan</div>
                @elseif ($saved === false)
                    <i wire:loading.remove wire:target='save' data-lucide='x' class="size-5"></i>
                    <div wire:loading.remove wire:target='save' class="">Gagal menyimpan data, coba lagi</div>
                @else
                    <i wire:loading.remove wire:target='save' data-lucide='save' class="size-5"></i>
                    <div wire:loading.remove wire:target='save' class="">Simpan</div>
                @endif
                <div wire:loading wire:target='save' class="animate-spin">
                    <i data-lucide='loader' class="size-5"></i>
                </div>
                <div wire:loading wire:target='save' class="">Menyimpan...</div>
            </button>
        </div>
    </form>
</div>
