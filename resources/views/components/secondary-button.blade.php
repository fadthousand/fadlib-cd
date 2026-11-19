<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-4 py-2 bg-ivory border border-silver rounded-md font-medium text-sm text-navy hover:bg-sand/20 focus:outline-none focus:ring-2 focus:ring-bronze focus:ring-offset-2 focus:ring-offset-paper transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
