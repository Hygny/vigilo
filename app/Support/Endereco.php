<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Regras de "mesmo endereço" entre estabelecimentos da Receita — fonte única do
 * grafo (vizinhos de endereço) e dos indícios de sucessão. CEP + número
 * normalizado identificam o endereço; CEP "especial" (grande usuário, caixa
 * postal, Correios) e número genérico ("S/N", "0") não identificam; complemento
 * diferente nos dois lados = outra sala do mesmo prédio.
 */
final class Endereco
{
    /**
     * CEP de 8 dígitos NNNNN-SSS cujo sufixo (3 últimos) é >= 900: "grande
     * usuário" (edifício/entidade com CEP próprio), caixa postal ou unidade
     * operacional dos Correios. Não é um logradouro que empresas distintas
     * "compartilham" por acaso — é um único endereço-entidade.
     */
    public static function cepEspecial(string $cepDigits): bool
    {
        return strlen($cepDigits) === 8 && (int) substr($cepDigits, 5, 3) >= 900;
    }

    /** Número sem dígitos úteis (vazio, "S/N", "0", "00") — não identifica endereço. */
    public static function numeroInespecifico(string $numeroNormalizado): bool
    {
        $digits = preg_replace('/\D/', '', $numeroNormalizado) ?? '';

        return $digits === '' || ltrim($digits, '0') === '';
    }

    /**
     * Normaliza o complemento (sala/andar) para comparação: translitera acento
     * (TÉRREO → TERREO), caixa alta, só alfanumérico, e colapsa zeros à esquerda
     * dos números (SALA 02 ≡ SALA 2) — para duas grafias da MESMA unidade não
     * serem tratadas como salas diferentes (e derrubarem um vínculo legítimo).
     */
    public static function complemento(?string $value): string
    {
        $alnum = preg_replace('/[^A-Z0-9]/', '', mb_strtoupper(Str::ascii(trim((string) $value)))) ?? '';

        return preg_replace_callback('/\d+/', static fn (array $m): string => ltrim($m[0], '0') ?: '0', $alnum) ?? $alnum;
    }

    /** CEP só com dígitos, ou null se não identifica um endereço (vazio, zeros, especial). */
    public static function cepUtil(?string $cep): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $cep) ?? '';

        return $digits === '' || ltrim($digits, '0') === '' || self::cepEspecial($digits) ? null : $digits;
    }

    /** Mesmo endereço: mesmo CEP útil, mesmo número específico e sem complementos conflitantes. */
    public static function mesmoLocal(?string $cepA, ?string $numA, ?string $complA, ?string $cepB, ?string $numB, ?string $complB): bool
    {
        $cep = self::cepUtil($cepA);
        $numero = AddressNumber::normalize((string) $numA);

        if ($cep === null || self::numeroInespecifico($numero) || $cep !== self::cepUtil($cepB) || $numero !== AddressNumber::normalize((string) $numB)) {
            return false;
        }

        $a = self::complemento($complA);
        $b = self::complemento($complB);

        return $a === '' || $b === '' || $a === $b;
    }
}
