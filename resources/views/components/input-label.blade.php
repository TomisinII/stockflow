@props(['for' => '', 'value' => '', 'required' => false])

<label for="{{ $for }}" {{ $attributes->merge(['class' => 'block text-sm font-medium text-gray-700 mb-2']) }}>
    {{ $value ?? $slot }}
    @if($required)
        <span class="text-red-500">*</span>
    @endif
</label>
