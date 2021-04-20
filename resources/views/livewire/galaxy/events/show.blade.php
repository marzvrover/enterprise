<div>
    <x-bit.nav.tabs :options="$pages" class="mb-8" />

    @switch($page)
        @case('dashboard')
        <livewire:galaxy.events.show.dashboard :event="$event" />
        @break
        @case('workshops')
        <livewire:galaxy.events.show.workshops :event="$event" />
        @break
    @endswitch
</div>
