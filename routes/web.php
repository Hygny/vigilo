<?php

declare(strict_types=1);

use App\Http\Controllers\AsaasWebhookController;
use App\Http\Controllers\StopImpersonationController;
use App\Livewire\Admin\Organizations\Index as AdminOrganizations;
use App\Livewire\Admin\Organizations\Show as AdminOrganizationShow;
use App\Livewire\Admin\Users;
use App\Livewire\Alerts\Inbox;
use App\Livewire\Billing\Index as Billing;
use App\Livewire\Companies\Graph as CompanyGraph;
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
    Route::get('companies/{company}/grafo', CompanyGraph::class)->name('companies.graph');

    Route::get('alerts', Inbox::class)->name('alerts.index');

    Route::get('usuarios', Users::class)->name('users.index')->middleware('admin');

    // Assinatura da organização — só o admin do tenant gerencia a cobrança.
    Route::get('assinatura', Billing::class)->name('billing.index')->middleware('admin');
});

// Webhook de pagamento do Asaas: público (sem sessão), autenticado pelo token no
// header. Fica fora do grupo auth e é isento de CSRF (ver bootstrap/app.php).
Route::post('webhooks/asaas', AsaasWebhookController::class)->name('webhooks.asaas');

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
