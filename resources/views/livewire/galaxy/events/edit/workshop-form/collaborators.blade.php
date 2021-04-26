<div class="rounded-md dark:bg-gray-700">
    <div class="flex items-center justify-between p-4 bg-gray-100 dark:bg-gray-900 rounded-t-md">
        <span class="dark:text-gray-400">Collaborators {{ $index + 1 }}</span>
        @include('livewire.galaxy.events.edit.workshop-form.toolbar')
    </div>
    @if($openIndex === $index)
    <div class="p-4 space-y-4 rounded-b-md">
        <x-bit.input.group :for="'question-id-'.$index" label="ID">
            <x-bit.input.text type="text" class="w-full mt-1" :id="'question-id-'.$index" wire:model="form.{{ $index }}.id" />
            <x-bit.input.help>Short, unique identifier for question. Use dashes instead of spaces.</x-bit.input.help>
        </x-bit.input.group>
    </div>
    @endif
</div>