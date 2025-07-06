<button
    {{ $attributes->merge([
        'class' =>
            'bg-primary hover:bg-hover disabled:bg-gray-500 disabled:cursor-not-allowed rounded-md text-white flex justify-center items-center py-3 px-4 my-3 w-32',
    ]) }}>
    {{ $slot }}
</button>
