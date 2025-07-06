<label {{ $attributes->merge(['class' => 'block peer relative']) }}>
    <span
        class="block text-gray-700 text-sm font-bold mb-2"><x-texts.text-small>{{ $slot }}</x-texts.text-small></span>
    {{ $container }}
</label>
