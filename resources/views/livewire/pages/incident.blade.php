<?php

use Illuminate\Support\Collection;
use Livewire\Volt\Component;

new #[\Livewire\Attributes\Layout('layouts.app')] class extends Component {
    public Collection $incidents;

    #[\Livewire\Attributes\Url('incident', true)]
    public int $incident_id;
    public \App\Models\Incident $incident;
    public Collection $messages;

    public string $message;


    public function setIncident($id)
    {
        $this->incident_id = $id;
        $this->incident = \App\Models\Incident::find($id);
        $this->messages = $this->incident->comments->reverse();

        // update the URL

    }

    public function setStatus($status)
    {
        $this->incident->status = $status;
        $this->incident->save();
        $this->incident = \App\Models\Incident::find($this->incident->id);
    }

    public function sendMessage()
    {
        \App\Models\IncidentComment::create([
            'message' => $this->message,
            'user_id' => auth()->id(),
            'incident_id' => $this->incident->id
        ]);

        $this->message = '';
        $this->messages = $this->incident->comments->reverse();
    }

    public function mount()
    {
        $this->incidents = \App\Models\Incident::all();

        if(request('incident')) {
            $this->setIncident(request('incident'));
        }
    }
}; ?>
<div class="flex h-screen">

    <div class="w-80 bg-gray-50 border-l p-6 overflow-auto">
        <h2 class="text-xl font-semibold mb-4">Incidents</h2>
        <ul class="space-y-2">
            @foreach($incidents as $incident)
                <li>
                    <button class="w-full block p-2 bg-white rounded-lg shadow-md" wire:click="setIncident({{$incident->id}})">
                        <h3 class="font-semibold">{{ $incident->title }}</h3>
                        <p class="text-sm text-gray-500">{{ $incident->site->name }}</p>
                        <p class="text-sm text-gray-500">{{ $incident->created_at->diffForHumans() }}</p>
                    </button>
                </li>
            @endforeach
        </ul>
    </div>


    @if(isset($this->incident))
        <div class="flex-1 p-6 overflow-auto">
            <!-- Incident Card -->
            <div class="bg-white rounded-lg shadow-md mb-6">
                <div class="p-4 border-b">
                    <h2 class="text-xl font-semibold flex justify-between items-center">
                        {{$incident->title}}
                        <span
                            class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">
                            {{$incident->status}}
                        </span>
                    </h2>
                </div>
                <div class="p-4">
                    <p>
                        {{$incident->description}}
                    </p>
                </div>
            </div>

            <!-- Status Dropdown (non-functional in static HTML) -->
            <div class="relative inline-block text-left">
                <select id="status" name="status" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                    wire:change="setStatus($event.target.value)">
                >
                    <option value="open" @if($incident->status == 'open') selected @endif>Open</option>
                    <option value="closed" @if($incident->status == 'closed') selected @endif>Closed</option>
                </select>
            </div>

            <!-- Chat Section -->
            <div class="bg-white rounded-lg shadow-md mt-6">
                <div class="p-4 border-b">
                    <h2 class="text-xl font-semibold">Chat</h2>
                </div>
                <div class="p-4">
                    <div class="space-y-4">
                        @foreach($messages as $message)
                            <div class="flex flex-col">
                                <span class="font-semibold">{{ $message->user->name }}</span>
                                <span>{{ $message->message }}</span>
                                <span class="text-sm text-gray-500">{{ $message->created_at->diffForHumans() }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <form class="flex space-x-2" wire:submit="sendMessage">
                            <input
                                type="text"
                                placeholder="Type your message..."
                                class="flex-grow px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                wire:model.live="message"
                            />
                            <button
                                type="submit"
                                class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            >
                                Send
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    @endif
</div>
