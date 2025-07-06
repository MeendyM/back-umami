<button
    {{ $attributes->merge([
        'type' => 'submit',
        'class' =>
            'inline-flex justify-center w-full rounded-md border border-primary px-4 py-[11px] shadow-sm bg-primary ' .
            'text-lg leading-6 text-white font-medium font-poppins sm:text-sm sm:leading-5 ' .
            'hover:bg-hover hover:text-white focus:outline-none focus:border-green-700 focus:shadow-outline-green ' .
            'transition ease-in-out duration-150 sm:text-sm sm:leading-5 ',
    ]) }}>
    {{ $slot }}
</button>
