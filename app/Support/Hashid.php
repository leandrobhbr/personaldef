<?php

namespace App\Support;

use Hashids\Hashids;

/**
 * Ids públicos das APIs (como no 69fit), sempre com 6 caracteres.
 * O salt fica no .env (APP_HASHID_SALT): sem ele, os ids seriam fáceis de adivinhar.
 */
class Hashid
{
    private static ?Hashids $hashids = null;

    private static function hashids(): Hashids
    {
        return self::$hashids ??= new Hashids((string) config('app.hashid_salt'), 6, 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789');
    }

    public static function encode(int $id): string
    {
        return self::hashids()->encode($id);
    }

    /** Id numérico do hashid, ou null se inválido. */
    public static function decode(?string $hashid): ?int
    {
        if (! is_string($hashid) || strlen($hashid) !== 6) {
            return null;
        }
        $ids = self::hashids()->decode($hashid);

        return isset($ids[0]) && count($ids) === 1 ? (int) $ids[0] : null;
    }
}
