<?php

declare(strict_types=1);

use App\Livewire\Alerts\Inbox;
use App\Livewire\Companies\Show as CompanyShow;
use App\Livewire\Dashboard;
use App\Livewire\Portfolios\Index as PortfolioIndex;
use App\Livewire\Portfolios\Show as PortfolioShow;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');

    Route::get('portfolios', PortfolioIndex::class)->name('portfolios.index');
    Route::get('portfolios/{portfolio}', PortfolioShow::class)->name('portfolios.show');

    Route::get('companies/{company}', CompanyShow::class)->name('companies.show');

    Route::get('alerts', Inbox::class)->name('alerts.index');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
