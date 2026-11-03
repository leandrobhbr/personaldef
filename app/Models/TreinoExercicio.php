<?php

namespace App\Models;

use App\Models\Concerns\TemHashid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreinoExercicio extends Model
{
    use TemHashid;

    protected $table = 'tb_treino_exercicio';

    protected $primaryKey = 'tex_id';

    public $timestamps = false;

    protected $guarded = ['tex_id'];

    // nas APIs só sai o hashid de 6 caracteres, nunca o id numérico
    protected $hidden = ['tex_id', 'tre_id', 'exe_id', 'mus_id', 'pau_id', 'cad_id', 'met_id'];

    protected $appends = ['tex_hashid'];

    public function getTexHashidAttribute(): ?string
    {
        return $this->hashidDaChave();
    }

    public function treino(): BelongsTo
    {
        return $this->belongsTo(Treino::class, 'tre_id');
    }

    public function exercicio(): BelongsTo
    {
        return $this->belongsTo(Exercicio::class, 'exe_id');
    }

    public function musculo(): BelongsTo
    {
        return $this->belongsTo(Musculo::class, 'mus_id');
    }

    public function pausa(): BelongsTo
    {
        return $this->belongsTo(Pausa::class, 'pau_id');
    }

    public function cadencia(): BelongsTo
    {
        return $this->belongsTo(Cadencia::class, 'cad_id');
    }

    public function metodo(): BelongsTo
    {
        return $this->belongsTo(Metodo::class, 'met_id');
    }
}
