<?php

namespace App\Models;

use App\Models\Concerns\TemHashid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Treino extends Model
{
    use TemHashid;

    protected $table = 'tb_treino';

    protected $primaryKey = 'tre_id';

    public $timestamps = false;

    protected $guarded = ['tre_id'];

    // nas APIs só sai o hashid de 6 caracteres, nunca o id numérico
    protected $hidden = ['tre_id', 'alu_id'];

    protected $appends = ['tre_hashid'];

    protected $casts = ['tre_publico' => 'boolean', 'tre_data_cadastro' => 'datetime'];

    public function getTreHashidAttribute(): ?string
    {
        return $this->hashidDaChave();
    }

    public function aluno(): BelongsTo
    {
        return $this->belongsTo(Aluno::class, 'alu_id');
    }

    public function exercicios(): HasMany
    {
        return $this->hasMany(TreinoExercicio::class, 'tre_id')->where('tex_situacao', 'AT')->orderBy('tex_ordem');
    }

    /** Fichas genéricas do Denilson liberadas na área livre do app. */
    public function scopePublicas(Builder $query): Builder
    {
        return $query->whereNull('alu_id')->where('tre_publico', true)->where('tre_situacao', 'AT');
    }
}
