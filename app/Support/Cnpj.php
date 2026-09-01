<?php

declare(strict_types=1);

namespace App\Support;

use App\Support\Exceptions\InvalidCnpjException;
use Stringable;

/**
 * Immutable CNPJ value object.
 *
 * Holds a normalized (14-digit) CNPJ whose check digits have been validated
 * with the mod-11 algorithm. Constructing an instance for an invalid CNPJ is
 * impossible — use {@see self::fromString()} (throws) or {@see self::tryFrom()}
 * (returns null).
 */
final readonly class Cnpj implements Stringable
{
    public const LENGTH = 14;

    private function __construct(public string $value) {}

    /**
     * Build a validated CNPJ, throwing when the input is not a valid CNPJ.
     *
     * @throws InvalidCnpjException
     */
    public static function fromString(string $raw): self
    {
        $normalized = self::normalize($raw);

        if (! self::isValidNormalized($normalized)) {
            throw InvalidCnpjException::for($raw);
        }

        return new self($normalized);
    }

    /**
     * Build a validated CNPJ or return null when the input is invalid.
     */
    public static function tryFrom(string $raw): ?self
    {
        return self::isValid($raw) ? new self(self::normalize($raw)) : null;
    }

    /**
     * Strip every non-digit and left-pad to 14 characters.
     *
     * Left-padding recovers CNPJs whose leading zeros were dropped by
     * spreadsheets (a very common CSV import problem).
     */
    public static function normalize(string $raw): string
    {
        $digits = preg_replace('/\D/', '', $raw) ?? '';

        if ($digits === '' || strlen($digits) > self::LENGTH) {
            return $digits;
        }

        return str_pad($digits, self::LENGTH, '0', STR_PAD_LEFT);
    }

    /**
     * Validate an arbitrary (possibly formatted) CNPJ.
     */
    public static function isValid(string $raw): bool
    {
        return self::isValidNormalized(self::normalize($raw));
    }

    /**
     * Render as the masked form 00.000.000/0000-00.
     */
    public function formatted(): string
    {
        return preg_replace(
            '/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/',
            '$1.$2.$3/$4-$5',
            $this->value
        ) ?? $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private static function isValidNormalized(string $cnpj): bool
    {
        if (strlen($cnpj) !== self::LENGTH || ! ctype_digit($cnpj)) {
            return false;
        }

        // Sequences of a single repeated digit satisfy mod-11 but are never real.
        if (preg_match('/^(\d)\1{13}$/', $cnpj) === 1) {
            return false;
        }

        return $cnpj[12] === self::checkDigit($cnpj, 12)
            && $cnpj[13] === self::checkDigit($cnpj, 13);
    }

    /**
     * Compute the check digit at $position (12 or 13) over the preceding digits.
     */
    private static function checkDigit(string $cnpj, int $position): string
    {
        $sum = 0;
        $weight = 2;

        for ($i = $position - 1; $i >= 0; $i--) {
            $sum += (int) $cnpj[$i] * $weight;
            $weight = $weight === 9 ? 2 : $weight + 1;
        }

        $remainder = $sum % 11;

        return (string) ($remainder < 2 ? 0 : 11 - $remainder);
    }
}
