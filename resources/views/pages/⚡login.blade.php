<?php

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;

new class extends Component {
    #[Title('Masuk Sebagai Admin')]
    //
    #[Validate('required|min:6|max:50|alpha_dash')]
    public $username;
    #[Validate('required|min:6|max:255')]
    public $password;
    public $loginSuccess;
    public function auth()
    {
        // vaidasi
        $credentials = $this->validate();
        if (Auth::attempt($credentials)) {
            // regenerate session
            session()->regenerate();
            $this->loginSuccess = true;
            // arahkan ke hal. menu
            return redirect()
                ->intended()
                ->to('/')
                ->with('success', 'Welcome back, ' . auth()->user()->name . '!');
        }

        $this->loginSuccess = false;
    }
};
?>

<div class="flex items-center justify-center h-[90vh]">
    <form wire:submit='auth' class="space-y-5 p-5 min-w-96 max-w-96">
        @csrf
        <div class="text-5xl bg-highlighter p-3 font-bold">Masuk <br> sebagai <br> admin</div>
        <div class="space-y-2">
            <label for="username" class="block">Username</label>
            <x-input type="text" id="username" model="username" :error="$errors->first('username')" :autofocus="true" />
            <label for="password" class="block">Kata sandi</label>
            <x-input type="password" id="password" model="password" :error="$errors->first('password')" />
        </div>
        <x-button id="loginButton" type="submit" hideText="false" color="dark">
            @if ($loginSuccess === true)
                <i wire:loading.remove wire:target='auth' data-lucide='check' class="size-5"></i>
                <div wire:loading.remove wire:target='auth' class="">Berhasil masuk</div>
            @elseif ($loginSuccess === false)
                <i wire:loading.remove wire:target='auth' data-lucide='x' class="size-5"></i>
                <div wire:loading.remove wire:target='auth' class="">Gagal, coba lagi</div>
            @else
                <i wire:loading.remove wire:target='auth' data-lucide='log-in' class="size-5"></i>
                <div wire:loading.remove wire:target='auth' class="">Masuk</div>
            @endif
            <div wire:loading wire:target='auth' class="animate-spin">
                <i data-lucide='loader' class="size-5"></i>
            </div>
            <div wire:loading wire:target='auth' class="">Mencoba masuk...</div>
        </x-button>
        {{-- <button
            class="flex text-base gap-2 items-center rounded-full border-2 bg-neutral-50 border-neutral-500 hover:bg-highlighter border-dashed px-5 py-3 cursor-pointer transition-all ease-in-out">

        </button> --}}
    </form>
</div>
