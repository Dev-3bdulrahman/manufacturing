<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm">
        <div class="space-y-1">
            <h1 class="text-2xl font-black text-gray-900 dark:text-white">{{ __('mfg::mfg.boms') }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('mfg::mfg.bom_description') }}</p>
        </div>
        <button wire:click="openCreateModal()" class="flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-blue-500/20 transition-all">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>{{ __('mfg::mfg.add_bom') }}</span>
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
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('mfg::mfg.bom_name') }}</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('mfg::mfg.bom_code') }}</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('mfg::mfg.finished_product') }}</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('mfg::mfg.base_quantity') }}</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('mfg::mfg.bom_items') }}</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('mfg::mfg.status') }}</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">{{ __('mfg::mfg.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($boms as $bom)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">{{ $bom->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $bom->code }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-700 dark:text-gray-300">
                                {{ $bom->product?->translated_name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ number_format($bom->quantity, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                @foreach($bom->items as $item)
                                    <span class="inline-block bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs px-2 py-0.5 rounded mr-1 mb-1">
                                        {{ $item->product?->translated_name ?? '-' }} ({{ number_format($item->quantity, 2) }})
                                    </span>
                                @endforeach
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $bom->is_active ? 'bg-green-50 text-green-700 dark:bg-green-900/20' : 'bg-gray-100 text-gray-500 dark:bg-gray-800' }}">
                                        {{ $bom->is_active ? __('mfg::mfg.active') : __('mfg::mfg.inactive') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="openEditModal({{ $bom->id }})" title="{{ __('mfg::mfg.edit') }}" class="p-1.5 text-gray-500 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-all">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </button>
                                    <button onclick="confirmBomDelete({{ $bom->id }})" title="{{ __('mfg::mfg.delete') }}" class="p-1.5 text-red-500 hover:text-red-700 rounded-lg hover:bg-red-50 transition-all">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">{{ __('mfg::mfg.no_boms') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($boms->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">{{ $boms->links() }}</div>
        @endif
    </div>

    <!-- Create/Edit Modal -->
    @if($showFormModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm transition-all">
            <div class="bg-white dark:bg-gray-900 rounded-2xl max-w-2xl w-full border border-gray-100 dark:border-gray-800 shadow-2xl overflow-hidden animate__animated animate__fadeInUp animate__faster">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white">
                        {{ $bomId ? __('mfg::mfg.edit_bom') : __('mfg::mfg.add_bom') }}
                    </h3>
                    <button wire:click="closeFormModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <form wire:submit.prevent="save" class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('mfg::mfg.bom_name') }} *</label>
                            <input type="text" wire:model="name" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all">
                            @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('mfg::mfg.bom_code') }} *</label>
                            <input type="text" wire:model="code" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all">
                            @error('code') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('mfg::mfg.finished_product') }} *</label>
                            <select wire:model="productId" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all">
                                <option value="">-- {{ __('mfg::mfg.select_product') }} --</option>
                                @foreach($finishedProducts as $prod)
                                    <option value="{{ $prod->id }}">{{ $prod->translated_name }}</option>
                                @endforeach
                            </select>
                            @error('productId') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('mfg::mfg.base_quantity') }} *</label>
                            <input type="number" step="0.01" wire:model="quantity" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all">
                            @error('quantity') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" wire:model="isActive" id="isActive" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="isActive" class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('mfg::mfg.active') }}</label>
                    </div>

                    <!-- Raw materials section -->
                    <div class="border-t border-gray-100 dark:border-gray-800 pt-4 space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-black text-gray-900 dark:text-white">{{ __('mfg::mfg.bom_items') }}</h4>
                            <button type="button" wire:click="addMaterial()" class="text-xs font-bold text-blue-600 hover:text-blue-800 transition-all">+ {{ __('mfg::mfg.add_material') }}</button>
                        </div>

                        <div class="space-y-3">
                            @foreach($items as $index => $item)
                                <div class="flex items-center gap-3">
                                    <div class="flex-1">
                                        <select wire:model="items.{{ $index }}.product_id" class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none transition-all">
                                            <option value="">-- {{ __('mfg::mfg.material') }} --</option>
                                            @foreach($rawMaterials as $raw)
                                                <option value="{{ $raw->id }}">{{ $raw->translated_name }} [{{ strtoupper($raw->type) }}]</option>
                                            @endforeach
                                        </select>
                                        @error("items.{$index}.product_id") <span class="text-xs text-red-500 mt-0.5 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="w-32">
                                        <input type="number" step="0.0001" wire:model="items.{{ $index }}.quantity" placeholder="{{ __('mfg::mfg.qty') }}" class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none transition-all">
                                        @error("items.{$index}.quantity") <span class="text-xs text-red-500 mt-0.5 block">{{ $message }}</span> @enderror
                                    </div>
                                    <button type="button" wire:click="removeMaterial({{ $index }})" title="{{ __('mfg::mfg.delete') }}" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-all">
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
</div>

<script>
    function confirmBomDelete(id) {
        window.dispatchEvent(new CustomEvent('swal:confirm', {
            detail: {
                title: '{{ __('mfg::mfg.confirm_delete_bom') }}',
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
