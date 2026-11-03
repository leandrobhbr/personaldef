<?php

namespace App\Models;

use App\Models\Concerns\TemHashid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Aluno extends Model
{
    use TemHashid;

    protected $table = 'tb_aluno';

    protected $primaryKey = 'alu_id';

    public $timestamps = true;

    protected $guarded = ['alu_id'];

    // nas APIs só sai o hashid de 6 caracteres, nunca o id numérico
    protected $hidden = ['alu_id'];

    protected $appends = ['alu_hashid'];

    public function getAluHashidAttribute(): ?string
    {
        return $this->hashidDaChave();
    }

    public function telefones(): HasMany
    {
        return $this->hasMany(AlunoTelefone::class, 'alu_id')->orderByDesc('tel_principal')->orderBy('tel_id');
    }

    public function treinos(): HasMany
    {
        return $this->hasMany(Treino::class, 'alu_id');
    }

    public function acessos(): HasMany
    {
        return $this->hasMany(Acesso::class, 'alu_id');
    }
}
