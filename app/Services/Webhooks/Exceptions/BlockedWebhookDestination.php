<?php

declare(strict_types=1);

namespace App\Services\Webhooks\Exceptions;

use RuntimeException;

/**
 * O destino do webhook foi barrado antes do envio — URL inválida ou host que
 * resolve para um IP privado/reservado (anti-SSRF no momento do envio).
 */
final class BlockedWebhookDestination extends RuntimeException {}
