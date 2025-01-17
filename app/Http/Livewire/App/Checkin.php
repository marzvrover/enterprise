<?php

namespace App\Http\Livewire\App;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Checkin extends Component
{
    use AuthorizesRequests;

    public Ticket $ticket;

    public User $user;

    public $event;

    public $editing = false;

    public function rules()
    {
        return [
            'user.name' => 'required',
            'user.pronouns' => '',
            'user.notifications_via' => 'required',
            'user.email' => 'required',
            'user.phone' => Rule::requiredIf(in_array('vonage', $this->user->notifications_via ?? [])),
        ];
    }

    public function mount($ticket = null)
    {
        $this->event = Event::find(8);

        if ($ticket) {
            $this->ticket = $ticket;
            $this->authorize('update', $this->ticket);
            $this->user = $this->ticket->user;
        } elseif (auth()->check()) {
            $ticket = auth()->user()->ticketForEvent($this->event);
            if ($ticket !== null) {
                $this->ticket = $ticket;
                $this->authorize('update', $this->ticket);
                $this->user = $this->ticket->user;
            }
        }
    }

    public function render()
    {
        return view('livewire.app.checkin');
    }

    public function getPositionProperty()
    {
        if (auth()->check() && isset($this->ticket) && $this->ticket->isQueued() && ! $this->ticket->isPrinted()) {
            return DB::table('event_badge_queue')->select('id')->where('printed', false)->where('id', '<', $this->ticket->queue->id)->count();
        }
    }

    public function add()
    {
        // Virtual Ticket
        if ($this->ticket->ticket_type_id === 31) {
            $this->user->save();
            $this->ticket->addToQueue(printed: true);

            return redirect()->route('app.program', ['event' => $this->event, 'page' => 'virtual-schedule']);
        }

        $this->validate();
        $this->user->save();
        $this->ticket->refresh()->addToQueue();

        // Add meal ticket
        if ($ticket = Ticket::where('ticket_type_id', 30)->where('user_id', $this->user->id)->first()) {
            $ticket->refresh()->addToQueue();
        }

        $this->ticket->refresh();

        $this->emit('notify', ['message' => 'Successfully checked in.', 'type' => 'success']);
    }
}
