<?php

use App\Models\server;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new  #[Layout('layouts.app')] class extends Component {
    public Collection $servers;

    public function mount(): void
    {
        $this->servers = \App\Models\Server::all();
    }

    public string $label = '';
    public string $url = '';
    public function createSite()
    {
        $this->validate([
            'label' => ['required', 'string', 'max:255'],
        ]);

        server::create([
            'name' => $this->label,
        ]);

        $this->label = '';
        $this->servers = server::all();

        $this->dispatch('close');
    }

    public function deleteServer($id)
    {
        Server::find($id)->delete();
        $this->servers = server::all();
    }

}; ?>

<div class="container mx-auto p-6">
    <x-slot name="header" >
        <div class="flex">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex-1">
                {{ __('server overview') }}
            </h2>

            <button class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded inline-flex items-center"
                    x-on:click.prevent="$dispatch('open-modal', 'server-form')"
                    x-data="{}"
            >
                <i class="fas fa-plus-circle mr-2"></i>
                Add New server
            </button>
        </div>
    </x-slot>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
            <thead class="bg-gray-100">
            <tr>
                <th class="py-3 px-4 text-left">ID</th>
                <th class="py-3 px-4 text-left">Label</th>
                <th class="py-3 px-4 text-left">CPU usage</th>
                <th class="py-3 px-4 text-left">Memory usage</th>
                <th class="py-3 px-4 text-left">Disk usage</th>
                <th class="py-3 px-4 text-left">Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($this->servers as $server)
                @php
                    $latestStat = $server->stats->last();
                    $cpuUsage = round($latestStat->used_cpu) . ' / ' . round($latestStat->total_cpu ). "%";
                    $memoryUsage = round($latestStat->used_memory / 1024 / 1024 / 1024, 2) . ' / ' . $latestStat->total_memory / 1024 / 1024 / 1024 . "GB";

                    $diskTotal = 0;
                    $diskUsed = 0;

                    foreach ($latestStat->disks as $disk) {
                        $disk = new \App\Helpers\MagicArray($disk);
                        $diskTotal += $disk->total_space;
                        $diskUsed += $disk->used_space;
                    }

                    $diskUsage = round($diskUsed / 1024 / 1024 / 1024, 2) . ' / ' . round($diskTotal / 1024 / 1024 / 1024, 2) . "GB";



                @endphp

                <tr class="border-b border-gray-200 hover:bg-gray-50" wire:key="{{$server->id}}">
                    <td class="py-3 px-4">{{ $server->id }}</td>
                    <td class="py-3 px-4">{{ $server->name }}</td>
                    <td class="py-3 px-4">{{ $cpuUsage }}</td>
                    <td class="py-3 px-4">{{ $memoryUsage }}</td>
                    <td class="py-3 px-4">{{ $diskUsage }}</td>


                    <td class="py-3 px-4">
                        <button class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded inline-flex items-center ms-2"
                                x-on:click="confirm('Are you sure you want to delete this server?') && $wire.deleteServer('{{ $server->id }}')"
                        >
                            <i class="fas fa-trash-alt mr-2"></i>
                            Delete
                        </button>

                        <a class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded inline-flex items-center ms-2"
                           href="{{ route('servers.inspect', $server) }}"
                        >
                            <i class="fas fa-edit mr-2"></i>
                            Inspect
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <x-modal name="server-form" :show="$errors->isNotEmpty()" focusable>
        <header class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Create a new server') }}
            </h2>
        </header>

        <form wire:submit.prevent="createSite" class="p-6">
            <div class="mt-6">
                <x-input-label for="label" value="{{ __('Label') }}" class="sr-only" />
                <x-text-input
                    wire:model="label"
                    id="label"
                    name="label"
                    type="text"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('Label') }}"
                />
                <x-input-error :messages="$errors->get('label')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>
                <x-primary-button class="ms-3">
                    {{ __('Create server') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
</div>
