@props(['type' => 'submit', 'disabled' => false])

<button
    type="{{ $type }}"
    {{ $disabled ? 'disabled' : '' }}
    {{ $attributes->merge(['class' => 'w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed']) }}
>
    {{ $slot }}
</button>
