<?php

use App\Models\Site;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new  #[Layout('layouts.app')] class extends Component {
    public Collection $sites;

    public function mount(): void
    {
        $this->sites = Site::all();
    }

    public string $label = '';
    public string $url = '';
    public function createSite()
    {
        $this->validate([
            'label' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url'],
        ]);

        Site::create([
            'name' => $this->label,
            'base_url' => $this->url,
            'created_by' => auth()->id(),
        ]);

        $this->label = '';
        $this->url = '';

        $this->sites = Site::all();

        $this->dispatch('close');
    }

    public function deleteSite($id)
    {
        Site::find($id)->delete();
        $this->sites = Site::all();
    }

}; ?>

<div class="container mx-auto p-6">
    <x-slot name="header" >
        <div class="flex">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex-1">
                {{ __('Site overview') }}
            </h2>

            <button class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded inline-flex items-center"
                    x-on:click.prevent="$dispatch('open-modal', 'site-form')"
                    x-data="{}"
            >
                <i class="fas fa-plus-circle mr-2"></i>
                Add New Site
            </button>
        </div>
    </x-slot>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
            <thead class="bg-gray-100">
            <tr>
                <th class="py-3 px-4 text-left">Label</th>
                <th class="py-3 px-4 text-left">Site</th>
                <th class="py-3 px-4 text-left">Uptime</th>
                <th class="py-3 px-4 text-left">Status</th>
                <th class="py-3 px-4 text-left">Lighthouse</th>
                <th class="py-3 px-4 text-left">Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($this->sites as $site)
                <tr class="border-b border-gray-200 hover:bg-gray-50" wire:key="{{$site->id}}">
                    <td class="py-3 px-4">{{ $site->name }}</td>
                    <td class="py-3 px-4">
                        <a href="{{ $site->base_url }}" target="_blank" rel="noopener noreferrer" class="text-blue-500 hover:text-blue-600 inline-flex items-center">
                            Visit Site
                            <i class="fas fa-external-link-alt ml-1"></i>
                        </a>
                    </td>
                    <td class="py-3 px-4">
                        @php
                            $uptime = $site->uptime(
                                \Carbon\Carbon::now()->subDays(30),
                            );


                        $uptime_color = match (true) {
                            $uptime >= 99 => [
                                'color' => 'text-green-800',
                                'bg' => 'bg-green-100',
                            ],
                            $uptime >= 95 => [
                                'color' => 'text-yellow-800',
                                'bg' => 'bg-yellow-100',
                               ],
                            default => [
                                'color' => 'text-red-800',
                                'bg' => 'bg-red-100',
                            ],
                        };

                        $uptime_color = $uptime_color['color'] . ' ' . $uptime_color['bg'];
                        @endphp

                        <span class="{{$uptime_color}} px-2 py-1 rounded" title="Uptime">
                            {{$site->uptime(
                                \Carbon\Carbon::now()->subDays(30),
                            )}}%
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        @if($site->statusses->reverse()->first()?->up)
                            <i class="fas fa-check-circle text-green-500"></i>
                        @else
                            <i class="fas fa-times-circle text-red-500"></i>
                        @endif
                    </td>
                    <td class="py-3 px-4">
                        @if($site->lighthouses->count() > 0)
                            @php
                                $lightHouse = $site->lighthouses->reverse()->first();
                                $performance = $lightHouse->performance;
                                $accessibility = $lightHouse->accessibility;
                                $bestPractices = $lightHouse->best_practices;
                                $seo = $lightHouse->seo;
                            @endphp
                            <div class="flex space-x-2">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded" title="Performance">
                                    {{ $performance }}
                                </span>
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded" title="Accessibility">
                                    {{ $accessibility }}
                                </span>
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded" title="Best Practices">
                                    {{ $bestPractices }}
                                </span>
                                <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded" title="SEO">
                                    {{ $seo }}
                                </span>
                            </div>
                        @else
                            <span class="text-gray-500">No data</span>
                        @endif
                    </td>

                    <td class="py-3 px-4">
                        <button class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded inline-flex items-center ms-2"
                                x-on:click="confirm('Are you sure you want to delete this site?') && $wire.deleteSite({{ $site->id }})"
                        >
                            <i class="fas fa-trash-alt mr-2"></i>
                            Delete
                        </button>

                        <a class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded inline-flex items-center ms-2"
                           href="{{ route('sites.inspect', $site) }}"
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

    <x-modal name="site-form" :show="$errors->isNotEmpty()" focusable>
        <header class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Create a new site') }}
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

            <div class="mt-6">
                <x-input-label for="url" value="{{ __('URL') }}" class="sr-only" />
                <x-text-input
                    wire:model="url"
                    id="url"
                    name="url"
                    type="text"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('URL') }}"
                />
                <x-input-error :messages="$errors->get('url')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>
                <x-primary-button class="ms-3">
                    {{ __('Create Site') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
</div>
