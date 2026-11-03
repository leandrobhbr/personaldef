<?php

namespace App\Models;

use App\Models\Concerns\TemHashid;
use Illuminate\Database\Eloquent\Model;

class Pausa extends Model
{
    use TemHashid;

    protected $table = 'tb_pausa';

    protected $primaryKey = 'pau_id';

    public $timestamps = false;

    protected $guarded = ['pau_id'];

    // nas APIs só sai o hashid de 6 caracteres, nunca o id numérico
    protected $hidden = ['pau_id'];

    protected $appends = ['pau_hashid'];

    public function getPauHashidAttribute(): ?string
    {
        return $this->hashidDaChave();
    }
}
