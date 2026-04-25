{{-- Mi participación --}}
@if($userProject)
<div class="bg-white rounded-xl border border-blue-200 dark:bg-neutral-900 dark:border-blue-900 overflow-hidden">
    <div class="p-6">
        <div class="flex items-center gap-2 mb-4">
            <flux:icon.user-circle class="size-4 text-blue-500" />
            <p class="text-xs font-semibold text-neutral-500 uppercase tracking-widest">Mi participación</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div class="flex items-start gap-2">
                <flux:icon.tag class="size-4 text-neutral-400 mt-0.5 shrink-0" />
                <div class="w-full">
                    <span class="text-neutral-400">Estado:</span>
                    <form id="form-participacion" action="{{ route('project_details.status', $proyecto->id) }}" method="POST" class="mt-1">
                        @csrf
                        @method('PATCH')
                        <select name="project_status_id"
                            class="rounded-md border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 text-sm px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach($statuses as $status)
                            <option value="{{ $status->id }}" @selected($userProject->pivot->project_status_id == $status->id)>
                                {{ $status->name }}
                            </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
            <div class="flex items-start gap-2">
                <flux:icon.clock class="size-4 text-neutral-400 mt-0.5 shrink-0" />
                <div>
                    <span class="text-neutral-400">Última actualización:</span>
                    <p class="font-medium mt-0.5">
                        {{ $userProject->pivot->updated_at ? \Carbon\Carbon::parse($userProject->pivot->updated_at)->format('d/m/Y H:i') : '—' }}
                    </p>
                </div>
            </div>
            <div class="md:col-span-2 flex items-start gap-2">
                <flux:icon.chat-bubble-left-ellipsis class="size-4 text-neutral-400 mt-0.5 shrink-0" />
                <div class="w-full">
                    <span class="text-neutral-400">Notas:</span>
                    <textarea name="notes" form="form-participacion" rows="4"
                        class="mt-1 w-full rounded-md border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-y"
                        placeholder="Escribe tus notas aquí...">{{ $userProject->pivot->notes }}</textarea>
                </div>
            </div>
            <div class="md:col-span-2 flex justify-end">
                <button type="submit" form="form-participacion"
                    class="flex items-center gap-1.5 text-sm bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition">
                    <flux:icon.check class="size-4" /> Guardar cambios
                </button>
            </div>
        </div>
    </div>
</div>
@endif