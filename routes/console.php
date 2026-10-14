<?php

declare(strict_types=1);

use App\Models\WebhookDelivery;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Alerta de sanção nova (CGU): varre a carteira inteira 1× por dia, fora do
// refresh da Receita, na janela em que a CGU libera mais req/min (00–06h de
// Brasília — a app roda em UTC, daí o fuso explícito). Duas passadas: a das
// 04:00 só retoma o que a da 01:00 não checou (disjuntor, deploy no meio).
// Um único evento = um único mutex, então as passadas nunca se sobrepõem. O
// teto do mutex (170 min) fica abaixo das 3h entre as passadas — se o processo
// em background morrer, a das 04:00 já encontra o mutex liberado. Comporta
// ~20 mil empresas a 120/min; acima disso, subir o teto junto com o intervalo
// entre as passadas. runInBackground para não segurar o scheduler.
Schedule::command('vigilo:varrer-sancoes')
    ->cron('0 1,4 * * *')
    ->timezone((string) config('risco.sweep.timezone'))
    ->name('vigilo-sweep-sanctions')
    ->withoutOverlapping(170)
    ->runInBackground();

// KYB contínuo: todo dia às 03:00 dispara os refreshes dos portfólios agendados
// para aquele dia (dia configurável, e vários dias, por portfólio).
Schedule::command('portfolios:run-scheduled')
    ->dailyAt('03:00')
    ->name('vigilo-portfolio-schedules')
    ->withoutOverlapping();

// Poda o log append-only de entregas de webhook (retenção em config/webhook.php).
Schedule::command('model:prune', ['--model' => [WebhookDelivery::class]])
    ->dailyAt('03:30')
    ->name('vigilo-prune-webhook-deliveries')
    ->withoutOverlapping();
