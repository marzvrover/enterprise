<?php

namespace App\Http\Livewire\Galaxy\Events\Edit;

use App\Models\Event;
use Livewire\Component;

class Settings extends Component
{
    public Event $event;
    public $formChanged = false;

    public $rules = [
        'event.settings.reservations' => 'boolean',
        'event.settings.volunteer_discounts' => 'boolean',
        'event.settings.presenter_discounts' => 'boolean',
        'event.settings.demographics' => 'boolean',
        'event.settings.add_ons' => 'boolean',
        'event.settings.invoices' => 'boolean',
        'event.settings.check_payment' => 'boolean',
        'event.settings.onsite' => 'boolean',
        'event.settings.livestream' => 'boolean',
        'event.settings.has_workshops' => 'boolean',
        'event.settings.has_volunteers' => 'boolean',
        'event.settings.has_sponsorship' => 'boolean',
        'event.settings.has_vendors' => 'boolean',
        'event.settings.has_ads' => 'boolean',
        'event.settings.allow_donations' => 'boolean',
    ];

    public function updating($field)
    {
        if(in_array($field, array_keys($this->rules))) {
            $this->formChanged = true;
        }
    }

    public function render()
    {
        return view('livewire.galaxy.events.edit.settings');
    }

    public function save()
    {
        $this->validate();

        $this->event->save();

        $this->formChanged = false;
        $this->emit('notify', ['message' => 'Successfully updated event settings.', 'type' => 'success']);
    }
}
