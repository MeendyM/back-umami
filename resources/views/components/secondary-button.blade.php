<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex justify-center items-center h-[42px] space-x-2 px-6 py-2 border border-light-blue rounded-full font-semibold text-sm text-blue tracking-widest hover:text-white hover:bg-blue focus:border-blue active:indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
