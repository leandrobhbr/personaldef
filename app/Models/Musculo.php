<?php

namespace App\Models;

use App\Models\Concerns\TemHashid;
use Illuminate\Database\Eloquent\Model;

class Musculo extends Model
{
    use TemHashid;

    protected $table = 'tb_musculo';

    protected $primaryKey = 'mus_id';

    public $timestamps = false;

    protected $guarded = ['mus_id'];

    // nas APIs só sai o hashid de 6 caracteres, nunca o id numérico
    protected $hidden = ['mus_id'];

    protected $appends = ['mus_hashid'];

    public function getMusHashidAttribute(): ?string
    {
        return $this->hashidDaChave();
    }
}
