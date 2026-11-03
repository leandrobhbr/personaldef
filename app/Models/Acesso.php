<?php

namespace App\Models;

use App\Models\Concerns\TemHashid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Acesso extends Model
{
    use TemHashid;

    protected $table = 'tb_acesso';

    protected $primaryKey = 'ace_id';

    public $timestamps = true;

    protected $guarded = ['ace_id'];

    // nas APIs só sai o hashid de 6 caracteres, nunca o id numérico
    protected $hidden = ['ace_id', 'alu_id', 'ace_link_hash', 'ace_codigo_hash', 'ace_token_hash'];

    protected $appends = ['ace_hashid'];

    protected $casts = ['ace_link_expira' => 'datetime', 'ace_link_usado_em' => 'datetime', 'ace_ultimo_uso' => 'datetime', 'ace_revogado_em' => 'datetime'];

    public function getAceHashidAttribute(): ?string
    {
        return $this->hashidDaChave();
    }

    public function aluno(): BelongsTo
    {
        return $this->belongsTo(Aluno::class, 'alu_id');
    }

    public function ehPersonal(): bool
    {
        return $this->ace_perfil === 'PER';
    }
}
