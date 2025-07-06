<a
    {{ $attributes->merge([
        'class' =>
            'inline-flex justify-center w-full rounded-md border border-transparent px-4 py-[11px] shadow-sm bg-primary '.
            'text-lg leading-6 text-white font-medium font-poppins ' .
            'hover:bg-hover focus:outline-none focus:border-green-700 focus:shadow-outline-green '.
            'transition ease-in-out duration-150 sm:text-sm sm:leading-5 box-content ',
    ]) }}>
    {{ $slot }}
</a>
