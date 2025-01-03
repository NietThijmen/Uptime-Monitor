<?php

use Illuminate\Support\Collection;
use Livewire\Volt\Component;

new #[\Livewire\Attributes\Layout('layouts.app')] class extends Component {
    public \App\Models\Site $site;

    public int $lighthouse_performance = 0;
    public int $lighthouse_accessibility = 0;
    public int $lighthouse_bestPractices = 0;
    public int $lighthouse_seo = 0;
    public string $lighthouse_checkedAt = '';

    public string $css_checker_checkedAt = '';
    public Collection $css_checker_data;

    public array $response_time_data = [];
    public array $lighthouse_data = [];

    private function get_latest_lighthouse()
    {
        $lighthouse = \App\Models\SiteLighthouse::latest()->first();
        $this->lighthouse_performance = $lighthouse?->performance ?? 0;
        $this->lighthouse_accessibility = $lighthouse?->accessibility ?? 0;
        $this->lighthouse_bestPractices = $lighthouse?->best_practices ?? 0;
        $this->lighthouse_seo = $lighthouse?->seo ?? 0;
        $this->lighthouse_checkedAt = $lighthouse?->created_at ?? now();
    }

    private function get_latest_css_checker()
    {
        $css_checker_batch = \App\Models\SiteCss::where('site_id', $this->site->id)->max('batch');
        $this->css_checker_data = \App\Models\SiteCss::where('site_id', $this->site->id)->where('batch', $css_checker_batch)->get();
        $this->css_checker_checkedAt = $this->css_checker_data->first()->created_at;
    }


    private function get_response_time_data_24_hours()
    {
        $data = [
            'type' => 'line',
            'labels' => [],
            'datasets' => [
                [
                    'label' => 'Response time',
                    'data' => [],
                ],
            ],
        ];

        $response_times = \App\Models\SiteStatus::where('site_id', $this->site->id)
            ->where('created_at', '>', now()->subDay())
            ->get();

        $tmp_data = [];

        foreach ($response_times as $response_time) {
            $tmp_data[$response_time->created_at->format('H:i')] = $response_time->response_time;
        }

        $data['labels'] = array_keys($tmp_data);
        $data['datasets'][0]['data'] = array_values($tmp_data);

        $this->response_time_data = $data;
    }

    private function get_lighthouse_data_24_hours()
    {
        $data = [
            'type' => 'line',
            'labels' => [],
            'datasets' => [
                [
                    'label' => 'Performance',
                    'data' => [],
                ],
                [
                    'label' => 'Accessibility',
                    'data' => [],
                ],
                [
                    'label' => 'Best practices',
                    'data' => [],
                ],
                [
                    'label' => 'SEO',
                    'data' => [],
                ],
            ],
        ];

        $lighthouse_data = \App\Models\SiteLighthouse::where('site_id', $this->site->id)
            ->where('created_at', '>', now()->subDay())
            ->get();

        $tmp_data = [
            'performance' => [],
            'accessibility' => [],
            'best_practices' => [],
            'seo' => [],
        ];

        foreach ($lighthouse_data as $lighthouse) {
            $tmp_data['performance'][$lighthouse->created_at->format('H:i')] = $lighthouse->performance;
            $tmp_data['accessibility'][$lighthouse->created_at->format('H:i')] = $lighthouse->accessibility;
            $tmp_data['best_practices'][$lighthouse->created_at->format('H:i')] = $lighthouse->best_practices;
            $tmp_data['seo'][$lighthouse->created_at->format('H:i')] = $lighthouse->seo;
        }

        $data['labels'] = array_keys($tmp_data['performance']);
        $data['datasets'][0]['data'] = array_values($tmp_data['performance']);
        $data['datasets'][1]['data'] = array_values($tmp_data['accessibility']);
        $data['datasets'][2]['data'] = array_values($tmp_data['best_practices']);
        $data['datasets'][3]['data'] = array_values($tmp_data['seo']);

        $this->lighthouse_data = $data;
    }

    public function mount(\App\Models\Site $site)
    {
        $this->site = $site;

        if (request('timerange')) {
            $this->chart_timerange = request('timerange');
        }
        $this->get_latest_lighthouse();
        $this->get_latest_css_checker();
        $this->get_response_time_data_24_hours();
        $this->get_lighthouse_data_24_hours();
    }
}; ?>

<x-slot name="header">
    <div class="flex justify-around">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex-1">
            {{ __('Site information') }}
        </h2>

        <div class="flex gap-2">
            <a
                class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded inline-flex items-center"
                href="{{ route('sites.playwright', $site) }}"
                wire:navigate
            >
                <i class="fas fa-plus-circle mr-2"></i>
                Generate Playwright
            </a>

            <button
                class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded inline-flex items-center"
                x-on:click.prevent="$dispatch('open-modal', 'site-form')"
                x-data="{}"
            >
                <i class="fas fa-file-pdf mr-2"></i>
                Generate PDF
            </button>
        </div>
    </div>
