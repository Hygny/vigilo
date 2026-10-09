<?php

declare(strict_types=1);

namespace App\Livewire\Integrations;

use App\Models\User;
use App\Rules\PublicHttpsUrl;
use App\Services\Webhooks\WebhookSender;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Tela de Integrações da organização (só o admin do tenant): configura o webhook
 * de saída (URL + segredo HMAC), envia um teste e mostra as últimas entregas.
 * O segredo NÃO é pré-carregado no formulário (não trafega a cada load); o campo
 * serve para definir/gerar um novo — o existente é mantido se o campo ficar vazio.
 */
#[Layout('layouts.app')]
class Index extends Component
{
    public string $webhookUrl = '';

    public string $webhookSecret = '';

    public function mount(): void
    {
        $user = $this->currentUser();
        abort_unless($user->isAdmin(), 403);
        abort_if($user->organization === null, 404);

        $this->webhookUrl = $user->organization->webhook_url ?? '';
    }

    /** Gera um segredo forte para o admin copiar ao consumidor. */
    public function generateSecret(): void
    {
        abort_unless($this->currentUser()->isAdmin(), 403);

        $this->webhookSecret = Str::random(40);
    }

    public function save(): void
    {
        $user = $this->currentUser();
        abort_unless($user->isAdmin(), 403);

        $validated = $this->validate([
            'webhookUrl' => ['nullable', 'url', 'max:2048', new PublicHttpsUrl],
            'webhookSecret' => ['nullable', 'string', 'min:16', 'max:255'],
        ], [
            'webhookUrl.url' => 'Informe uma URL válida (https://...).',
            'webhookSecret.min' => 'O segredo deve ter ao menos 16 caracteres.',
        ]);

        $organization = $user->organization;
        abort_if($organization === null, 404);

        $url = $validated['webhookUrl'] !== '' ? $validated['webhookUrl'] : null;

        // Sem URL → desliga o webhook (limpa os dois).
        if ($url === null) {
            $organization->forceFill(['webhook_url' => null, 'webhook_secret' => null])->save();
            $this->webhookSecret = '';
            session()->flash('status', 'Webhook desativado.');

            return;
        }

        // Segredo: usa o novo (campo preenchido) ou mantém o existente.
        $secret = $validated['webhookSecret'] !== '' ? $validated['webhookSecret'] : $organization->webhook_secret;

        if ($secret === null) {
            $this->addError('webhookSecret', 'Defina um segredo para assinar as entregas.');

            return;
        }

        $organization->forceFill(['webhook_url' => $url, 'webhook_secret' => $secret])->save();
        $this->webhookSecret = '';
        session()->flash('status', 'Integração salva.');
    }

    /** Envia um POST de teste (síncrono) e registra o resultado. */
    public function sendTest(): void
    {
        $user = $this->currentUser();
        abort_unless($user->isAdmin(), 403);

        $organization = $user->organization;

        if ($organization === null || ! $organization->hasWebhook()) {
            $this->addError('webhookUrl', 'Configure e salve a URL + segredo antes de testar.');

            return;
        }

        $eventId = (string) Str::uuid();
        $payload = [
            'event_id' => $eventId,
            'event' => 'test',
            'cnpj' => null,
            'razao_social' => 'Teste Vigilo',
            'detectado_em' => now()->toIso8601String(),
            'mudancas' => [],
        ];

        try {
            $response = (new WebhookSender)->send(
                (string) $organization->webhook_url,
                (string) $organization->webhook_secret,
                $payload,
            );

            $organization->webhookDeliveries()->create([
                'event_id' => $eventId,
                'event_type' => 'test',
                'cnpj' => null,
                'changes_count' => 0,
                'status' => $response->successful() ? 'success' : 'failed',
                'response_status' => $response->status(),
                'error' => $response->successful() ? null : 'HTTP '.$response->status(),
            ]);

            session()->flash('status', $response->successful()
                ? 'Teste enviado com sucesso (HTTP '.$response->status().').'
                : 'O endpoint respondeu HTTP '.$response->status().'.');
        } catch (\Throwable $e) {
            $organization->webhookDeliveries()->create([
                'event_id' => $eventId,
                'event_type' => 'test',
                'cnpj' => null,
                'changes_count' => 0,
                'status' => 'failed',
                'response_status' => null,
                'error' => mb_substr($e->getMessage(), 0, 480),
            ]);

            session()->flash('status', 'Falha ao enviar o teste: '.mb_substr($e->getMessage(), 0, 120));
        }
    }

    public function render(): View
    {
        $organization = $this->currentUser()->organization;

        return view('livewire.integrations.index', [
            'configured' => $organization !== null && $organization->hasWebhook(),
            'deliveries' => $organization === null
                ? collect()
                : $organization->webhookDeliveries()->orderByDesc('created_at')->orderByDesc('id')->limit(10)->get(),
        ]);
    }

    private function currentUser(): User
    {
        /** @var User $user */
        $user = auth()->user();

        return $user;
    }
}
