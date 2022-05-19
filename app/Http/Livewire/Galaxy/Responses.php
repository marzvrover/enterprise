<?php

namespace App\Http\Livewire\Galaxy;

use App\Http\Livewire\Traits\WithFiltering;
use App\Http\Livewire\Traits\WithSorting;
use App\Models\Event;
use App\Models\EventItem;
use App\Models\Form;
use App\Models\Response;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class Responses extends Component
{
    use WithPagination;
    use WithSorting;
    use WithFiltering;

    public Event $event;
    public Form $form;

    public $advanced = [];
    public $advancedChanged = false;

    public $editingItem;
    public $editingTracks;
    public $editingWorkshopId;
    public $showItemModal = false;

    public $filters = ['search' => ''];

    public $perPage = 25;

    protected $listeners = ['refresh' => '$refresh'];

    protected $rules = [
        'editingItem.name' => 'required',
        'editingItem.parent_id' => 'required',
        'editingItem.description' => 'required',
        'editingItem.location' => 'required',
    ];

    public function mount()
    {
        if (isset($this->event)) {
            $this->form = $this->event->workshopForm;
        }
        if ($formId = request()->query('form')) {
            $this->form = Form::with('event')->find($formId);
            $this->event = $this->form->event;
        }

        $this->editingItem = new EventItem();

        if ($this->form->settings->get('searchable')) {
            $this->setAdvancedForm();
        }
    }

    public function updating($field, $value)
    {
        if (strpos($field, 'advanced') !== false) {
            $this->advancedChanged = true;
        }

        if ($field === 'editingItem.parent_id') {
            $this->editingTracks = $this->slots->firstWhere('id', $value)->tagsWithType('tracks')->pluck('name')->join(',');
        }
    }

    public function render()
    {
        return view('livewire.galaxy.responses')
            ->layout('layouts.galaxy', ['title' => 'Responses for ' . $this->form->name])
            ->with([
                'responses' => $this->responses,
                'assignedWorkshops' => $this->assignedWorkshops,
                'advancedSearchForm' => $this->advancedSearchForm,
                'slots' => $this->slots,
                'tracks' => $this->event->tracks,
            ]);
    }

    public function getAdvancedSearchFormProperty()
    {
        if ($this->form->settings->get('searchable')) {
            return $this->form->form->whereIn('id', $this->form->settings->searchable);
        }
    }

    public function getSlotsProperty()
    {
        return $this->event->items->whereNull('parent_id');
    }

    public function getAssignedWorkshopsProperty()
    {
        return $this->event->items->whereNotNull('parent_id')->mapWithKeys(fn ($item) => [$item->settings->get('workshop_id') => $item->id]);
    }

    public function getResponsesProperty()
    {
        return Response::query()
            ->with(['form', 'collaborators'])
            ->when($this->filters['search'] !== '', function ($query) {
                $search = trim($this->filters['search']);

                if ($this->form->settings->get('searchable', []) !== []) {
                    foreach ($this->form->settings->get('searchable') as $index => $item) {
                        $function = $index === 0 ? 'where' : 'orWhere';

                        $query->$function('answers->' . $item, 'LIKE', '%' . $search . '%');
                    }
                } else {
                    $query->where('answers->name', 'LIKE', '%' . $search . '%');
                }

                $query->orWhere('status', 'LIKE', '%' . $search . '%');
            })
            ->when($this->advancedChanged, function ($query) {
                foreach ($this->advanced as $id => $value) {
                    if (is_array($value)) {
                        foreach ($value as $item) {
                            $query->where('answers->' . $id, 'LIKE', '%' . trim($item) . '%');
                        }
                    } elseif (is_string($value) && $value != '') {
                        $query->where('answers->' . $id, 'LIKE', '%' . trim($value) . '%');
                    }
                }
            })
            ->where('form_id', $this->form->id)
            ->paginate($this->perPage);
    }

    public function assignTime($id)
    {
        $workshop = $this->workshops->firstWhere('id', $id);
        $this->editingWorkshop = $workshop;

        $this->editingItem->name = $workshop->answers->get('name');
        $this->editingItem->description = $workshop->answers->get('question-description');

        $this->showItemModal = true;
    }

    public function editItem($id)
    {
        $item = $this->event->items->firstWhere('id', $id);

        $this->editingItem = $item;
        $this->editingTracks = $item->tagsWithType('tracks')->pluck('name')->join(',');

        $this->showItemModal = true;
    }

    public function resetItemModal()
    {
        $this->showItemModal = false;
        $this->editingItem = new EventItem();
        $this->reset('editingTracks', 'editingWorkshopId');
    }

    public function saveItem()
    {
        $parent = $this->slots->firstWhere('id', $this->editingItem->parent_id);

        $this->editingItem->event_id = $this->event->id;
        $this->editingItem->start = $parent->start;
        $this->editingItem->end = $parent->end;
        $this->editingItem->timezone = $parent->timezone;
        $this->editingItem->settings->set('workshop_id', $this->editingWorkshop->id);

        $this->editingItem->save();

        $this->editingItem->syncTagsWithType(explode(',', $this->editingTracks), 'tracks');

        if ($this->editingWorkshop->status !== 'scheduled') {
            $this->editingWorkshop->status = 'scheduled';
            activity()->performedOn($this->editingWorkshop)->withProperties(['comment' => 'Scheduled for ' . $this->editingItem->formattedDuration])->log('scheduled');
            // send notification
        }

        $this->emit('notify', ['message' => 'Successfully assigned time to workshop', 'type' => 'success']);

        $this->resetItemModal();

        $this->emit('refresh');
    }

    public function setAdvancedForm()
    {
        $this->advanced = $this->form->form
            ->whereIn('id', $this->form->settings->searchable)
            ->mapWithKeys(function ($item) {
                if ($item['style'] === 'question') {
                    if ($item['type'] === 'list') {
                        return [$item['id'] => []];
                    }

                    return [$item['id'] => ''];
                }
            })->toArray();
    }
}
