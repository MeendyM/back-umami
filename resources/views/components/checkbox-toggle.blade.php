@props([
    'title' => '',
    'input' => false,
    'wireModel' => null,
])

<div class="mt-4 w-full">
    @if ($title)
        <label for="{{ $input }}" class="text-smm text-tx-black font-bold mb-4">{{ $title }}</label>
    @endif

    <div class="mt-2">
        <button type="button" @if ($wireModel) wire:click="$toggle('{{ $wireModel }}')" @endif
            class="flex items-center justify-between w-full font-dm_sans rounded-full border border-light-blue py-2 px-4 text-sm lg:text-base
                focus:ring-0  leading-tight focus:outline-none focus:shadow-outline 
                disabled:cursor-not-allowed disabled:text-gray2  text-black2 
                {{ $input ? 'bg-indigo-500 text-white border-indigo-600' : 'bg-white text-tx-black hover:bg-light-blue' }}">
            <span>{{ $title }}</span>

            <div
                class="w-4 h-4 border border-light-blue rounded-sm flex items-center justify-center
                {{ $input ? 'bg-white' : 'bg-transparent' }}">
                @if ($input)
                    <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" stroke-width="3"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                @endif
            </div>
        </button>
    </div>
</div>
