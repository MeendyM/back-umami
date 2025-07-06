<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center ml-3 px-3 py-2 border border-light-blue rounded-xl font-semibold text-sm text-blue tracking-widest hover:border-blue focus:border-blue active:border-blue focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 transition ease-in-out duration-150']) }}>
    <x-icons.filter></x-icons.filter>
    {{ $slot }}
</button>
