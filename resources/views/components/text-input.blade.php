@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-silver bg-ivory/70 text-navy placeholder-mist focus:border-navy focus:bg-paper focus:ring-navy rounded-md shadow-xs text-sm']) }}>
