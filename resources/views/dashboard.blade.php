<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-3">
                {{--Sites offline card--}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h1 class="text-2xl font-semibold text-gray-800">Sites offline</h1>
                    <p class="text-gray-500">The following sites are currently offline:</p>

                    <ul class="mt-4">
                        @foreach(\App\Models\Site::all() as $site)
                            @if(!$site->statusses()->get()->last()->up)
                                <li class="text-red-500">{{ $site->name }}</li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
