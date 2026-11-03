<?php

namespace App\Models;

use App\Models\Concerns\TemHashid;
use Illuminate\Database\Eloquent\Model;

class AlunoTelefone extends Model
{
    use TemHashid;

    protected $table = 'tb_aluno_telefone';

    protected $primaryKey = 'tel_id';

    public $timestamps = false;

    protected $guarded = ['tel_id'];

    // nas APIs só sai o hashid de 6 caracteres, nunca o id numérico
    protected $hidden = ['tel_id', 'alu_id'];

    protected $appends = ['tel_hashid'];

    protected $casts = ['tel_principal' => 'boolean'];

    public function getTelHashidAttribute(): ?string
    {
        return $this->hashidDaChave();
    }
}
