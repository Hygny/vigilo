<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

/**
 * Emite um token Bearer (Sanctum) para a API OSINT de vigilância, com a ability
 * `vigilancia:osint`. Ao contrário do `vigilo:api-token` (escopado à carteira),
 * este token LÊ A BASE CNPJ INTEIRA — por isso é separado e avisa no uso.
 */
final class IssueOsintToken extends Command
{
    protected $signature = 'vigilo:osint-token
        {email : E-mail do usuário dono do token}
        {--name=osint-n8n : Nome do token, para reconhecê-lo/revogá-lo depois}';

    protected $description = 'Emite um token Bearer da API OSINT de vigilância (lê a base CNPJ inteira).';

    public function handle(): int
    {
        $email = (string) $this->argument('email');
        $user = User::query()->where('email', $email)->first();

        if ($user === null) {
            $this->error("Usuário não encontrado: {$email}");

            return self::FAILURE;
        }

        $name = (string) $this->option('name');
        $token = $user->createToken($name, ['vigilancia:osint']);

        $this->info("Token OSINT '{$name}' emitido para {$user->email}.");
        $this->warn('Este token consulta a base CNPJ INTEIRA (sem escopo de carteira). Trate-o como segredo.');
        $this->warn('Guarde-o agora — ele não será exibido novamente:');
        $this->newLine();
        $this->line($token->plainTextToken);

        return self::SUCCESS;
    }
}
