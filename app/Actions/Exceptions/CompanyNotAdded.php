<?php

declare(strict_types=1);

namespace App\Actions\Exceptions;

use App\Actions\AddCompanyToPortfolio;
use RuntimeException;

/**
 * O CNPJ não entrou no portfólio ({@see AddCompanyToPortfolio}): já estava lá
 * ou o plano da organização não tem vaga. A mensagem é para o usuário.
 */
final class CompanyNotAdded extends RuntimeException {}
