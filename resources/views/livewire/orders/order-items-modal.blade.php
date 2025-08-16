{{-- Modal para mostrar productos de la orden --}}
<div>
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                {{-- Overlay --}}
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

                {{-- Modal panel --}}
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="w-full">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        Productos de la Orden #{{ $order->id_order ?? '' }}
                                    </h3>
                                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                {{-- Sets y sus productos --}}
                                @if (count($groupedItems['sets']) > 0)
                                    <div class="mb-6">
                                        <h4 class="font-semibold text-indigo-700 mb-2">Sets</h4>
                                        @foreach ($groupedItems['sets'] as $setGroup)
                                            <div class="border rounded-lg mb-4 p-3 bg-indigo-50">
                                                <div class="font-bold text-indigo-900 mb-2">{{ $setGroup['set']->name ?? 'Set' }}</div>
                                                <div class="text-sm text-gray-700 mb-2">Cantidad: {{ $setGroup['set_item']->quantity }}</div>
                                                <div class="text-sm text-gray-700 mb-2">Subtotal: ${{ number_format($setGroup['set_item']->subtotal, 2) }}</div>
                                                <div class="ml-4">
                                                    <div class="font-semibold text-gray-800">Productos en el set:</div>
                                                    <ul class="list-disc ml-6">
                                                        @foreach ($setGroup['products'] as $prod)
                                                            <li class="mb-1">
                                                                {{ $prod->product->name ?? 'Producto' }}
                                                                <span class="text-xs text-gray-500">(Cantidad: {{ $prod->quantity }})</span>
                                                                @if($prod->is_customized)
                                                                    <span class="text-xs text-blue-600 ml-2">Personalizado</span>
                                                                    @if(!empty($prod->custom_text))
                                                                        <span class="block text-xs text-gray-500 ml-2">@if(is_array($prod->custom_text)){{ implode(' | ', $prod->custom_text) }}@else{{ $prod->custom_text }}@endif</span>
                                                                    @endif
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Productos sueltos --}}
                                @if (count($groupedItems['products']) > 0)
                                    <div>
                                        <h4 class="font-semibold text-green-700 mb-2">Productos Individuales</h4>
                                        <ul class="list-disc ml-6">
                                            @foreach ($groupedItems['products'] as $prod)
                                                <li class="mb-2">
                                                    {{ $prod->product->name ?? 'Producto' }}
                                                    <span class="text-xs text-gray-500">(Cantidad: {{ $prod->quantity }})</span>
                                                    <span class="text-xs text-gray-500 ml-2">Subtotal: ${{ number_format($prod->subtotal, 2) }}</span>
                                                    @if($prod->is_customized)
                                                        <span class="text-xs text-blue-600 ml-2">Personalizado</span>
                                                        @if(!empty($prod->custom_text))
                                                            <span class="block text-xs text-gray-500 ml-2">@if(is_array($prod->custom_text)){{ implode(' | ', $prod->custom_text) }}@else{{ $prod->custom_text }}@endif</span>
                                                        @endif
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if (count($groupedItems['sets']) == 0 && count($groupedItems['products']) == 0)
                                    <div class="text-center py-8">
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">No hay productos en esta orden</h3>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="closeModal" type="button" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
