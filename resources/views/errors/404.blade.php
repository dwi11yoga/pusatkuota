@extends('layouts.app')
@section('slot')
    <x-error-page code="404" title="Halaman tidak ditemukan"
        message="Halaman yang kamu cari tidak ada atau mungkin telah dipindahkan." />
@endsection
