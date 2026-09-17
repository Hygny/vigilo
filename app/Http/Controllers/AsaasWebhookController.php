<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Billing\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Recebe os webhooks de pagamento do Asaas (rota pública, sem sessão). Valida o
 * token compartilhado antes de processar qualquer coisa e delega a transição de
 * estado ao SubscriptionService. Responde 200 sempre que aceita o evento — o
 * Asaas reenvia enquanto não receber 2xx.
 */
class AsaasWebhookController extends Controller
{
    public function __invoke(Request $request, SubscriptionService $subscriptions): Response
    {
        $expected = (string) config('asaas.webhook_token', '');
        $received = (string) $request->header('asaas-access-token', '');

        // Sem token configurado, ou token divergente: recusa. hash_equals evita
        // vazar o token por timing.
        abort_if($expected === '', 403);
        abort_unless(hash_equals($expected, $received), 403);

        $event = $request->input('event');

        if (is_string($event) && $event !== '') {
            $subscriptions->handleWebhook($event, $request->all());
        }

        return response()->noContent();
    }
}
