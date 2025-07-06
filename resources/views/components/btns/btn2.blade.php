<button
    {{ $attributes->merge([
        'type' => 'submit',
        'class' =>
            'inline-flex justify-center w-full rounded-md border border-primary px-4 py-[11px] shadow-sm ' .
            'text-lg leading-6 text-primary font-medium font-poppins sm:text-sm sm:leading-5 ' .
            'hover:bg-primary hover:text-white focus:outline-none focus:border-primary focus:shadow-outline-blue ' .
            'transition ease-in-out duration-150 sm:text-sm sm:leading-5 ',
    ]) }}>
    {{ $slot }}
</button>
