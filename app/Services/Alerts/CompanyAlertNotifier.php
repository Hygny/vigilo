<?php

declare(strict_types=1);

namespace App\Services\Alerts;

use App\Jobs\SendWebhookNotification;
use App\Models\ChangeEvent;
use App\Models\MonitoredCompany;
use App\Notifications\CompanyChangeDetected;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

/**
 * Entrega os alertas de uma empresa monitorada: notifica os usuários da
 * organização e dispara o webhook de saída (se configurado). Fonte única usada
 * pelo refresh da Receita (mudanças cadastrais, Lista Suja) e pela varredura
 * diária de sanções — os dois caminhos geram change_events e avisam do mesmo jeito.
 */
final class CompanyAlertNotifier
{
    /**
     * @param  EloquentCollection<int, ChangeEvent>  $events
     */
    public function notify(MonitoredCompany $company, string $razaoSocial, EloquentCollection $events): void
    {
        if ($events->isEmpty()) {
            return;
        }

        $organization = $company->portfolio->organization;

        if ($organization->users->isNotEmpty()) {
            Notification::send($organization->users, new CompanyChangeDetected($company, $razaoSocial, $events));
        }

        // A entrega assinada/retry fica no job dedicado.
        if ($organization->hasWebhook()) {
            SendWebhookNotification::dispatch($organization->id, [
                'event_id' => (string) Str::uuid(),
                'event' => 'company.changes',
                'cnpj' => $company->cnpj,
                'razao_social' => $razaoSocial,
                'detectado_em' => Carbon::now()->toIso8601String(),
                'mudancas' => $events->map(fn (ChangeEvent $event): array => [
                    'tipo' => $event->type->value,
                    'campo' => $event->field,
                    'de' => $event->old_value,
                    'para' => $event->new_value,
                    'severidade' => $event->severity->value,
                ])->values()->all(),
            ]);
        }
    }
}
