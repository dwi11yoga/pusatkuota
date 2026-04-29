@props(['code', 'title', 'message'])

<div class="flex items-center justify-center h-[90vh]">
    <div class="max-w-80 space-y-1">
        <div class="text-5xl font-bold bg-highlighter p-3">{{ $code }}</div>
        <div class="font-bold text-xl">{{ $title }}</div>
        <p>{{ $message }}</p>
    </div>
</div>
