<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2 bg-navy border border-transparent rounded-md font-medium text-sm text-paper hover:bg-slate focus:bg-slate focus:outline-none focus:ring-2 focus:ring-bronze focus:ring-offset-2 focus:ring-offset-paper transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
