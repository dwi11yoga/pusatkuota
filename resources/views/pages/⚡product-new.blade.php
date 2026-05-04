<?php

use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Attributes\Title;
use App\Models\Product;

new class extends Component {
    // jumlah input
    #[Title('Tambah Produk baru')]
    public $inputCount = 1;

    // input global
    #[Url]
    #[Validate('required')]
    public $type;
    #[Url]
    public $provider;
    #[Url]
    public $category;
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

    // reset saved jika ada input yang diisi
    public function updated()
    {
        $this->saved = null;
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

    // fungsi reset form
    public function resetForm()
    {
        $this->type = $this->category = $this->provider = $this->url = null;
        $this->names = $this->costs = $this->profits = $this->prices = [];
        $this->inputCount = 1;
    }
};
?>

<div class="space-y-5">
    <x-header title="produk baru" />
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
            {{-- tombol reset form --}}
            <x-button id="resetForm" type="button" text="Reset" icon="refresh-ccw" function="resetForm" />
            {{-- tambah input produk --}}
            <x-button id="addInput" type="button" text="Tambah input" icon="plus" function="inputIncrement" />
            {{-- simpan --}}
            @if ($saved == true)
                <x-button id="save" type="link" url="/{{ strtolower($type) }}/{{ strtolower($provider) }}"
                    :hideText="false" color="dark" text="Data berhasil disimpan, lihat" icon="check" />
            @else
                <x-button id="save" type="submit" :hideText="false" color="dark">
                    @if ($saved === false)
                        <div wire:loading.remove wire:target='save' class="">Gagal menyimpan data, coba lagi</div>
                        <i wire:loading.remove wire:target='save' data-lucide='x' class="size-5"></i>
                    @else
                        <div wire:loading.remove wire:target='save' class="">Simpan</div>
                        <i wire:loading.remove wire:target='save' data-lucide='save' class="size-5"></i>
                    @endif
                    <div wire:loading wire:target='save' class="">Menyimpan...</div>
                    <div wire:loading wire:target='save' class="animate-spin">
                        <i data-lucide='loader' class="size-5"></i>
                    </div>
                </x-button>
            @endif
        </div>
    </form>
</div>
