<?php

namespace App\Models;

use App\Models\Concerns\TemHashid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exercicio extends Model
{
    use TemHashid;

    protected $table = 'tb_exercicio';

    protected $primaryKey = 'exe_id';

    public $timestamps = false;

    protected $guarded = ['exe_id'];

    // nas APIs só sai o hashid de 6 caracteres, nunca o id numérico
    protected $hidden = ['exe_id', 'mus_id'];

    protected $appends = ['exe_hashid'];

    public function getExeHashidAttribute(): ?string
    {
        return $this->hashidDaChave();
    }

    public function musculo(): BelongsTo
    {
        return $this->belongsTo(Musculo::class, 'mus_id');
    }
}
