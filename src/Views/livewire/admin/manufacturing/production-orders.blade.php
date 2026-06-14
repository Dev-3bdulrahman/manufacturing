<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm">
        <div class="space-y-1">
            <h1 class="text-2xl font-black text-gray-900 dark:text-white">{{ __('mfg::mfg.production_orders') }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('mfg::mfg.production_description') }}</p>
        </div>
        <button wire:click="openCreateModal()" class="flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-blue-500/20 transition-all">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>{{ __('mfg::mfg.add_production_order') }}</span>
        </button>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white dark:bg-gray-900 p-4 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">{{ __('mfg::mfg.search') }}</label>
            <div class="relative flex items-center">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('mfg::mfg.search_placeholder') }}" class="w-full ps-10 pe-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                <i data-lucide="search" class="absolute start-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
            </div>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">{{ __('mfg::mfg.status') }}</label>
            <select wire:model.live="statusFilter" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none transition-all">
                <option value="">{{ __('mfg::mfg.all_statuses') }}</option>
                <option value="draft">{{ __('mfg::mfg.status_draft') }}</option>
                <option value="planned">{{ __('mfg::mfg.status_planned') }}</option>
                <option value="in_progress">{{ __('mfg::mfg.status_in_progress') }}</option>
                <option value="completed">{{ __('mfg::mfg.status_completed') }}</option>
                <option value="cancelled">{{ __('mfg::mfg.status_cancelled') }}</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('mfg::mfg.production_order_code') }}</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('mfg::mfg.finished_product') }}</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('mfg::mfg.qty') }}</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('mfg::mfg.start_date') }}</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('mfg::mfg.target_warehouse') }}</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('mfg::mfg.status') }}</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('mfg::mfg.production_cost') }}</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">{{ __('mfg::mfg.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">{{ $order->code }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-700 dark:text-gray-300">
                                {{ $order->product?->translated_name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ number_format($order->quantity, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->start_date?->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->warehouse?->name ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @php
                                    $statusColors = [
                                        'draft' => 'bg-gray-100 text-gray-600',
                                        'planned' => 'bg-yellow-50 text-yellow-700 dark:bg-yellow-950/20',
                                        'in_progress' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/20',
                                        'completed' => 'bg-green-50 text-green-700 dark:bg-green-950/20',
                                        'cancelled' => 'bg-red-50 text-red-700 dark:bg-red-950/20',
                                    ];
                                @endphp
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $statusColors[$order->status] ?? 'bg-gray-100' }}">
                                    {{ __('mfg::mfg.status_' . $order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-semibold">
                                {{ $order->status === 'completed' ? number_format($order->cost, 2) : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($order->status === 'draft')
                                        <button wire:click="updateStatus({{ $order->id }}, 'planned')" title="{{ __('mfg::mfg.plan_order') }}" class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-all">
                                            <i data-lucide="calendar" class="w-4 h-4"></i>
                                        </button>
                                    @endif
                                    
                                    @if($order->status === 'planned')
                                        <button wire:click="updateStatus({{ $order->id }}, 'in_progress')" title="{{ __('mfg::mfg.start_production') }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-all">
                                            <i data-lucide="play" class="w-4 h-4"></i>
                                        </button>
                                    @endif

                                    @if(in_array($order->status, ['planned', 'in_progress']))
                                        <button wire:click="openCompleteModal({{ $order->id }})" title="{{ __('mfg::mfg.complete_production') }}" class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition-all">
                                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                                        </button>
                                    @endif

                                    @if(in_array($order->status, ['draft', 'cancelled']))
                                        <button onclick="confirmOrderDelete({{ $order->id }})" title="{{ __('mfg::mfg.delete') }}" class="p-1.5 text-red-500 hover:text-red-700 rounded-lg hover:bg-red-50 transition-all">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-sm text-gray-500">{{ __('mfg::mfg.no_production_orders') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">{{ $orders->links() }}</div>
        @endif
    </div>

    <!-- Create Modal -->
    @if($showFormModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm transition-all">
            <div class="bg-white dark:bg-gray-900 rounded-2xl max-w-2xl w-full border border-gray-100 dark:border-gray-800 shadow-2xl overflow-hidden animate__animated animate__fadeInUp animate__faster">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white">
                        {{ __('mfg::mfg.add_production_order') }}
                    </h3>
                    <button wire:click="closeFormModal()" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <form wire:submit.prevent="save" class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('mfg::mfg.bom') }} *</label>
                            <select wire:model="bomId" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all">
                                <option value="">-- {{ __('mfg::mfg.select_bom') }} --</option>
                                @foreach($allBoms as $recipe)
                                    <option value="{{ $recipe->id }}">{{ $recipe->name }} ({{ $recipe->product?->translated_name }})</option>
                                @endforeach
                            </select>
                            @error('bomId') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('mfg::mfg.target_warehouse') }} *</label>
                            <select wire:model="warehouseId" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all">
                                <option value="">-- {{ __('mfg::mfg.select_warehouse') }} --</option>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                @endforeach
                            </select>
                            @error('warehouseId') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('mfg::mfg.quantity_to_produce') }} *</label>
                            <input type="number" step="0.01" wire:model="quantity" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all">
                            @error('quantity') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('mfg::mfg.start_date') }} *</label>
                            <input type="date" wire:model="startDate" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all">
                            @error('startDate') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Work Order Routing Steps -->
                    <div class="border-t border-gray-100 dark:border-gray-800 pt-4 space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-black text-gray-900 dark:text-white">{{ __('mfg::mfg.routing_steps') }}</h4>
                            <button type="button" wire:click="addRoutingStep()" class="text-xs font-bold text-blue-600 hover:text-blue-800 transition-all">+ {{ __('mfg::mfg.add_routing_step') }}</button>
                        </div>

                        <div class="space-y-3">
                            @foreach($routingSteps as $index => $step)
                                <div class="flex items-center gap-3">
                                    <div class="flex-1">
                                        <select wire:model="routingSteps.{{ $index }}.work_center_id" class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none transition-all">
                                            <option value="">-- {{ __('mfg::mfg.work_centers') }} --</option>
                                            @foreach($workCenters as $wc)
                                                <option value="{{ $wc->id }}">{{ $wc->name }}</option>
                                            @endforeach
                                        </select>
                                        @error("routingSteps.{$index}.work_center_id") <span class="text-xs text-red-500 mt-0.5 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="flex-1">
                                        <input type="text" wire:model="routingSteps.{{ $index }}.name" placeholder="{{ __('mfg::mfg.step_name') }}" class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none transition-all">
                                        @error("routingSteps.{$index}.name") <span class="text-xs text-red-500 mt-0.5 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="w-24">
                                        <input type="number" step="0.01" wire:model="routingSteps.{{ $index }}.planned_hours" placeholder="{{ __('mfg::mfg.planned_hours') }}" class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none transition-all">
                                        @error("routingSteps.{$index}.planned_hours") <span class="text-xs text-red-500 mt-0.5 block">{{ $message }}</span> @enderror
                                    </div>
                                    <button type="button" wire:click="removeRoutingStep({{ $index }})" title="{{ __('mfg::mfg.delete') }}" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-all">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 dark:border-gray-800 flex justify-end gap-2">
                        <button type="button" wire:click="closeFormModal()" class="px-5 py-2 text-sm font-bold bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl transition-all">
                            {{ __('mfg::mfg.cancel') }}
                        </button>
                        <button type="submit" class="px-5 py-2 text-sm font-bold bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-lg transition-all">
                            {{ __('mfg::mfg.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Complete Production Order Modal -->
    @if($showCompleteModal && $completeOrder)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm transition-all">
            <div class="bg-white dark:bg-gray-900 rounded-2xl max-w-lg w-full border border-gray-100 dark:border-gray-800 shadow-2xl overflow-hidden animate__animated animate__fadeInUp animate__faster">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white">
                        {{ __('mfg::mfg.complete_order') }}: {{ $completeOrder->code }}
                    </h3>
                    <button wire:click="closeCompleteModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <form wire:submit.prevent="completeOrder" class="p-6 space-y-4">
                    <p class="text-sm text-gray-500">{{ __('mfg::mfg.verify_consumption') }}</p>

                    <div class="space-y-3 max-h-[40vh] overflow-y-auto">
                        @foreach($completeOrder->materialConsumptions as $mc)
                            <div class="flex items-center justify-between gap-4 p-3 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-750">
                                <div class="flex-1">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white block">{{ $mc->product?->translated_name }}</span>
                                    <span class="text-xs text-gray-400">{{ __('mfg::mfg.expected') }} {{ number_format($mc->qty_expected, 4) }}</span>
                                </div>
                                <div class="w-32">
                                    <input type="number" step="0.0001" wire:model="actualConsumptions.{{ $mc->product_id }}" class="w-full px-3 py-2 text-sm bg-white dark:bg-gray-900 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none transition-all">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-4 border-t border-gray-100 dark:border-gray-800 flex justify-end gap-2">
                        <button type="button" wire:click="closeCompleteModal()" class="px-5 py-2 text-sm font-bold bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl transition-all">
                            {{ __('mfg::mfg.cancel') }}
                        </button>
                        <button type="submit" class="px-5 py-2 text-sm font-bold bg-green-600 hover:bg-green-700 text-white rounded-xl shadow-lg transition-all">
                            {{ __('mfg::mfg.complete_order') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

<script>
    function confirmOrderDelete(id) {
        window.dispatchEvent(new CustomEvent('swal:confirm', {
            detail: {
                title: '{{ __('mfg::mfg.confirm_delete_order') }}',
                text: '{{ __('mfg::mfg.delete_confirm_text') }}',
                icon: 'warning',
                confirmButtonText: '{{ __('mfg::mfg.delete') }}',
                cancelButtonText: '{{ __('mfg::mfg.cancel') }}',
                onConfirm: 'delete',
                params: [id]
            }
        }));
    }
</script>