</x-slot>

<div class="py-12">


    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-3 gap-3">

            {{-- LIGHTHOUSE--}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h1 class="text-2xl font-semibold text-gray-800">Lighthouse statistics</h1>
                <p class="text-gray-500">These are the latest lighthouse statistics</p>

                <div class="mt-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h2 class="text-xl font-semibold text-gray-800">Performance</h2>
                            <p class="text-gray-500">The performance score is {{ $lighthouse_performance }}.</p>
                        </div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h2 class="text-xl font-semibold text-gray-800">Accessibility</h2>
                            <p class="text-gray-500">The accessibility score is {{ $lighthouse_accessibility }}.</p>
                        </div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h2 class="text-xl font-semibold text-gray-800">Best practices</h2>
                            <p class="text-gray-500">The best practices score is {{ $lighthouse_bestPractices }}.</p>
                        </div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h2 class="text-xl font-semibold text-gray-800">SEO</h2>
                            <p class="text-gray-500">The SEO score is {{ $lighthouse_seo}}.</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <p class="text-gray-500">These scores were last checked at<br/> {{ $lighthouse_checkedAt }}.</p>
                    </div>
                </div>
            </div>


            {{--CSS CHECKER--}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 ">
                <h1 class="text-2xl font-semibold text-gray-800">CSS checker</h1>
                <p class="text-gray-500">These are the latest CSS checker statistics</p>

                <div class="mt-4">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h2 class="text-xl font-semibold text-gray-800">CSS checker</h2>
                        <p class="text-gray-500">The CSS checker
                            found {{ $css_checker_data->where('active', 0)->count() }} issues.</p>
                    </div>


                    <div class="flex flex-col">
                        @foreach($css_checker_data as $data)
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mt-4 flex">


                                <p class="font-semibold text-gray-800">
                                    @if($data->active)
                                        <i class="fas fa-check-circle text-green-500 "></i>
                                    @else
                                        <i class="fas fa-times-circle text-red-500"></i>
                                    @endif
                                    {{ str_replace($site->base_url, '', $data->url) }}

                                </p>

                            </div>
                        @endforeach
                    </div>


                    <div class="mt-4">
                        <p class="text-gray-500">These issues were last checked at<br/> {{ $css_checker_checkedAt }}.
                        </p>
                    </div>
                </div>
            </div>

            {{--Uptime overview--}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 ">
                <h1 class="text-2xl font-semibold text-gray-800">Uptime overview</h1>
                <p class="text-gray-500">View your uptime in the last periods</p>

                <div class="mt-4">
                    <div class="flex flex-col">
                        {{--                        @foreach($css_checker_data as $data)--}}
                        {{--                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mt-4 flex">--}}


                        {{--                                <p class="font-semibold text-gray-800">--}}
                        {{--                                    @if($data->active)--}}
                        {{--                                        <i class="fas fa-check-circle text-green-500 "></i>--}}
                        {{--                                    @else--}}
                        {{--                                        <i class="fas fa-times-circle text-red-500"></i>--}}
                        {{--                                    @endif--}}
                        {{--                                    {{ str_replace($site->base_url, '', $data->url) }}--}}

                        {{--                                </p>--}}
                        {{--                                --}}

                        {{--                            </div>--}}
                        {{--                        @endforeach--}}

                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mt-4 flex">
                            <p class="font-semibold text-gray-800">
                                24 hours {{ $site->uptime(\Carbon\Carbon::now()->subDay())  }}
                            </p>
                        </div>

                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mt-4 flex">
                            <p class="font-semibold text-gray-800">
                                7 days {{ $site->uptime(\Carbon\Carbon::now()->subDays(7))  }}
                            </p>
                        </div>

                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mt-4 flex">
                            <p class="font-semibold text-gray-800">
                                30 days {{ $site->uptime(\Carbon\Carbon::now()->subDays(30))  }}
                            </p>
                        </div>

                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mt-4 flex">
                            <p class="font-semibold text-gray-800">
                                90 days {{ $site->uptime(\Carbon\Carbon::now()->subDays(90))  }}
                            </p>
                        </div>

                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mt-4 flex">
                            <p class="font-semibold text-gray-800">
                                365 days {{ $site->uptime(\Carbon\Carbon::now()->subDays(365))  }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{--RESPONSE TIME--}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 col-span-3">
                <canvas data-chart='@json($response_time_data)'></canvas>
            </div>

            {{--LIGHTHOUSE--}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 col-span-3">
                <canvas data-chart='@json($lighthouse_data)'></canvas>
            </div>
        </div>
