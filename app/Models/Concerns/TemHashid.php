<?php

namespace App\Models\Concerns;

use App\Support\Hashid;
use Illuminate\Database\Eloquent\Builder;

/**
 * Busca pelo hashid de 6 caracteres usado nas APIs (o accessor *_hashid fica em cada model, como no 69fit).
 */
trait TemHashid
{
    public function scopeHashid(Builder $query, ?string $hashid): Builder
    {
        return $query->whereKey(Hashid::decode($hashid) ?? 0);
    }

    protected function hashidDaChave(): ?string
    {
        return $this->getKey() ? Hashid::encode((int) $this->getKey()) : null;
    }
}
