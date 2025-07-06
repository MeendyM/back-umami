<a
    {{ $attributes->merge([
        'class' =>
            'inline-flex justify-center w-full rounded-md border border-primary px-4 py-[11px] bg-gray-100 shadow-sm ' .
            'text-lg leading-6 font-medium font-poppins text-primary sm:text-sm sm:leading-5 ' .
            'hover:bg-primary hover:text-white focus:outline-none focus:border-primary focus:shadow-outline-blue ' .
            'transition ease-in-out duration-150 box-content ',
    ]) }}>
    {{ $slot }}
</a>
