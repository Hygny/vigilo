<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

/**
 * Emite um token Bearer (Sanctum) para a API pública de CNPJ. O token herda a
 * organização do usuário informado — é o que escopa o que a API pode devolver.
 */
final class IssueApiToken extends Command
{
    protected $signature = 'vigilo:api-token
        {email : E-mail do usuário dono do token (define a organização/escopo)}
        {--name=integracao : Nome do token, para você reconhecê-lo/revogá-lo depois}';

    protected $description = 'Emite um token Bearer da API de CNPJ, no escopo da organização do usuário.';

    public function handle(): int
    {
        $email = (string) $this->argument('email');
        $user = User::query()->where('email', $email)->first();

        if ($user === null) {
            $this->error("Usuário não encontrado: {$email}");

            return self::FAILURE;
        }

        $name = (string) $this->option('name');
        $token = $user->createToken($name);

        $this->info("Token '{$name}' emitido para {$user->email} (organização #{$user->organization_id}).");
        $this->warn('Guarde-o agora — ele não será exibido novamente:');
        $this->newLine();
        $this->line($token->plainTextToken);

        return self::SUCCESS;
    }
}
