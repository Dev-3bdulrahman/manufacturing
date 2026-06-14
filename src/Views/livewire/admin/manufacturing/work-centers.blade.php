<div class="p-6 space-y-6" x-data="{ activeTab: 'wc' }">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm">
        <div class="space-y-1">
            <h1 class="text-2xl font-black text-gray-900 dark:text-white">{{ __('mfg::mfg.title') }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('mfg::mfg.mfg_description') }}</p>
        </div>
        <div class="flex gap-2">
            <button x-show="activeTab === 'wc'" wire:click="openWcModal()" class="flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-blue-500/20 transition-all">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>{{ __('mfg::mfg.add_work_center') }}</span>
            </button>
            <button x-show="activeTab === 'machine'" wire:click="openMachineModal()" class="flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-blue-500/20 transition-all">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>{{ __('mfg::mfg.add_machine') }}</span>
            </button>
        </div>
    </div>

    <!-- Tabs Triggers -->
    <div class="flex border-b border-gray-200 dark:border-gray-700 gap-4">
        <button @click="activeTab = 'wc'" :class="activeTab === 'wc' ? 'border-blue-600 text-blue-600 dark:text-blue-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-3 px-1 border-b-2 text-sm font-semibold transition-all">
            {{ __('mfg::mfg.work_centers') }}
        </button>
        <button @click="activeTab = 'machine'" :class="activeTab === 'machine' ? 'border-blue-600 text-blue-600 dark:text-blue-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-3 px-1 border-b-2 text-sm font-semibold transition-all">
            {{ __('mfg::mfg.machines') }}
        </button>
    </div>

    <!-- Tab 1: Work Centers -->
    <div x-show="activeTab === 'wc'" class="space-y-6">
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
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('mfg::mfg.work_center_name') }}</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('mfg::mfg.work_center_code') }}</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('mfg::mfg.cost_per_hour') }}</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('mfg::mfg.status') }}</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">{{ __('mfg::mfg.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($workCenters as $wc)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">{{ $wc->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $wc->code }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-750 dark:text-gray-300">{{ number_format($wc->cost_per_hour, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $wc->status === 'active' ? 'bg-green-50 text-green-700 dark:bg-green-900/20' : 'bg-gray-100 text-gray-500 dark:bg-gray-800' }}">
                                        {{ __('mfg::mfg.' . $wc->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button wire:click="openWcModal({{ $wc->id }})" title="{{ __('mfg::mfg.edit') }}" class="p-1.5 text-gray-500 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-all">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </button>
                                        <button onclick="confirmWcDelete({{ $wc->id }})" title="{{ __('mfg::mfg.delete') }}" class="p-1.5 text-red-500 hover:text-red-700 rounded-lg hover:bg-red-50 transition-all">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">{{ __('mfg::mfg.no_work_centers') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($workCenters->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">{{ $workCenters->links() }}</div>
            @endif
        </div>
    </div>

    <!-- Tab 2: Machines -->
    <div x-show="activeTab === 'machine'" class="space-y-6">
        <!-- Search and Filters -->
        <div class="bg-white dark:bg-gray-900 p-4 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">{{ __('mfg::mfg.search') }}</label>
                <div class="relative flex items-center">
                    <input type="text" wire:model.live.debounce.300ms="machineSearch" placeholder="{{ __('mfg::mfg.search_placeholder') }}" class="w-full ps-10 pe-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
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
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('mfg::mfg.machine_name') }}</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('mfg::mfg.machine_code') }}</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('mfg::mfg.work_centers') }}</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('mfg::mfg.cost_per_hour') }}</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('mfg::mfg.status') }}</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">{{ __('mfg::mfg.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($machines as $machine)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">{{ $machine->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $machine->code }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $machine->workCenter?->name ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-750 dark:text-gray-300">{{ number_format($machine->cost_per_hour, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $machine->status === 'active' ? 'bg-green-50 text-green-700 dark:bg-green-900/20' : 'bg-gray-100 text-gray-500 dark:bg-gray-800' }}">
                                        {{ __('mfg::mfg.' . $machine->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button wire:click="openMachineModal({{ $machine->id }})" title="{{ __('mfg::mfg.edit') }}" class="p-1.5 text-gray-500 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-all">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </button>
                                        <button onclick="confirmMachineDelete({{ $machine->id }})" title="{{ __('mfg::mfg.delete') }}" class="p-1.5 text-red-500 hover:text-red-700 rounded-lg hover:bg-red-50 transition-all">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">{{ __('mfg::mfg.no_machines') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($machines->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">{{ $machines->links() }}</div>
            @endif
        </div>
    </div>

    <!-- Work Center Create/Edit Modal -->
    @if($showWcModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm transition-all">
            <div class="bg-white dark:bg-gray-900 rounded-2xl max-w-lg w-full border border-gray-100 dark:border-gray-800 shadow-2xl overflow-hidden animate__animated animate__fadeInUp animate__faster">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white">
                        {{ $wcId ? __('mfg::mfg.edit_work_center') : __('mfg::mfg.add_work_center') }}
                    </h3>
                    <button wire:click="closeWcModal()" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <form wire:submit.prevent="saveWc" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('mfg::mfg.work_center_name') }} *</label>
                        <input type="text" wire:model="wcName" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        @error('wcName') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('mfg::mfg.work_center_code') }} *</label>
                        <input type="text" wire:model="wcCode" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        @error('wcCode') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('mfg::mfg.cost_per_hour') }} *</label>
                        <input type="number" step="0.01" wire:model="wcCostPerHour" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        @error('wcCostPerHour') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('mfg::mfg.description') }}</label>
                        <textarea wire:model="wcDescription" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"></textarea>
                        @error('wcDescription') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('mfg::mfg.status') }}</label>
                        <select wire:model="wcStatus" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            <option value="active">{{ __('mfg::mfg.active') }}</option>
                            <option value="inactive">{{ __('mfg::mfg.inactive') }}</option>
                        </select>
                        @error('wcStatus') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4 border-t border-gray-100 dark:border-gray-800 flex justify-end gap-2">
                        <button type="button" wire:click="closeWcModal()" class="px-5 py-2 text-sm font-bold bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl transition-all">
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

    <!-- Machine Create/Edit Modal -->
    @if($showMachineModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm transition-all">
            <div class="bg-white dark:bg-gray-900 rounded-2xl max-w-lg w-full border border-gray-100 dark:border-gray-800 shadow-2xl overflow-hidden animate__animated animate__fadeInUp animate__faster">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white">
                        {{ $machineId ? __('mfg::mfg.edit_machine') : __('mfg::mfg.add_machine') }}
                    </h3>
                    <button wire:click="closeMachineModal()" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <form wire:submit.prevent="saveMachine" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('mfg::mfg.machine_name') }} *</label>
                        <input type="text" wire:model="machineName" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        @error('machineName') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('mfg::mfg.machine_code') }} *</label>
                        <input type="text" wire:model="machineCode" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        @error('machineCode') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('mfg::mfg.work_centers') }}</label>
                        <select wire:model="machineWcId" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            <option value="">-- {{ __('mfg::mfg.select_work_center') }} --</option>
                            @foreach($allWorkCenters as $wc)
                                <option value="{{ $wc->id }}">{{ $wc->name }}</option>
                            @endforeach
                        </select>
                        @error('machineWcId') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('mfg::mfg.cost_per_hour') }} *</label>
                        <input type="number" step="0.01" wire:model="machineCostPerHour" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        @error('machineCostPerHour') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('mfg::mfg.status') }}</label>
                        <select wire:model="machineStatus" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            <option value="active">{{ __('mfg::mfg.active') }}</option>
                            <option value="inactive">{{ __('mfg::mfg.inactive') }}</option>
                        </select>
                        @error('machineStatus') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4 border-t border-gray-100 dark:border-gray-800 flex justify-end gap-2">
                        <button type="button" wire:click="closeMachineModal()" class="px-5 py-2 text-sm font-bold bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl transition-all">
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
    function confirmWcDelete(id) {
        window.dispatchEvent(new CustomEvent('swal:confirm', {
            detail: {
                title: '{{ __('mfg::mfg.confirm_delete_wc') }}',
                text: '{{ __('mfg::mfg.delete_confirm_text') }}',
                icon: 'warning',
                confirmButtonText: '{{ __('mfg::mfg.delete') }}',
                cancelButtonText: '{{ __('mfg::mfg.cancel') }}',
                onConfirm: 'deleteWc',
                params: [id]
            }
        }));
    }
    function confirmMachineDelete(id) {
        window.dispatchEvent(new CustomEvent('swal:confirm', {
            detail: {
                title: '{{ __('mfg::mfg.confirm_delete_machine') }}',
                text: '{{ __('mfg::mfg.delete_confirm_text') }}',
                icon: 'warning',
                confirmButtonText: '{{ __('mfg::mfg.delete') }}',
                cancelButtonText: '{{ __('mfg::mfg.cancel') }}',
                onConfirm: 'deleteMachine',
                params: [id]
            }
        }));
    }
</script>
