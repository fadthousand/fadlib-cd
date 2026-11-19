@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-xs uppercase tracking-wider text-slate']) }}>
    {{ $value ?? $slot }}
</label>
