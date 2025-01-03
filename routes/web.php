<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

\Livewire\Volt\Volt::route('sites', 'pages.sites.site-overview')
    ->middleware(['auth'])
    ->name('sites');

\Livewire\Volt\Volt::route('sites/{site}', 'pages.sites.inspect')
    ->middleware(['auth'])
    ->name('sites.inspect');

\Livewire\Volt\Volt::route('sites/{site}/playwright', 'pages.sites.playwright-editor')
    ->middleware(['auth'])
    ->name('sites.playwright');

\Livewire\Volt\Volt::route('incidents', 'pages.incident')
    ->middleware(['auth'])
    ->name('incidents');

require __DIR__.'/auth.php';
