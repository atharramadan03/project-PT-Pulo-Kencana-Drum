@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-[11px] text-rose-500 font-medium leading-tight mt-1 space-y-0.5']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
