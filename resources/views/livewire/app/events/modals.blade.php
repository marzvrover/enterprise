<div>
<section>
    <div class="block h-64 transition duration-150 ease-in-out bg-green-500 shadow">
        <div class="bg-center bg-cover h-2/3 bg-gray-50 dark:bg-gray-850" style="background-image: url({{ $event->backgroundUrl }});">
            <img src="{{ $event->backgroundUrl }}" alt="{{ $event->name }}" class="sr-only">
        </div>
        <div class="px-4 py-2 mx-4 -mt-8 transition duration-150 ease-in-out bg-green-500">
            <p class="text-2xl text-gray-200">{{ $event->name }}</p>
            <p class="text-sm text-gray-300 text-italic">{{ $event->formattedDuration }}</p>
            <p class="text-sm text-gray-300 text-italic">{{ $event->formattedLocation }}</p>
        </div>
    </div>
    <div class="divide-y divide-gray-900">
        <button wire:click="showPolicyModal('description')" class="flex items-center w-full px-6 py-4 space-x-4 text-gray-900 bg-white dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 dark:text-gray-200">
            <x-heroicon-o-calendar class="w-6 h-6 text-gray-600 dark:text-gray-400" />
            <span>Event Description</span>
        </button>
        <button wire:click="showPolicyModal('refund')" class="flex items-center w-full px-6 py-4 space-x-4 text-gray-900 bg-white dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 dark:text-gray-200">
            <x-heroicon-o-receipt-refund class="w-6 h-6 text-gray-600 dark:text-gray-400" />
            <span>Refund Policy</span>
        </button>
        <button wire:click="showPolicyModal('code-inclusion')" class="flex items-center w-full px-6 py-4 space-x-4 text-gray-900 bg-white dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 dark:text-gray-200">
            <x-heroicon-o-document-text class="w-6 h-6 text-gray-600 dark:text-gray-400" />
            <span>Code for Inclusion</span>
        </button>
        <button wire:click="showPolicyModal('photo-policy')" class="flex items-center w-full px-6 py-4 space-x-4 text-gray-900 bg-white dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 dark:text-gray-200">
            <x-heroicon-o-camera class="w-6 h-6 text-gray-600 dark:text-gray-400" />
            <span>Photo Policy</span>
        </button>
    </div>
</section>

    <x-bit.modal.dialog wire:model="showModal" max-width="md">
        <x-slot name="title">{{ $modalTitle ?? '' }}</x-slot>

        <x-slot name="content">
            <div class="prose dark:prose-light">
                {!! markdown($modalContent ?? '') !!}
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-bit.button.flat.secondary size="xs" wire:click="resetModal">Close</x-bit.button.flat.secondary>
        </x-slot>
    </x-bit.modal.dialog>
</div>
