<?php

use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;
use App\Models\Product;

new class extends Component {
    // jumlah input
    public $inputCount = 1;

    // input global
    #[Validate('required')]
    public $type;
    public $provider, $category;
    #[Validate('nullable|url')]
    public $url;

    //input produk
    #[Validate(['names' => 'array', 'names.*' => 'required'])]
    public array $names = [];
    #[Validate(['costs' => 'array', 'costs.*' => 'required|integer|min:1'])]
    public array $costs = [];
    #[Validate(['profits' => 'array', 'profits.*' => 'required|integer|min:0'])]
    public array $profits = [];
    #[Validate(['prices' => 'array', 'prices.*' => 'required|integer|min:0'])]
    public array $prices = [];

    // inisialisasi nilai pada array agar tidak kosong
    public function fillArrayData()
    {
        // jika inputCount bertambah, tambah index baru
        for ($i = 1; $i <= $this->inputCount; $i++) {
            $this->names[$i] = $this->names[$i] ?? '';
            $this->costs[$i] = $this->costs[$i] ?? '';
            $this->profits[$i] = $this->profits[$i] ?? 2000;
            $this->prices[$i] = $this->prices[$i] ?? 0;
        }
    }
    public function mount()
    {
        $this->fillArrayData();
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
    public function calculatePrice($key)
    {
        $cost = (int) ($this->costs[$key] ?? 0);
        $profit = (int) ($this->profits[$key] ?? 0);
        $price = $cost + $profit;
        // bulatkan harga ke ribuan terdekat
        $price = (int) round($price / 1000) * 1000;
        $this->prices[$key] = $price;
    }

    // incerment jumlah input
    function inputIncrement()
    {
        $this->inputCount += 1;
        $this->fillArrayData();
    }

    // dapatkan opsi tipe
    #[Computed]
    public function typeOptions()
    {
        return Product::distinct()->pluck('type');
    }
    // dapatkan opsi provider
    #[Computed]
    public function providerOptions()
    {
        return Product::distinct()->pluck('provider');
    }
    // dapatkan opsi kategori
    #[Computed]
    public function categoryOptions()
    {
        $data = Product::distinct();
        if ($this->provider) {
            $data = $data->where('provider', $this->provider);
        }
        $data = $data->pluck('category');
        return $data;
    }

    // fungsi simpan data
    public $saved;
    public function save()
    {
        sleep(2);
        try {
            $this->validate();

            // simpan nilai input kedalam satu array
            $data = [];
            foreach ($this->names as $key => $value) {
                $data[$key] = [
                    'type' => $this->type,
                    'provider' => $this->provider,
                    'category' => $this->category,
                    'name' => $value,
                    'base_price' => $this->costs[$key],
                    'expected_profit' => $this->profits[$key],
                    'real_profit' => $this->prices[$key] - $this->costs[$key],
                    'url' => $this->url,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            // dd($data);
            $this->saved = true;
            Product::insert($data);
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

<div class="space-y-5">
    <livewire:header title="produk baru" />
    {{-- body (input) --}}
    <form wire:submit='save' class="space-y-5">
        <div class="text-base divide-y divide-dashed">
            {{-- tipe --}}
            <div class="py-2">
                <x-input id="type" model="type" type="text" label="Tipe" dataset="type-list"
                    :error="$errors->first('type')" />
                <datalist id="type-list">
                    @foreach ($this->typeOptions as $option)
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
                    @foreach ($this->providerOptions as $option)
                        <option>{{ $option }}</option>
                    @endforeach
                </datalist>
            </div>
            {{-- kategori --}}
            <div class="py-2">
                <x-input id="category" model="category" type="text" label="Kategori" dataset="category-list"
                    :error="$errors->first('category')" />
                <datalist id="category-list">
                    @foreach ($this->categoryOptions as $option)
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
                @for ($i = 1; $i <= $inputCount; $i++)
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
        {{-- tambah input + simpan --}}
        <div class="fixed bottom-5 right-5 flex justify-center text-base gap-2">
            <div wire:click='inputIncrement'
                class="flex gap-2 items-center rounded-full border-2 border-neutral-500 hover:bg-highlighter border-dashed px-5 py-3 cursor-pointer transition-all ease-in-out group">
                <i data-lucide='plus' class="size-5"></i>
                <div class="hidden group-hover:block">Tambah input</div>
            </div>
            {{-- simpan --}}
            <button
                class="flex gap-2 items-center rounded-full border-2 border-neutral-500 hover:bg-highlighter border-dashed px-5 py-3 cursor-pointer transition-all ease-in-out">
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
