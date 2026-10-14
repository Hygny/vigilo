<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\Severity;
use App\Models\ChangeEvent;
use App\Models\MonitoredCompany;
use App\Support\Cnpj;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Alerts an organization that one or more changes were detected on a monitored
 * company. Delivered in-app (database) and by e-mail.
 *
 * Queued: a delivery failure (SMTP down) never aborts the caller — the refresh
 * job or the sanctions sweep, where an inline e-mail error used to stop the rest
 * of the portfolio. Each channel is its own queued job, retried with backoff
 * (~20 min of outage absorbed before it lands in failed_jobs).
 */
class CompanyChangeDetected extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 4;

    /** @var list<int> */
    public array $backoff = [60, 300, 900];

    /** Company removed before delivery → drop the alert instead of failing. */
    public bool $deleteWhenMissingModels = true;

    /**
     * @param  Collection<int, ChangeEvent>  $events
     */
    public function __construct(
        public MonitoredCompany $company,
        public string $razaoSocial,
        public Collection $events,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject("[Vigilo] Mudança cadastral: {$this->razaoSocial}")
            ->greeting('Alerta de monitoramento (KYB)')
            ->line("Detectamos {$this->events->count()} mudança(s) no cadastro de **{$this->razaoSocial}** (CNPJ {$this->formattedCnpj()}).");

        foreach ($this->events as $event) {
            $mail->line("• [{$event->severity->label()}] {$event->type->label()} — {$this->describe($event)}");
        }

        return $mail->action('Ver empresa', url("/companies/{$this->company->id}"));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $highest = $this->highestSeverity();

        return [
            'monitored_company_id' => $this->company->id,
            'cnpj' => $this->company->cnpj,
            'razao_social' => $this->razaoSocial,
            'label' => $this->company->label,
            'changes_count' => $this->events->count(),
            'highest_severity' => $highest->value,
            'changes' => $this->events->map(fn (ChangeEvent $event): array => [
                'type' => $event->type->value,
                'type_label' => $event->type->label(),
                'field' => $event->field,
                'old_value' => $event->old_value,
                'new_value' => $event->new_value,
                'severity' => $event->severity->value,
            ])->all(),
        ];
    }

    private function highestSeverity(): Severity
    {
        return $this->events
            ->map(fn (ChangeEvent $event): Severity => $event->severity)
            ->sortByDesc(fn (Severity $severity): int => $severity->weight())
            ->first() ?? Severity::Low;
    }

    private function describe(ChangeEvent $event): string
    {
        return match (true) {
            $event->old_value === null => "novo valor: {$event->new_value}",
            $event->new_value === null => "valor removido: {$event->old_value}",
            default => "de \"{$event->old_value}\" para \"{$event->new_value}\"",
        };
    }

    private function formattedCnpj(): string
    {
        return Cnpj::tryFrom($this->company->cnpj)?->formatted() ?? $this->company->cnpj;
    }
}
