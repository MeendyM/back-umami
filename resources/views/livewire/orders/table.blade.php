<div>

    {{-- Mensaje de éxito --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    <div class="flex justify-between my-3">
        <x-search class="focus:border-indigo-400 focus:ring-indigo-400">
            Buscar por ID, total, estado o usuario...
        </x-search>
    </div>


    <div class="px-4 py-4 bg-white custom-box-shadow rounded-t-[16px]">
        <table class="table-auto w-full">
            <thead>
                <tr class="text-gray text-smm font-semibold text-left">
                    <th class="px-2 py-2 rounded-tl-md">
                        <div class="flex items-center">
                            <button wire:click="sortBy('id_order')" class="text-left">
                                ID Orden
                            </button>
                            <x-sort-icon field="id_order" :sortField="$sortField" :sortAsc="$sortAsc" />
                        </div>
                    </th>
                    <th class="px-2 py-2">
                        <div class="flex items-center">
                            <button wire:click="sortBy('user')" class="text-left">
                                Usuario
                            </button>
                        </div>
                    </th>
                    <th class="px-2 py-2">Institución</th>
                    <th class="px-2 py-2">
                        <div class="flex items-center">
                            <button wire:click="sortBy('status')" class="text-left">
                                Estado
                            </button>
                            <x-sort-icon field="status" :sortField="$sortField" :sortAsc="$sortAsc" />
                        </div>
                    </th>
                    <th class="px-2 py-2">
                        <div class="flex items-center">
                            <button wire:click="sortBy('total')" class="text-left">
                                Total
                            </button>
                            <x-sort-icon field="total" :sortField="$sortField" :sortAsc="$sortAsc" />
                        </div>
                    </th>
                    <th class="px-2 py-2">Descuento</th>
                    <th class="px-2 py-2">Total Final</th>
                    <th class="px-2 py-2">
                        <div class="flex items-center">
                            <button wire:click="sortBy('created_at')" class="text-left">
                                Fecha
                            </button>
                            <x-sort-icon field="created_at" :sortField="$sortField" :sortAsc="$sortAsc" />
                        </div>
                    </th>
                    <th class="px-2 py-2 text-center">Ver / otras acciones</th>
                    <th class="px-2 py-2 rounded-tr-md text-center">Marcar como</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr class="text-left text-sm hover:bg-light-blue">
                        <td class="border-b border-t border-light-blue px-2 py-2 font-semibold">
                            #{{ $order->id_order }}
                        </td>
                        <td class="border-b border-t border-light-blue px-2 py-2">
                            <div>
                                <div class="font-medium">{{ $order->user->name ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $order->user->email ?? '-' }}</div>
                            </div>
                        </td>
                        <td class="border-b border-t border-light-blue px-2 py-2">
                            {{ $order->user->institution->name ?? '-' }}
                        </td>
                        <td class="border-b border-t border-light-blue px-2 py-2">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($order->status == 'requested') bg-blue-100 text-blue-800
                                @elseif($order->status == 'paying') bg-orange-100 text-orange-800
                                @elseif($order->status == 'under_review') bg-indigo-100 text-indigo-800
                                @elseif($order->status == 'review') bg-yellow-100 text-yellow-800
                                @elseif($order->status == 'paid') bg-green-100 text-green-800
                                @elseif($order->status == 'delivered') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="border-b border-t border-light-blue px-2 py-2">
                            ${{ number_format($order->total, 2) }}
                        </td>
                        <td class="border-b border-t border-light-blue px-2 py-2">
                            @if($order->discount_amount)
                                <div>
                                    <div class="text-green-600">-${{ number_format($order->discount_amount, 2) }}</div>
                                    @if($order->discount)
                                        <div class="text-xs text-gray-500">{{ $order->discount->name }}</div>
                                    @endif
                                </div>
                            @else
                                No aplica
                            @endif
                        </td>
                        <td class="border-b border-t border-light-blue px-2 py-2 font-semibold">
                            ${{ number_format($order->final_total, 2) }}
                        </td>
                        <td class="border-b border-t border-light-blue px-2 py-2">
                            {{ $order->created_at->format('d/m/Y') }}
                        </td>
                        <td class="border-b border-t border-light-blue px-2 py-2 text-center">
                            <div class="flex flex-col gap-1 text-xs">
                                <button 
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs whitespace-nowrap"
                                    wire:click="$dispatch('showReceipts', { orderId: {{ $order->id_order }} })"
                                    wire:loading.attr="disabled">
                                    Ver Recibos
                                </button>
                                <button 
                                    class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs whitespace-nowrap"
                                    wire:click="$dispatch('showOrderItems', { orderId: {{ $order->id_order }} })"
                                    wire:loading.attr="disabled">
                                    Ver Productos
                                </button>
                                   <button 
                                class="bg-orange-500 hover:bg-orange-600 text-white px-2 py-1 rounded text-xs whitespace-nowrap"
                                wire:click="$dispatch('showCashReceipModal', { orderId: {{ $order->id_order }} })"
                                wire:loading.attr="disabled">
                                Abono efectivo
                            </button>
                            </div>
                        </td>
                      
                     
                        <td class="border-b border-t border-light-blue px-2 py-2 text-center">
                            <div class="flex flex-col gap-1 text-xs">
                              
                                @if($order->status !== 'paid')
                                <button 
                                    class="bg-emerald-500 hover:bg-emerald-600 text-white px-2 py-1 rounded text-xs whitespace-nowrap"
                                    wire:click="changeStatusToPaid({{ $order->id_order }})"
                                    wire:loading.attr="disabled"
                                    wire:confirm="¿Marcar como 'Pagado'?">
                                    Pagado
                                </button>
                                @endif

                                @if($order->status !== 'delivered')
                                <button 
                                    class="bg-purple-500 hover:bg-purple-600 text-white px-2 py-1 rounded text-xs whitespace-nowrap"
                                    wire:click="changeStatusToDelivered({{ $order->id_order }})"
                                    wire:loading.attr="disabled"
                                    wire:confirm="¿Marcar como 'Entregado'?">
                                    Entregado
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if ($orders->count() == 0)
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6 text-center">
                No hay registros
            </div>
        @endif
    </div>

    {{-- Paginación --}}
    <div class="px-4 py-4 relative min-h-20 mt-3">
        {{ $orders->links() }}
        <div class="w-[65px] absolute bottom-6 right-72">
            <x-inputs.dropdown class="py-1 px-1 border border-gray text-smm">
                <x-bgs.flex-center-between class="w-full">
                    <span x-text="$wire.perPage"></span>
                    <x-icons.drop />
                </x-bgs.flex-center-between>
                <x-slot name="options" class="-top-28">
                    @foreach ($optionsPerPage as $option)
                        <x-inputs.dropdown.item wire:key="option-perPage-{{ $option }}" :active="$option == $perPage"
                            x-on:click="$wire.set('perPage', {{ $option }}); show = false">
                            {{ $option }}
                        </x-inputs.dropdown.item>
                    @endforeach
                </x-slot>
            </x-inputs.dropdown>
        </div>
    </div>


    {{-- Modal para ver recibos --}}
    @livewire('orders.receipts-modal', [], key('orders.receipts-modal'))


    {{-- Modal para ver productos de la orden --}}
    @livewire('orders.order-items-modal', [], key('orders.order-items-modal'))

    {{-- Modal para abono en efectivo --}}
    @livewire('orders.cash-receip-modal', [], key('orders.cash-receip-modal'))


</div>
