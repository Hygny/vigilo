<?php

declare(strict_types=1);

use App\Http\Controllers\StopImpersonationController;
use App\Livewire\Admin\Organizations\Index as AdminOrganizations;
use App\Livewire\Admin\Organizations\Show as AdminOrganizationShow;
use App\Livewire\Admin\Users;
use App\Livewire\Alerts\Inbox;
use App\Livewire\Companies\Show as CompanyShow;
use App\Livewire\Dashboard;
use App\Livewire\Portfolios\Index as PortfolioIndex;
use App\Livewire\Portfolios\Show as PortfolioShow;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');

    Route::get('portfolios', PortfolioIndex::class)->name('portfolios.index');
    Route::get('portfolios/{portfolio}', PortfolioShow::class)->name('portfolios.show');

    Route::get('companies/{company}', CompanyShow::class)->name('companies.show');

    Route::get('alerts', Inbox::class)->name('alerts.index');

    Route::get('usuarios', Users::class)->name('users.index')->middleware('admin');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Encerra a personificação (disparada pelo banner na sessão personificada).
Route::post('impersonate/stop', StopImpersonationController::class)
    ->middleware('auth')
    ->name('impersonate.stop');

// Painel do super-admin (dono da plataforma) — back-office sobre todas as orgs.
Route::middleware(['auth', 'super-admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::redirect('/', '/admin/organizations');
        Route::get('organizations', AdminOrganizations::class)->name('organizations.index');
        Route::get('organizations/{organization}', AdminOrganizationShow::class)->name('organizations.show');
    });

require __DIR__.'/auth.php';
