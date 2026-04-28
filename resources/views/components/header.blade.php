@props(['title', 'underline' => true])

<div class="">
    <div class="p-12 text-6xl font-bold italic text-center uppercase">{{ $title }}</div>
    @if ($underline !== false)
        <div class="border border-dashed"></div>
    @endif
</div>
