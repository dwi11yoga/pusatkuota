@extends('layouts.app')
@section('slot')
    <x-error-page code="404" title="Page Not Found"
        message="The page you're looking for doesn't exist or has been moved." />
@endsection
