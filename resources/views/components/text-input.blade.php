@props(['disabled' => false, 'type' => 'text'])

<input
    type="{{ $type }}"
    {{ $disabled ? 'disabled' : '' }}
    {!! $attributes->merge(['class' => 'w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all disabled:bg-gray-100 disabled:cursor-not-allowed']) !!}
>
