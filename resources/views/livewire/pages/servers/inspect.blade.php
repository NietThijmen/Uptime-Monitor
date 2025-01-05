<?php

use Illuminate\Support\Collection;
use Livewire\Volt\Component;

new #[\Livewire\Attributes\Layout('layouts.app')] class extends Component {

    public \App\Models\Server $server;
    public function mount(\App\Models\Server $server)
    {
        $this->server = $server;
    }
}; ?>

<x-slot name="header">
    <div class="flex justify-around">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex-1">
            {{ __('Server information') }}
        </h2>
    </div>
</x-slot>

<div class="py-12">


    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-3 gap-3">

        </div>
    </div>
</div>
