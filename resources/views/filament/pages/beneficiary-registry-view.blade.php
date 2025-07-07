<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Selector de Actividad -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Selección de Actividad</h2>
            <p class="text-sm text-gray-600 mb-4">Selecciona una actividad para ver y gestionar sus beneficiarios</p>

            <div class="max-w-md">
                <select wire:model.live="selectedActivityId" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    <option value="">Selecciona una actividad...</option>
                    @foreach($this->getActivities() as $id => $description)
                        <option value="{{ $id }}">{{ $description }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if($selectedActivityId)
            <!-- Información de la Actividad Seleccionada -->
            @php $activity = $this->getSelectedActivity(); @endphp
            @if($activity)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="text-md font-semibold text-blue-900 mb-2">Actividad Seleccionada</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-blue-800">Descripción:</span>
                            <p class="text-blue-700">{{ $activity->description }}</p>
                        </div>
                        <div>
                            <span class="font-medium text-blue-800">Responsable:</span>
                            <p class="text-blue-700">{{ $activity->responsible->name ?? 'No asignado' }}</p>
                        </div>
                        <div>
                            <span class="font-medium text-blue-800">Total Beneficiarios:</span>
                            <p class="text-blue-700 font-semibold">{{ $this->getBeneficiariesCount() }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Formulario para Nuevo Beneficiario -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Nuevo Beneficiario</h2>
                <p class="text-sm text-gray-600 mb-4">Agregar un nuevo beneficiario a la actividad seleccionada</p>

                <form wire:submit="saveBeneficiary" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Apellido Paterno *</label>
                            <input type="text" wire:model="beneficiaryForm.last_name"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                   required>
                            @error('beneficiaryForm.last_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Apellido Materno *</label>
                            <input type="text" wire:model="beneficiaryForm.mother_last_name"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                   required>
                            @error('beneficiaryForm.mother_last_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombres *</label>
                            <input type="text" wire:model="beneficiaryForm.first_names"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                   required>
                            @error('beneficiaryForm.first_names')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Año de Nacimiento</label>
                            <input type="number" wire:model="beneficiaryForm.birth_year"
                                   min="1900" max="{{ date('Y') }}"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            @error('beneficiaryForm.birth_year')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Género *</label>
                            <select wire:model="beneficiaryForm.gender"
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                    required>
                                <option value="">Selecciona...</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                                <option value="Otro">Otro</option>
                            </select>
                            @error('beneficiaryForm.gender')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                            <input type="tel" wire:model="beneficiaryForm.phone"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            @error('beneficiaryForm.phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                        <textarea wire:model="beneficiaryForm.address_backup" rows="3"
                                  class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500"></textarea>
                        @error('beneficiaryForm.address_backup')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Firma (opcional)</label>
                        <textarea wire:model="beneficiaryForm.signature" rows="2"
                                  class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500"></textarea>
                        @error('beneficiaryForm.signature')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span>Guardar Beneficiario</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabla de Beneficiarios -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Beneficiarios de la Actividad</h2>
                    <p class="text-sm text-gray-600">Total: {{ $this->getBeneficiariesCount() }} beneficiarios</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Apellido Paterno</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Apellido Materno</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombres</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Año Nacimiento</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Género</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registrado el</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($this->getBeneficiaries() as $beneficiary)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $beneficiary->last_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $beneficiary->mother_last_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $beneficiary->first_names }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $beneficiary->birth_year }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            {{ $beneficiary->gender === 'Masculino' ? 'bg-blue-100 text-blue-800' :
                                               ($beneficiary->gender === 'Femenino' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ $beneficiary->gender }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $beneficiary->phone }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $beneficiary->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('filament.admin.resources.beneficiary-registries.edit', $beneficiary) }}"
                                           class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>
                                        <button wire:click="deleteBeneficiary({{ $beneficiary->id }})"
                                                class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('¿Estás seguro de eliminar este beneficiario?')">
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No hay beneficiarios registrados para esta actividad.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <!-- Mensaje cuando no hay actividad seleccionada -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Selecciona una Actividad</h3>
                <p class="text-gray-600">Para ver y gestionar beneficiarios, primero selecciona una actividad de la lista superior.</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
